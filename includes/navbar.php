<?php session_start([
  'cookie_path' => '/',
  'cookie_lifetime' => 3600,
  'cookie_secure' => false,
  'cookie_httponly' => true,
  'cookie_samesite' => 'lax',
]); ?>
<nav class="navbar navbar-expand-sm navbar-dark bg-primary fixed-top shadow fs-5">
  <div class="container-fluid">
    <a href="/" class="navbar-brand mb-0 h1 fs-5">
      <img class="d-inline-block align-center" src="static/images/logo.png" width="50" height="50" alt="Logo" />
      Hızlı Bilet
    </a>
    <button type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav"
      aria-expanded="false" aria-label="Navigasyonu Aç" class="navbar-toggler"><span
        class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item active">
          <!-- Add Dynamic Company Listing Here-->
          <a href="/companies.php" class="nav-link">Firmalar</a>
        </li>
        <li class="nav-item">
          <a href="/findtrip.php" class="nav-link">Bilet Al</a>
        </li>
        <li class="nav-item">
          <a href="contact.php" class="nav-link">İletişim</a>
        </li>
        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) { ?>
          <li class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" id="profileDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <img src="static/images/profilelogo.svg" alt="Profile" width="30" height="30" class="rounded-circle" />
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li><a class="dropdown-item" href="profile.php">Profilim</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="addbalance.php">Bakiye Yükle</a></li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="logout.php">Çıkış Yap</a></li>
            </ul>
          </li>
        <?php } else { ?>
          <li class="nav-item">
            <a href="/login.php"
              class="nav-link bg-light text-primary fw-bold px-3 rounded-pill ms-2 shadow-sm">Üye-Girişi/Kayıt Ol</a>
          </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>