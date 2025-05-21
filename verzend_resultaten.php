<?php
require_once 'klassen/db.php';
$db = (new Database())->getConnection();


// Haal onbezonden resultaten ouder dan 1 uur op
$stmt = $db->prepare("
    SELECT r.*, u.email FROM quiz_resultaten r
    JOIN accounts u ON r.user_id = u.id
    WHERE r.is_verzonden = 0 AND r.timestamp <= NOW() - INTERVAL 1 HOUR
");
$stmt->execute();
$resultaten = $stmt->fetchAll();

foreach ($resultaten as $r) {
    $to = $r['email'];
    $subject = "Uw quizresultaten op RoadMate";
    $message = "Beste gebruiker,\n\nUw quizresultaat: {$r['score']} / {$r['totaal']}.\nBedankt voor het deelnemen!";
    $headers = "From: no-reply@roadmate.be";

    if (mail($to, $subject, $message, $headers)) {
        // Markeer als verzonden
        $update = $db->prepare("UPDATE quiz_resultaten SET is_verzonden = 1 WHERE id = ?");
        $update->execute([$r['id']]);
    }
}