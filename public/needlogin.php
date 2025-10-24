<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yapmanız Gerek</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor"
                                class="text-warning" viewBox="0 0 16 16">
                                <path
                                    d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                            </svg>
                        </div>
                        <h1 class="display-6 fw-bold mb-3">Giriş Yapmanız Gerek</h1>
                        <p class="text-muted mb-4">
                            Bu sayfaya erişmek için giriş yapmanız gerek.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="/login.php" class="btn btn-primary btn-lg py-3">
                                Giriş Yap
                            </a>
                            <a href="/" class="btn btn-outline-secondary">
                                Ana Sayfaya Dön
                            </a>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <p class="text-muted">
                        Hesabınız Yok Mu? <a href="/register.php" class="text-primary fw-bold">Kayıt Olun</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>