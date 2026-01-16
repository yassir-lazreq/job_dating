<?php

namespace App\Core;

class Session{
    // Start session if not already started
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Set a session variable
    public static function set(string $key, $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    // Get a session variable
    public static function get(string $key)
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    // Unset a session variable
    public static function unset(string $key): void
    {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    // Destroy the session
    public static function destroy(): void
    {
        self::start();
        session_unset();
        session_destroy();
    }
}