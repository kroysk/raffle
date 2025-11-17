<?php

namespace App\Models;

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
}