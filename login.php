<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
  <div class="container d-flex justify-content-center">
    <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-4 bg-white p-4 rounded shadow">
      <h1 class="text-center mb-4">Giriş Yap</h1>
      <?php if(isset($_GET['error'])){
        echo "<div class='alert alert-danger text-center py-2' role='alert'>Giriş Hatası, Lütfen Şifrenizi Ve Email Adresinizi Kontrol Edin.</div>";
      }?>
      <form action="/login.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">E-posta Adresi</label>
          <input type="email" id="email" class="form-control" name="email" required>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Şifre</label>
          <input type="password" id="password" class="form-control" name="password" required>
        </div>

        <div class="text-center mb-3">
          <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
        </div>

        <div class="text-center">
          <a href="#!">Şifrenizi mi unuttunuz?</a>
        </div>
      </form>
    </div>
  </div>
<?php 
session_start(options: [ 
        'cookie_path' => '/',
        'cookie_lifetime' => 3600,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'lax',
    ]);
require_once 'includes/db.php';

if(isset($_SESSION["user_id"])){
    echo "<script> window.location.href = '/profile.php' </script>";
    exit;
}
if($_SERVER["REQUEST_METHOD"] === 'POST'){
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $statement = $pdo->prepare(query: 'SELECT role,password,id FROM User WHERE email = :mail');
    $statement->execute(params: [':mail'=>$email]);
    $user = $statement->fetch(mode: PDO::FETCH_ASSOC);
    if($user){
        if(password_verify(password: $password, hash: $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            header(header: "Location: /tickets.php");
            exit;
        }
        else {
            echo "<script> window.location.href = '/login.php?error=true' </script>";
            exit;
        }
    }
    else{
        echo "<script> window.location.href = '/login.php?error=true' </script>";
        exit;
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>