<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Ana Sayfa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php session_start(options: [
        'cookie_path' => '/',
        'cookie_lifetime' => 3600,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'lax',
    ]);
    ?>
    <?php include 'includes/navbar.php'; ?>
    <div class="bg-primary text-white position-relative"
        style="background: linear-gradient(rgba(13, 110, 253, 0.5), rgba(13, 110, 253, 0.5)), url('https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=1920') center/cover; min-height: 100vh;">
        <div class="container">
            <div class="row min-vh-100 align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-3 fw-bold mb-4">Hızlı Bilet™ İle Yolculuğunuzu Planlayın</h1>
                    <p class="lead mb-4">
                        Hızlı ve kolay bir şekilde yolculuğunuzu planlayın.
                    </p>
                    <a href="/findtrip.php" class="btn btn-light btn-lg px-5 py-3">
                        Sefer Arayın
                    </a>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-light py-5">
        <div class="container">
            <h2 class="text-center mb-5">Neden Bizi Seçmelisiniz?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                                    class="text-primary" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                    <path
                                        d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z" />
                                </svg>
                            </div>
                            <h5 class="card-title">Hızlı</h5>
                            <p class="card-text text-muted">
                                Birkaç tıkla yolculuğunuzu planlayın.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                                    class="text-primary" viewBox="0 0 16 16">
                                    <path
                                        d="M1 11a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3zm5-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2z" />
                                </svg>
                            </div>
                            <h5 class="card-title">En İyi Fiyatlar</h5>
                            <p class="card-text text-muted">
                                Rekabetçi fiyat politikamız ve cömert kupon altyapımız ile en ucuz yolculuklar sizin
                                olsun!
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor"
                                    class="text-primary" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z" />
                                    <path
                                        d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319z" />
                                </svg>
                            </div>
                            <h5 class="card-title">24/7 Destek</h5>
                            <p class="card-text text-muted">
                                Müşteri hizmetleri ekibimiz istediğiniz her konuda size yardımcı olacaktır.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="mb-4">Yeni bir yolculuğa çıkmaya hazır mısınız?</h2>
            <p class="lead mb-4">Hızlıca biletinizi alın!</p>
            <a href="/findtrip.php" class="btn btn-light btn-lg px-5 py-3">
                Sefer Arayın
            </a>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

</body>

</html>