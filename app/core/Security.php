<?php


namespace App\Core;

use App\Core\Database;
use PDOStatement;

class Security
{
    private static $db;

    // Get the PDO database connection
    private static function getDb()
    {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getConnection();
        }
        return self::$db;
    }

    // Sanitize input to prevent XSS
    public static function sanitizeInput(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    // CSRF Token Generation and Validation
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;

        return $token;
    }

    // Validate CSRF Token
    public static function validateCsrfToken(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '')) {
            unset($_SESSION['csrf_token']);
            return true;
        }

        return false;
    }

    // Password Hashing and Verification
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    // Verify Password
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // Prevent Clickjacking
    public static function preventClickjacking(): void
    {
        header('X-Frame-Options: DENY');
    }

    // Secure Query Execution with Prepared Statements
    public static function secureQuery(string $query, array $params = []): PDOStatement|false
    {
        $db = self::getDb();
        $stmt = $db->prepare($query);
        if ($stmt === false) {
            return false;
        }
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        if (!$stmt->execute()) {
            return false;
        }

        return $stmt;
    }
}