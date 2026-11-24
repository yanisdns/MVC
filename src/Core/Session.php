<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => !empty($_SERVER['HTTPS']),
            ]);
            session_start();
        }
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        self::refresh($user);
    }

    public static function refresh(array $user): void
    {
        $_SESSION['user'] = [
            'id'     => (int) $user['id'],
            'pseudo' => $user['pseudo'],
            'email'  => $user['email'],
            'role'   => $user['role'],
        ];
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function isLogged(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? '') === 'ADMIN';
    }

    public static function canAccessAdmin(): bool
    {
        return in_array(self::user()['role'] ?? '', ['ADMIN', 'AUTHOR'], true);
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function pullFlashes(): array
    {
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }
}
