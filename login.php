<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
<div class="container">
    <h1 class="text-center my-4">Login</h1>
<form action="/login.php" method="POST">
  <div data-mdb-input-init class="form-outline mb-4">
    <input type="email" id="email" class="form-control" name="email"/>
    <label class="form-label" for="email">Email address</label>
  </div>

  <div data-mdb-input-init class="form-outline mb-4">
    <input type="password" id="password" class="form-control" name="password"/>
    <label class="form-label" for="email">Password</label>
  </div>

    <div class="col">
      <!-- Simple link -->
      <a href="#!">Forgot password?</a>
    </div>
  </div>

  <!-- Submit button -->
  <button  type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4 align-center">Sign in</button>

  <!-- Register buttons -->
  <div class="text-center">
    <p>Not a member? <a href="#!">Register</a></p>
    <p>or sign up with:</p>
  </div>
</form>
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