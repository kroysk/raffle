<?php

namespace App\Models;

use Psr\Http\Message\ServerRequestInterface as Request;

use App\Core\Model;

Class ShopWiredAccount extends Model
{
    protected string $table = 'shopwired_accounts';

    public function createShopWiredAccount(string $userId, string $name, string $shopWiredApiKey, string $shopWiredApiSecret, string $shopWiredWebhooksSecret): int
    {
        return $this->create([
            'user_id' => $userId,
            'name' => $name,
            'shopwired_api_key' => $shopWiredApiKey,
            'shopwired_api_secret' => $shopWiredApiSecret,
            'shopwired_webhooks_secret' => $shopWiredWebhooksSecret,
        ]);
    }

    public function findAllByUserId(string $userId): array
    {
        return $this->where(['user_id' => $userId]);
    }

    public function verifyWebhookRequest(Request $request): bool
    {
        $body = $request->getBody()->getContents();
        $accountId = $request->getAttribute('accountId');
        $signature = $request->getHeaderLine('X-ShopWired-Signature');
        if (!$body || !$signature) {
            return false;
        }
        $shopWiredWebhooksSecret = $this->find($accountId)['shopwired_webhooks_secret'];
        $expected_signature = hash_hmac('sha256', $body, $shopWiredWebhooksSecret);
        return hash_equals($signature, $expected_signature);
    }
}