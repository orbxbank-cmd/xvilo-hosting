<?php

class Auth
{
    private static ?array $user = null;

    public static function init(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(int $userId): void
    {
        self::init();
        $_SESSION['user_id'] = $userId;
    }

    public static function logout(): void
    {
        self::init();
        unset($_SESSION['user_id']);
        self::$user = null;
    }

    public static function isLoggedIn(): bool
    {
        self::init();
        return !empty($_SESSION['user_id']);
    }

    public static function userId(): ?int
    {
        self::init();
        return $_SESSION['user_id'] ?? null;
    }

    public static function user(): ?array
    {
        self::init();
        $id = self::userId();
        if (!$id) return null;

        if (self::$user === null) {
            $db = Database::getInstance();
            self::$user = $db->fetch("SELECT id, email, name, google_id, created_at FROM users WHERE id = ?", [$id]);
        }
        return self::$user;
    }

    public static function register(string $email, string $password, string $name): ?array
    {
        $db = Database::getInstance();

        $existing = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) return null;

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $id = $db->insert('users', [
            'email' => $email,
            'password' => $hash,
            'name' => $name,
        ]);

        return ['id' => $id, 'email' => $email, 'name' => $name];
    }

    public static function loginByEmail(string $email, string $password): ?int
    {
        $db = Database::getInstance();
        $user = $db->fetch("SELECT id, password FROM users WHERE email = ?", [$email]);
        if (!$user || !$user['password']) return null;

        if (!password_verify($password, $user['password'])) return null;

        return (int)$user['id'];
    }

    public static function findOrCreateByGoogle(string $googleId, string $email, string $name): int
    {
        $db = Database::getInstance();

        $user = $db->fetch("SELECT id FROM users WHERE google_id = ?", [$googleId]);
        if ($user) return (int)$user['id'];

        $user = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        if ($user) {
            $db->update('users', ['google_id' => $googleId], 'id = :id', ['id' => $user['id']]);
            return (int)$user['id'];
        }

        return $db->insert('users', [
            'email' => $email,
            'name' => $name,
            'google_id' => $googleId,
        ]);
    }

    public static function require(): void
    {
        self::init();
        if (!self::isLoggedIn()) {
            header('Location: /auth/login.php');
            exit;
        }
    }
}
