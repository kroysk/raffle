<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Core\Controller;
use App\Models\RaffleEntry;
use App\Models\Raffle;
use App\Models\ShopWiredAccount;
use App\Services\ShopWireService;

Class RaffleEntryController extends Controller {
    private RaffleEntry $raffleEntryModel;
    private Raffle $raffleModel;
    public function __construct()
    {
        $this->raffleEntryModel = new RaffleEntry();
        $this->raffleModel = new Raffle();
    }

    public function findAll(Request $request, Response $response) : Response
    {
        $raffleId = $request->getAttribute('id');
        $userId = $request->getAttribute('user_id');
        if (!$this->raffleModel->belongsToUser($userId, $raffleId)) {
            return $this->error($response, 'Raffle does not belong to user', 403);
        }
        $raffleEntries = $this->raffleEntryModel->findAllByRaffleId($raffleId);
        return $this->success($response, $raffleEntries);
    }

    public function webhook(Request $request, Response $response) : Response
    {
        $shopWiredAccountModel = new ShopWiredAccount();
        $accountId = $request->getAttribute('accountId');
        $shopWiredAccount = $shopWiredAccountModel->find($accountId);
        if (!$shopWiredAccount) {
            return $this->error($response, 'Account not Found', 404);
        }
        if (!$shopWiredAccountModel->verifyWebhookRequest($request)) {
            return $this->error($response, 'Invalid Signature', 401);
        }
       
        
        $data = json_decode($request->getBody(), true);

        if (!empty($data) && !empty($data['verificationToken'])) {
            $signed_verification_token = hash_hmac('sha256', $data['verificationToken'], $shopWiredAccount['shopwired_webhooks_secret']);
            $response->getBody()->write($signed_verification_token);
            return $response->withStatus(200);
        }
        
        $data = $data['event']['data']['object'];
        $productId = $data['products'][0]['id'];
        $productQuantity = $data['products'][0]['quantity'];
        $customerName = $data['billingAddress']['name'];
        $email = $data['billingAddress']['emailAddress'];
        $address = $data['billingAddress']['addressLine1'].$data['billingAddress']['addressLine2'].$data['billingAddress']['addressLine3'];

        $data = [
            'name' => $customerName,
            'email' => $email,
            'address' => $address,
            'quantity' => $productQuantity,
        ];

        // return $this->success($response, $data);
        $raffle = $this->raffleModel->findBy('product_id', $productId);

        // @TODO: Validate signature from webhook with the shopwired account

        $entries = $this->raffleEntryModel->createEntriesRandomSecuence($raffle, $data);

        if ($this->raffleModel->shouldClose($raffle['id'])) {
            $shopWireService = new ShopWireService();
            try {
                $shopWireService->disableProduct($shopWiredAccount, $raffle['product_id']);
            } catch (\Exception $e) {
                return $this->error($response, 'Failed to disable product', 500, $e->getMessage());
            }
        }
        return $this->success($response, $entries);
    }

    public function exportCsv(Request $request, Response $response) : Response
    {
        $raffleId = $request->getAttribute('id');
        $userId = $request->getAttribute('user_id');
        if (!$this->raffleModel->belongsToUser($userId, $raffleId)) {
            return $this->error($response, 'Raffle does not belong to user', 403);
        }
        $raffleEntries = $this->raffleEntryModel->findAllByRaffleId($raffleId);
        
        // Crear archivo CSV temporal
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['Name', 'Email', 'Address', 'Raffle Number']);
        foreach ($raffleEntries as $entry) {
            fputcsv($csv, [$entry['customer_name'], $entry['customer_email'], $entry['customer_address'], $entry['raffle_number']]);
        }
        
        // Leer el contenido del CSV
        rewind($csv);
        $csvContent = stream_get_contents($csv);
        fclose($csv);
        
        // Escribir el contenido al response y devolver con headers apropiados
        $response->getBody()->write($csvContent);
        return $response
            ->withHeader('Content-Type', 'text/csv')
            ->withHeader('Content-Disposition', 'attachment; filename="raffle_entries.csv"');
    }   
}