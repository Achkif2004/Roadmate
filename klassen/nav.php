<?php
require_once __DIR__ . '/../klassen/Auth.php';

use Klassen\Auth;

// Zorg dat er een sessie actief is voor Auth::isLoggedIn()
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
?>
<header>
  <div class="logo">
    <a href="index.php">
      <img src="images/road mate logo.png" alt="RoadMate Logo">
    </a>
  </div>
  <div>
    <?php if (Auth::isLoggedIn()): ?>
      <form action="logout.php" method="post" style="display:inline;">
        <button type="submit" class="account-btn">Uitloggen</button>
      </form>
    <?php else: ?>
      <a href="registreer.php" class="account-btn">Registreren</a>
      <a href="login.php" class="account-btn">Inloggen</a>
    <?php endif; ?>
  </div>
</header>
