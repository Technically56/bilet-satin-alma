<?php
session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);
require_once 'includes/db/db.php';
require_once 'includes/db/UserOperations.php';
require_once 'includes/db/TicketOperations.php';
require_once 'includes/db/TripOperations.php';
require_once 'includes/db/PaymentOperations.php';


$userManager = new UserManager($pdo);
$ticketManager = new TicketManager($pdo);
$tripManager = new TripManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);

if ($_SERVER['REQUEST_METHOD'] === 'GET'):
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])):
        $user = $userManager->findById($_SESSION['user_id']);
        if ($user !== null):
            ?>

            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Bakiye Yükle</title>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
            </head>
            <?php include("includes/navbar.php"); ?>

            <body class="bg-light">
                <div class="container my-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-body text-center py-4">
                                    <h5 class="text-muted mb-2">Şu Anki Bakiyeniz</h5>
                                    <h1 class="display-3 fw-bold text-primary mb-3">
                                        <?php echo htmlspecialchars($user['balance']) . " TL"; ?>
                                    </h1>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="mb-0">Bakiye Miktarı Seçin</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="coinPackage" id="package1" value="100"
                                                autocomplete="off">
                                            <label class="btn btn-outline-primary w-100 p-4 text-start" for="package1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-end">
                                                        <h4 class="mb-0">100</h4>
                                                        <small class="text-muted">TL</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="coinPackage" id="package2" value="250"
                                                autocomplete="off">
                                            <label class="btn btn-outline-primary w-100 p-4 text-start" for="package2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-end">
                                                        <h4 class="mb-0">250</h4>
                                                        <small class="text-muted">TL</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="coinPackage" id="package3" value="500"
                                                autocomplete="off">
                                            <label class="btn btn-outline-primary w-100 p-4 text-start" for="package3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-end">
                                                        <h4 class="mb-0">500</h4>
                                                        <small class="text-muted">TL</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" class="btn-check" name="coinPackage" id="package4" value="1000"
                                                autocomplete="off">
                                            <label class="btn btn-outline-primary w-100 p-4 text-start" for="package4">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div class="text-end">
                                                        <h4 class="mb-0">1000</h4>
                                                        <small class="text-muted">TL</small>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="mb-0">Ödeme Bilgiler</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info mb-4" role="alert">
                                        <strong>Sahte Ödeme Sistemi</strong> - Site Testi Amacıyla, bakiye yükelemek için sadece
                                        matematiksel olarak geçerli bir kredi kartı numarası girin.
                                    </div>

                                    <form action="/addbalance.php" method="POST" id="paymentForm">
                                        <div class="mb-3">
                                            <label for="mockCardNumber" class="form-label fw-bold">Kart Numarası</label>
                                            <input type="text" class="form-control form-control-lg" id="mockCardNumber"
                                                name="mockCardNumber" placeholder="Matematiksel Olarak Geçerli Bir Kart Girin"
                                                required>
                                            <small class="text-muted">Geçerli bazı kartlar: 4111111111111111, 5500000000000004,
                                                340000000000009</small>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="expiry" class="form-label fw-bold">Son Kullanım Tarihi</label>
                                                <input type="text" class="form-control form-control-lg" id="expiry"
                                                    placeholder="MM/YY" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="cvv" class="form-label fw-bold">CVV</label>
                                                <input type="text" class="form-control form-control-lg" id="cvv" placeholder="123"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label for="cardName" class="form-label fw-bold">Kart Sahibinin İsmi</label>
                                            <input type="text" class="form-control form-control-lg" id="cardName"
                                                placeholder="Ali Velioğlu" required>
                                        </div>
                                        <input type="hidden" name="package" id="packageInput" value="">

                                        <div class="d-grid gap-2">
                                            <button class="btn btn-success btn-lg py-3 fw-bold" type="submit">
                                                Satın Al
                                            </button>
                                            <a href="/" class="btn btn-outline-secondary" type="button">
                                                Geri Dön
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include("includes/footer.php"); ?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
                <script>
                    document.querySelectorAll('input[name="coinPackage"]').forEach(radio => {
                        radio.addEventListener('change', function () {
                            document.getElementById('packageInput').value = this.value;
                        });
                    });
                    document.getElementById('paymentForm').addEventListener('submit', function (e) {
                        const selectedPackage = document.querySelector('input[name="coinPackage"]:checked');
                        if (!selectedPackage) {
                            e.preventDefault();
                            alert('Lütfen bir paket seçin');
                            return false;
                        }
                    });
                </script>
            </body>

            </html>
        <?php endif;
    endif;


endif;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mockCardNumber']) && isset($_POST['package'])) {
        $user = $userManager->findById($_SESSION['user_id']);

        if ($user !== null) {
            echo $paymentManager->addFunds($user['id'], (int) $_POST['package'], $_POST['mockCardNumber']);
            echo "<script>setTimeout(() => location.href = '/addbalance.php', 50)</script>";
        } else {
            die("Geçersiz Kullanıcı");
        }
    }
}


?>