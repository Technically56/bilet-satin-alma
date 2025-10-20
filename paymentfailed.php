<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Bus Booking</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
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
                                    class="text-danger" viewBox="0 0 16 16">
                                    <path
                                        d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z" />
                                </svg>
                            </div>
                            <h1 class="display-5 fw-bold text-danger mb-3">Payment Failed</h1>
                            <?php include("includes/flashbox.php") ?>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="/findtrip.php" class="btn btn-primary btn-lg py-3">
                                    Tekrar Deneyin
                                </a>
                                <a href="/buycoins.php" class="btn btn-outline-primary">
                                    Bakiye Yükleyin
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info mt-4" role="alert">
                        <h6 class="alert-heading">Yardıma mı ihtiyacınız var?</h6>
                        <p class="mb-0">
                            Yaşayacağınız sorunlar için lütfen iletişim sayfamızı ziyaret ediniz.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <?php include("includes/footer.php"); ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <?php
        unset($_SESSION['paymentredirect']);
    endif; ?>
</body>

</html>