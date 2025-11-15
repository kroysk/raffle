<?php

namespace App\Core;

use App\Config\Database;
use PDO;

abstract class Model {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    protected function findBy(string $column, string $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":value", $value);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $values = array_values($data);
        
        $columnString = implode(', ', $columns);
        $placeholderString = implode(', ', array_map(fn($col) => ":{$col}", $columns));
        
        $sql = "INSERT INTO {$this->table} ({$columnString}) VALUES ({$placeholderString}) RETURNING {$this->primaryKey}";
        $stmt = $this->db->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result[$this->primaryKey];
    }
}