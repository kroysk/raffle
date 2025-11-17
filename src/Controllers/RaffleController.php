<?php
namespace App\Controllers;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Core\Controller;
use App\Models\Raffle;

Class RaffleController extends Controller {
    private Raffle $raffleModel;
    public function __construct()
    {
        $this->raffleModel = new Raffle();
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
        try {
        $raffle = $this->raffleModel->createRaffle(
            $userId,
            $data['shopwired_account_id'],
                $data['title'],
                $data['max_entries']
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
}