<?php
require_once 'klassen/db.php';
require_once 'klassen/Auth.php';

use Klassen\Auth;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!Auth::isLoggedIn()) {
    http_response_code(403);
    echo 'Niet ingelogd';
    exit;
}

$userId = $_SESSION['gebruiker']['id'];
$score = $_POST['score'] ?? null;
$totaal = $_POST['totaal'] ?? null;

if ($score === null || $totaal === null) {
    http_response_code(400);
    echo 'Ongeldige invoer';
    exit;
}

$db = (new Database())->getConnection();
$stmt = $db->prepare("INSERT INTO quiz_resultaten (user_id, score, totaal, timestamp, is_verzonden) VALUES (?, ?, ?, NOW(), 0)");
$stmt->execute([$userId, $score, $totaal]);

echo 'Opgeslagen';
