<?php session_start();?>
<nav class="navbar navbar-expand-sm navbar-dark bg-primary shadow fs-5">
  <div class="container-fluid">
    <a href="/" class="navbar-brand mb-0 h1 fs-5">
      <img 
        class="d-inline-block align-center"
        src="static/images/logo.png"
        width="50" height="50"
        alt="Logo"
      />
      Hızlı Bilet
    </a>
    <button type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Navigasyonu Aç" class="navbar-toggler"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item active">
<!-- Add Dynamic Company Listing Here-->
          <a href="#" class="nav-link">Firmalar</a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">Bilet Al</a>
        </li>
        <li class="nav-item">
          <a href="#" class="nav-link">İletişim</a>
        </li>
        <?php if(isset($_SESSION['user_id']) && isset($_SESSION['user_role'])){ ?>
        <li class="nav-item">
          <a href="profile.php" class="nav-link">
            <img 
              src="static/images/profilelogo.svg"
              alt="Profile"
              width="30" height="30"
              class="rounded-circle"
            />
          </a>
        </li>
        <?php } else { ?>
        <li class="nav-item">
          <a href="/login.php" class="nav-link bg-light text-primary fw-bold px-3 rounded-pill ms-2 shadow-sm">Üye-Girişi/Kayıt Ol</a>
        </li>
        <?php } ?>
      </ul>
    </div>
  </div>
</nav>
