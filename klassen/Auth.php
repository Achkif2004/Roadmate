<?php
namespace Klassen;

use PDO;

class Auth
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->ensureSession();
    }

    // 1) Zorg dat er altijd een sessie gestart wordt
    private function ensureSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    // 2) Inlog-methode: zet gebruiker in $_SESSION bij succes
    public function login(string $email, string $password): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, voornaam, achternaam, email, password, admin FROM accounts WHERE email = :email'
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['gebruiker'] = [
                'id'        => $user['id'],
                'voornaam'  => $user['voornaam'],
                'achternaam'=> $user['achternaam'],
                'email'     => $user['email'],
                'admin'     => (bool)$user['admin'],
            ];
            return true;
        }
        return false;
    }

    // 3) Registratie blijft zoals je al had (optioneel e-mail/naam toevoegen)
    public function register(string $email, string $password, bool $admin = false): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO accounts (email, password, admin) VALUES (:email, :password, :admin)'
        );
        return $stmt->execute([
            ':email'    => $email,
            ':password' => $hashed,
            ':admin'    => $admin ? 1 : 0,
        ]);
    }

    // 4) Check of er een ingelogde gebruiker is
    public static function isLoggedIn(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        return !empty($_SESSION['gebruiker']);
    }

    // 5) Uitlog-methode: maak sessie schoon en redirect
    public static function logout(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
