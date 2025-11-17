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

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ?: null;
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

    public function all(): array
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result ?: [];
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

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function where(array $conditions): array
    {
        $whereClauses = [];
        $params = [];

        foreach ($conditions as $column => $value) {
            $whereClauses[] = "{$column} = :{$column}";
            $params[$column] = $value;
        }

        $whereString = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereString}";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result ?: [];
    }
}