<?php

namespace App\Models;

use App\Core\Model;

Class RaffleEntry extends Model
{
    protected string $table = 'raffle_entries';

    public function createRaffleEntry(string $raffleId, string $customerName, string $customerEmail, string $customerAddress, int $raffleNumber): int
    {
        return $this->create([
            'raffle_id' => $raffleId,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'customer_address' => $customerAddress,
            'raffle_number' => $raffleNumber,
        ]);
    }

    public function createEntriesRandomSecuence(array $raffle, array $data): int
    {
        // Generate unique random numbers that are not registered
        $sql = "
            WITH available_numbers AS (
                SELECT generate_series(1, :max_entries) AS number
                EXCEPT
                SELECT raffle_number 
                FROM raffle_entries 
                WHERE raffle_id = :raffle_id
            ),
            random_numbers AS (
                SELECT number 
                FROM available_numbers 
                ORDER BY RANDOM() 
                LIMIT :quantity
            )
            INSERT INTO raffle_entries (raffle_id, raffle_number, customer_name, customer_email, customer_address)
            SELECT 
                :raffle_id,
                number,
                :customer_name,
                :customer_email,
                :customer_address
            FROM random_numbers
            RETURNING id
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':max_entries', $raffle['max_entries'], \PDO::PARAM_INT);
        $stmt->bindValue(':raffle_id', $raffle['id']);
        $stmt->bindValue(':quantity', $data['quantity'], \PDO::PARAM_INT);
        $stmt->bindValue(':customer_name', $data['name']);
        $stmt->bindValue(':customer_email', $data['email']);
        $stmt->bindValue(':customer_address', $data['address']);
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    public function findAllByRaffleId(string $raffleId): array
    {
        return $this->where(['raffle_id' => $raffleId]);
    }
}