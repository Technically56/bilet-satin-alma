<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Onayı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <?php session_start(options: [
        'cookie_path' => '/',
        'cookie_lifetime' => 3600,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'lax',
    ]);
    if ($_SESSION['paymentredirect'] === true): ?>
        <?php include("includes/navbar.php"); ?>
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-body p-5 text-center">
                            <div class="mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor"
                                    class="text-success" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                </svg>
                            </div>
                            <h1 class="display-5 fw-bold text-success mb-3">Ödeme Başarılı!</h1>
                            <p class="lead text-muted mb-4">
                                Biletiniz oluşturulmuştur. Faturanızı ve bilet detaylarınızı biletlerim kısmından
                                inceleyebilirsiniz.
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="/profile/tickets.php" class="btn btn-primary btn-lg py-3">
                                    Biletlerimi Görüntüle
                                </a>
                                <a href="/" class="btn btn-outline-secondary">
                                    Back to Home
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Information Notice -->
                    <div class="alert alert-info mt-4" role="alert">
                        <h6 class="alert-heading">Important Information</h6>
                        <ul class="mb-0">
                            <li>Lütfen sefer saatinizden 15dk önce biniş durağınızda olunuz.</li>
                            <li>Soru ve sorunlarınız için iletişim sayfamızı ziyaret edin.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>


        <?php include("includes/footer.php"); ?>
        <?php
        unset($_SESSION['paymentredirect']);
    endif; ?>
</body>

</html>