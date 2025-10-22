<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Kayıt Ol</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
  <?php include 'includes/navbar.php'; ?>
  <div class="container d-flex justify-content-center mt-5 pt-5">
    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4 bg-white p-4 rounded shadow">
      <h1 class="text-center mb-4">Kayıt Ol</h1>
      <?php include 'includes/flashbox.php'; ?>
      <form action="/register.php" method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">İsim</label>
          <input type="text" id="name" class="form-control" name="fullname" required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label">E-posta Adresi</label>
          <input type="email" id="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Şifre</label>
          <input type="password" id="password" class="form-control" name="password" required>

        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Şifre Tekrar</label>
          <input type="password" id="password" class="form-control" name="confirmpass" required>
        </div>


        <div class="text-center mb-3">
          <button type="submit" class="btn btn-primary w-100">Kayıt Ol</button>
        </div>
        <div class="text-center mt-3">
          <span>Hesabınız var mı? <a href="/login.php">Giriş Yapın</a></span>
      </form>
    </div>
  </div>

  <?php include 'includes/footer.php' ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
    crossorigin="anonymous"></script>

  <?php
  session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
  ]);
  require_once("includes/db/db.php");
  require_once("includes/db/UserOperations.php");
  $userManager = new UserManager(pdo: $pdo);
  if (isset($_SESSION["user_id"])) {
    echo "<script> window.location.href = '/profile.php' </script>";
    exit;
  }
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = $_POST["fullname"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirmpass = $_POST["confirmpass"] ?? '';
    if ($password !== $confirmpass) {
      $_SESSION['flash_message'] = "Şifreler Eşleşmiyor!";
      echo "<script> window.location.href = '/register.php' </script>";
      exit;
    }
    if (!filter_var(value: $email, filter: FILTER_VALIDATE_EMAIL)) {
      $_SESSION['flash_message'] = "Geçersiz E-posta Adresi!";
      echo "<script> window.location.href = '/register.php' </script>";
      exit;
    }
    if ($userManager->findByEmail($email)) {
      $_SESSION['flash_message'] = "Bu E-posta Adresi Zaten Kayıtlı!";
      echo "<script> window.location.href = '/register.php' </script>";
      exit;
    }
    if (strlen($password) < 8) {
      $_SESSION["flash_message"] = "Lütfen Şifrenizi 8 karakterden uzun olacak şekilde giriniz";
      echo "<script> window.location.href = '/register.php' </script>";
      exit;
    }

    if ($userManager->create($fullname, $email, $password, 'user')) {
      $_SESSION['flash_message'] = "Kayıt Başarılı! Giriş Yapabilirsiniz.";
      $_SESSION["alert_type"] = "success";
      echo "<script> window.location.href = '/register.php' </script>";
      exit;
    }

  }


  ?>
</body>

</html>