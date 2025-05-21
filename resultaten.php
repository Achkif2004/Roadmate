<?php
require_once 'klassen/db.php';
require_once 'klassen/Auth.php';

use Klassen\Auth;

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!Auth::isLoggedIn()) header('Location: login.php');

$db = (new Database())->getConnection();
$userId = $_SESSION['gebruiker']['id'];

$stmt = $db->prepare("SELECT score, totaal, timestamp FROM quiz_resultaten WHERE user_id = ? ORDER BY timestamp DESC");
$stmt->execute([$userId]);
$resultaten = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <title>Mijn Quizresultaten | RoadMate</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f6f9fc;
      color: #2c3e50;
    }

    .container {
      max-width: 1100px;
      margin: 3rem auto;
      padding: 0 1.5rem;
    }

    h1 {
      font-size: 2rem;
      color: #2c3e50;
      margin-bottom: 2rem;
      text-align: center;
    }

    .result-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 1.5rem;
    }

    .result-card {
      background: white;
      border-radius: 20px;
      padding: 1.8rem;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
      transition: transform 0.2s ease;
    }

    .result-card:hover {
      transform: translateY(-6px);
    }

    .result-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 0.4rem;
      color: #136AEC;
    }

    .result-score {
      font-size: 1.6rem;
      font-weight: bold;
      color: #f1c40f;
      margin-bottom: 0.3rem;
    }

    .result-date {
      font-size: 0.95rem;
      color: #7f8c8d;
    }

    @media (max-width: 600px) {
      .container {
        padding: 0 1rem;
      }
      h1 {
        font-size: 1.6rem;
      }
    }
  </style>
</head>
<body>


<?php include 'klassen/nav.php'; ?>
<div class="container">
  


  <?php if (count($resultaten) === 0): ?>
    <p style="text-align:center; color: #888;">Er zijn nog geen resultaten beschikbaar.</p>
  <?php else: ?>
    <div class="result-grid">
      <?php foreach ($resultaten as $r): ?>
        <div class="result-card">
          <div class="result-title">Quiz <?= date('d-m-Y', strtotime($r['timestamp'])) ?></div>
          <div class="result-score"><?= $r['score'] ?> / <?= $r['totaal'] ?></div>
          <div class="result-date">Ingezonden op <?= date('d-m-Y H:i', strtotime($r['timestamp'])) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
