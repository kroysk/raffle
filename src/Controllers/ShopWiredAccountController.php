<?php
namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\ShopWiredAccount;

Class ShopWiredAccountController extends Controller {
    private ShopWiredAccount $shopWiredAccountModel;
    public function __construct()
    {
        $this->shopWiredAccountModel = new ShopWiredAccount();
    }

    public function create(Request $request, Response $response) : Response
    {
        $userId = $request->getAttribute('user_id');
        $data = json_decode($request->getBody(), true);
        $errors = $this->validate($data, [
            'name' => 'required|string|max:255',
            'shopwired_api_key' => 'required|string|max:255',
            'shopwired_api_secret' => 'required|string|max:255',
            'shopwired_webhooks_secret' => 'required|string|max:255',
        ]);

        if (!empty($errors)) {
            return $this->error($response, 'Validation failed', 422, $errors);
        }

        $shopWiredAccount = $this->shopWiredAccountModel->createShopWiredAccount(
            $userId,
            $data['name'],
            $data['shopwired_api_key'],
            $data['shopwired_api_secret'],
            $data['shopwired_webhooks_secret']
        );

        return $this->success($response, $shopWiredAccount);
    }

    public function findAll(Request $request, Response $response) : Response
    {
        $userId = $request->getAttribute('user_id');
        $shopWiredAccounts = $this->shopWiredAccountModel->findAllByUserId($userId);
        return $this->success($response, $shopWiredAccounts);
    }
    
    public function delete(Request $request, Response $response) : Response
    {
        $userId = $request->getAttribute('user_id');
        $id = $request->getAttribute('id');
        $this->shopWiredAccountModel->delete($id);
        return $this->success($response, null,'ShopWired account deleted successfully');
    }
}