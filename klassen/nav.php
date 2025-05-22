<?php
require_once __DIR__ . '/../klassen/Auth.php';
use Klassen\Auth;

// if (session_status() !== PHP_SESSION_ACTIVE) {
//     session_start();
// }
?>
<style>
  .main-header {
    background-color: #2c3e50;
    color: white;
    padding: 0.7rem 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .logo img {
    width: 200px;
    height: auto;
  }

  .nav-account-container {
    position: relative;
  }

  .account-icon {
    width: 32px;
    height: 32px;
    cursor: pointer;
  }

  .dropdown-menu {
    position: absolute;
    right: 0;
    top: 110%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 10px;
    z-index: 11000;
    min-width: 140px;
  }

  .dropdown-btn {
    display: block;
    padding: 8px 12px;
    text-decoration: none;
    color: #2c3e50;
    font-weight: 500;
    border-radius: 4px;
    transition: background 0.2s;
    cursor: pointer;
  }

  .dropdown-btn:hover {
    background: #f1f1f1;
  }

</style>
<header class="main-header">
  <div class="logo">
    <a href="index.php">
      <img src="images/road mate logo.png" alt="RoadMate Logo">
    </a>
  </div>

  <div class="nav-account-container">
    <img src="images/user.png" alt="Account" id="account-icon" class="account-icon" />

    <div id="account-dropdown" class="dropdown-menu" style="display:none;">
      <?php if (!Auth::isLoggedIn()): ?>
        <a href="registreer.php" class="dropdown-btn">Registreren</a>
        <a href="login.php" class="dropdown-btn">Inloggen</a>
        <a href="resultaten.php" class="dropdown-btn">Mijn resultaten</a>
      <?php else: ?>
        <a class="dropdown-btn" onclick="document.getElementById('logout-form').submit();">Uitloggen</a>
        <a href="resultaten.php" class="dropdown-btn">Mijn resultaten</a>
        <form id="logout-form" action="logout.php" method="post" style="display:none;"></form>
      <?php endif; ?>
    </div>
  </div>

  <script>
    const icon = document.getElementById('account-icon');
    const dropdown = document.getElementById('account-dropdown');
    let dropdownOpen = false;

    icon.addEventListener('click', () => {
      dropdownOpen = !dropdownOpen;
      dropdown.style.display = dropdownOpen ? 'block' : 'none';
    });

    document.addEventListener('click', (e) => {
      if (!icon.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
        dropdownOpen = false;
      }
    });
  </script>
</header>
