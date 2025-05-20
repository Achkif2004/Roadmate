<?php
require_once 'klassen/db.php';
require_once 'klassen/Auth.php';

use Klassen\Auth;

$database = new Database();
$pdo      = $database->getConnection();
$auth     = new Auth($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($auth->login($_POST['email'], $_POST['password'])) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Ongeldige gebruikersnaam of wachtwoord.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>RoadMate Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      height: 100vh;
      background-color: #f0f4f8;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-container {
      background: white;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
      width: 350px;
    }

    h2 {
      margin-bottom: 20px;
      color: #2c3e50;
      font-size: 2rem;
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 12px 15px;
      margin: 10px 0;
      border: 2px solid #ccc;
      border-radius: 10px;
      font-size: 1rem;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: #f1c40f;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      margin-top: 20px;
      background-color: #f1c40f;
      border: none;
      border-radius: 10px;
      font-size: 1rem;
      font-weight: bold;
      cursor: pointer;
      color: #2c3e50;
      transition: background-color 0.3s;
    }

    button:hover {
      background-color: #e0b400;
    }

    .extra {
      margin-top: 15px;
      font-size: 0.9rem;
      color: #34495e;
    }

    .extra a {
      color: #2c3e50;
      text-decoration: underline;
      cursor: pointer;
    }

  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login bij RoadMate</h2>
    <form action="login.php" method="POST">
      <input type="text" name="email" placeholder="E-mail" required />
      <input type="password" name="password" placeholder="Wachtwoord" required />
      <button type="submit">Inloggen</button>
    </form>
    <div class="extra">
        Nog geen account? <a href="registreer.php">Registreer</a>
    </div>
  </div>
</body>
</html>
