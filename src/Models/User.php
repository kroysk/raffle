<?php

namespace App\Models;

use App\Core\Model;

Class User extends Model
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function createUser(string $email, string $password, string $nickname): int
    {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        
        return $this->create([
            'email' => $email,
            'password_hash' => $passwordHash,
            'nickname' => $nickname,
        ]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}