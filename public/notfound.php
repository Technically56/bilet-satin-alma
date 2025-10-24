<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Aradığınız Sayfayı Bulamadık</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <h1 class="display-1 fw-bold text-primary">404</h1>
                        </div>
                        <h2 class="fw-bold mb-3">Bu Sayfa Bulunamadı!</h2>
                        <p class="text-muted mb-4">
                            Aradığınız Sayfayı Bulamadık.
                        </p>
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" fill="currentColor"
                                class="text-secondary opacity-50" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                <path
                                    d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z" />
                            </svg>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="/" class="btn btn-primary btn-lg py-3">
                                Ana Sayfaya Dön
                            </a>
                            <button class="btn btn-outline-secondary" onclick="history.back()">
                                Geri Dön
                            </button>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <p class="text-muted">
                        Yardıma mı ihtiyacın var? <a href="/contact.php" class="text-primary fw-bold">Bizimle iletişime
                            geç!</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>

</html>