<?php
namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Core\Controller;
use App\Models\Raffle;
use App\Models\ShopWiredAccount;
use App\Models\RaffleEntry;
use App\Services\ShopWireService;

Class RaffleController extends Controller {
    private Raffle $raffleModel;
    private ShopWiredAccount $shopWiredAccountModel;
    public function __construct()
    {
        $this->raffleModel = new Raffle();
        $this->shopWiredAccountModel = new ShopWiredAccount();
    }

    public function create(Request $request, Response $response) : Response
    {
        $userId = $request->getAttribute('user_id');
        $data = json_decode($request->getBody(), true);
        $errors = $this->validate($data, [
            'shopwired_account_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'max_entries' => 'required|int',
        ]);
        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 422, $errors);
        }

        $shopWiredAccount = $this->shopWiredAccountModel->find($data['shopwired_account_id']);
        if (!$shopWiredAccount) {
            return $this->error($response, 'ShopWired account not found', 404);
        }
        if ($shopWiredAccount['user_id'] !== $userId) {
            return $this->error($response, 'ShopWired account does not belong to user', 403);
        }
        $shopWireService = new ShopWireService();
        try {
            $product = $shopWireService->createProduct($shopWiredAccount, [
                'title' => $data['title'],
            ]);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to create product', 500, $e->getMessage());
        }
        
        try {
            $raffle = $this->raffleModel->createRaffle(
                $userId,
                $data['shopwired_account_id'],
                $data['title'],
                $data['max_entries'],
                $product['body']['id'],
            );
            return $this->success($response, $raffle);
        } catch (\Exception $e) {
            return $this->error($response, 'Failed to create raffle', 500, $e->getMessage());
        }
    }

    public function findAll(Request $request, Response $response) : Response
    {
        $userId = $request->getAttribute('user_id');
        $raffles = $this->raffleModel->findAllByUserId($userId);
        return $this->success($response, $raffles);
    }

    public function find(Request $request, Response $response) : Response
    {
        $userId = $request->getAttribute('user_id');
        $raffleId = $request->getAttribute('id');
        $raffle = $this->raffleModel->find($raffleId);
        if (!$raffle) {
            return $this->error($response, 'Raffle not found', 404);
        }
        if (!$this->raffleModel->belongsToUser($userId, $raffleId)) {
            return $this->error($response, 'Raffle does not belong to user', 403);
        }
        $raffleEntryModel = new RaffleEntry();
        $raffle['total_entries'] = $raffleEntryModel->count(['raffle_id' => $raffleId]);
        return $this->success($response, $raffle);
    }
}