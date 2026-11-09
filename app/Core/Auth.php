<?php
namespace App\Core;

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login(array $user): void
    {
        self::startSession();
        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['phonenumber'] = $user['phonenumber'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['house_id'] = $user['house_id'] ?? null;
        $_SESSION['logged_in_at'] = time();
    }

    public static function logout(): void
    {
        self::startSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function check(): bool
    {
        self::startSession();
        return isset($_SESSION['user_id']);
    }

    public static function user(): ?array
    {
        self::startSession();
        if (!self::check()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'role' => $_SESSION['role'],
            'firstname' => $_SESSION['firstname'],
            'lastname' => $_SESSION['lastname'],
            'fullname' => $_SESSION['firstname'] . ' ' . $_SESSION['lastname'],
            'phonenumber' => $_SESSION['phonenumber'],
            'email' => $_SESSION['email'],
            'house_id' => $_SESSION['house_id'],
        ];
    }

    public static function isAdmin(): bool
    {
        self::startSession();
        return self::check() && ($_SESSION['role'] === 'ADMIN');
    }

    public static function isTenant(): bool
    {
        self::startSession();
        return self::check() && ($_SESSION['role'] === 'TENANT');
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            header('Location: /login?error=' . urlencode('Admin access required'));
            exit;
        }
    }

    public static function requireTenant(): void
    {
        if (!self::isTenant()) {
            header('Location: /login?error=' . urlencode('Tenant access required'));
            exit;
        }
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
