<?php

namespace App\Core;

use DateTime;
use App\Core\Security;
use App\Core\validator;


class userModel extends BaseModel
{
    public $table = 'users';
    public $primaryKey = 'id';
    public $fields = ['id', 'name', 'email', 'password', 'created_at', 'updated_at'];

    private Validator $validator;
    private Database $db;
    protected Security $security;

    public function __construct()
    {
        parent::__construct();

        // Initialize Validator, Database, and Security instances
        $this->validator = new Validator();
        $this->db = Database::getInstance();
        $this->security = new Security();
    }

    //getes for private attributes
    public function getValidator(): Validator
    {
        return $this->validator;
    }

    public function getDb(): Database
    {
        return $this->db;
    }

    public function getSecurity(): Security
    {
        return $this->security;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    // seters for private attributes
    public function setValidator(Validator $validator): void
    {
        $this->validator = $validator;
    }

    public function setDb(Database $db): void
    {
        $this->db = $db;
    }

    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }


    // Get user by email
    public function getUserByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1";
        $stmt = $this->security->secureQuery($sql, ['email' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    // Create new user
    public function createUser(array $data, array $rules)
    {
        if (!$this->validator->validate($data, $rules)) {
            return false;
        }
        if (isset($data['password'])) {
            $data['password'] = $this->security->hashPassword($data['password']);
        }
        
            $data['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
            $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
    
            return $this->create($data);
    }

    // Update existing user
    public function updateUser(int $id, array $data, array $rules)
    {
        if (!$this->validator->validate($data, $rules)) {
            return false;
        }

        if (isset($data['password'])) {
            $data['password'] = $this->security->hashPassword($data['password']);
        }

        $data['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');

        return $this->update($id, $data);
    }

    // Verify user password
    public function verifyPassword(string $email, string $password): bool
    {
        $user = $this->getUserByEmail($email);
        if ($user) {
            return $this->security->verifyPassword($password, $user['password']);
        }
        return false;
    }

    // Show all users
    public function showAllUsers(): array
    {
        return $this->all();
    }

    // Delete user by ID
    public function deleteUser(int $id): bool
    {
        return $this->delete($id);
    }


}