<?php

namespace App\Core;

use App\Core\Database;
use App\Core\Security;

abstract class BaseModel
{
    protected $pdo;
    protected $table; // Doit être défini dans la classe child
    protected $primaryKey = 'id';
    protected $security;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
        $this->security = new Security();
    }

    // Opérations CRUD
    public function find($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        $stmt = $this->security->secureQuery($sql, ['id' => $id]);
        return $stmt->fetch();
    }

    public function all()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->security->secureQuery($sql);
        return $stmt->fetchAll();
    }

    // Insert a new record @param array $data Associative array of column => value
    public function create(array $data)
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->security->secureQuery($sql, $data);
        return $this->pdo->lastInsertId();
    }

    public function update($id, array $data)
    {
        $fields = "";
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        
        $fields = rtrim($fields, ", ");

        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :id";

        $data['id'] = $id;

        $stmt = $this->security->secureQuery($sql, $data);
        return $stmt !== false;
    }

    public function delete($id)
    {
        $stmt = $this->security->secureQuery("DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id", ['id' => $id]);
        return $stmt !== false;
    }

    // Custom Query Helper    
    public function where($column, $value)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = :val";
        $stmt = $this->security->secureQuery($sql, ['val' => $value]);
        return $stmt->fetchAll();
    }
}