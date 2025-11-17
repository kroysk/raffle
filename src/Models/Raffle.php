<?php

namespace App\Models;

use App\Core\Model;
use App\Models\RaffleEntry;

Class Raffle extends Model
{
    protected string $table = 'raffles';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_ACTIVE = 'active';
    public function createRaffle(string $userId, string $accountId, string $title, int $maxEntries, string $productId): int
    {
        return $this->create([
            'user_id' => $userId,
            'shopwired_account_id' => $accountId,
            'title' => $title,
            'max_entries' => $maxEntries,
            'status' => self::STATUS_ACTIVE,
            'product_id' => $productId,
        ]);
    }

    public function findAllByUserId(string $userId): array
    {
        return $this->where(['user_id' => $userId]);
    }

    public function belongsToUser(int $userId, int $raffleId): bool
    {
        $raffle = $this->find($raffleId);
        return $raffle && $raffle['user_id'] === $userId;
    }

    public function shouldClose(int $raffleId): bool
    {
        $raffleEntryModel = new RaffleEntry();
        $totalEntries = $raffleEntryModel->count(['raffle_id' => $raffleId]);
        $raffle = $this->find($raffleId);
        return $totalEntries >= $raffle['max_entries'];
    }
}
