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
require_once 'includes/db/TripOperations.php';
require_once 'includes/db/TicketOperations.php';
require_once 'includes/db/PaymentOperations.php';
require_once 'includes/db/CompanyOperations.php';
require_once 'includes/idatlas/idatlas.php';
$userManager = new UserManager($pdo);
$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
$companyManager = new CompanyManager($pdo);

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        $user_id = $_SESSION['user_id'];
        $trip_id = getFromAtlas($_POST['trip_id']);
        $seats = $_POST['seats'];
        $csrf = $_POST['csrf_token'];
        if ($_POST['coupon_id'] === "default") {
            $coupon_id = null;
        } else {
            $coupon_id = getFromAtlas($_POST['coupon_id']);
        }
        if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
            die("CSRF tokeni geçersiz.");
        }
        if (empty($seats)) {
            die("Lütfen en az bir koltuk seçin.");
        }
        echo "hello";
        $result = $paymentManager->buyTicket($trip_id, $seats, $coupon_id);
        echo "here";
        echo $_SESSION['debug'];
        if ($result === "success") {
            $_SESSION['paymentredirect'] = true;
            echo "<script>window.location.href = '/paymentconfirm.php';</script>";
        } else {
            $_SESSION['paymentredirect'] = true;
            $_SESSION['flash_message'] = $result;
            echo "<script>window.location.href = '/paymentfailed.php';</script>";

        }
    }
}
?>

<?php if ($_SERVER['REQUEST_METHOD'] === 'GET'):
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])):
        if (isset($_GET['trip_id']) && isset($_GET['seats']) && isset($_GET['seats'])): ?>
            <?php
            $trip_id = getFromAtlas(filter_input(INPUT_GET, 'trip_id', FILTER_UNSAFE_RAW));
            $seats = $_GET['seats'];
            if (!preg_match('/^[a-f0-9]{8}[-_]?[a-f0-9]{4}[-_]?[a-f0-9]{4}[-_]?[a-f0-9]{4}[-_]?[a-f0-9]{12}$/i', $trip_id)) {
                die('Invalid trip ID');
            }
            if (empty($seats)) {
                die('Lütfen en az bir koltuk seçin.');
            }
            $trip = $tripManager->getTripById($trip_id);
            $company = $companyManager->findById($trip['company_id']);
            $booked_Seats = $tripManager->getBookedSeats($trip_id);
            if (array_intersect($seats, $booked_Seats)) {
                die('Seçilen koltuklardan bazıları zaten rezerve edilmiş. Lütfen başka koltuklar seçin.');
            }
            $user = $userManager->findById($_SESSION['user_id']);
            $userCoupons = $paymentManager->getUserCoupons($_SESSION['user_id']);
            ?>
            <!DOCTYPE html>
            <html lang="en">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Satın Almayı Tamamla</title>
                <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
                <script src="https://unpkg.com/htmx.org@1.9.12"></script>
            </head>

            <body class="bg-light">
                <div class="container my-5">
                    <div class="row">
                        <div class="col-lg-8 mb-4">
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="mb-0">Sefer Özeti</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3 mb-4 pb-4 border-bottom">
                                        <img src="<?php echo htmlspecialchars($company['logo_path']); ?>" alt="Bus" class="rounded"
                                            style="width: 100px; height: 100px;">
                                        <div class="flex-grow-1">
                                            <h5 class="mb-2"><?php echo htmlspecialchars($company['name']); ?></h5>
                                            <p class="text-muted mb-1"><strong>Rota:</strong>
                                                <?php echo htmlspecialchars($trip['departure_city']); ?> →
                                                <?php echo htmlspecialchars($trip['destination_city']); ?>
                                            </p>
                                            <p class="text-muted mb-1"><strong>Saat:</strong>
                                                <?php $formatted_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $trip['departure_time']);
                                                $formatted_end_date = DateTime::createFromFormat('Y-m-d H:i:s', $trip['arrival_time']);
                                                ;
                                                echo htmlspecialchars($formatted_start_date->format("d-m-Y")); ?>
                                            </p>
                                            <p class="text-muted mb-0"><strong>Zaman:</strong>
                                                <?php echo htmlspecialchars($formatted_start_date->format("H:i")) . '-' . htmlspecialchars($formatted_end_date->format("H:i")); ?>
                                            </p>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-success fs-6 px-3 py-2">2+1 Rahat</span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h6 class="mb-3">Seçili Koltuklar</h6>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <?php foreach ($seats as $seat): ?>
                                                <span class="badge bg-primary fs-6 px-3 py-2">Koltuk
                                                    <?php echo htmlspecialchars($seat); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="mb-0">Yolcu Bilgileri</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="firstName" class="form-label fw-bold">Adı</label>
                                            <input type="text" class="form-control form-control-lg" id="firstName" placeholder="<?php $first_name = explode(" ", $user['full_name']);
                                            $last_name = array_pop($first_name);
                                            echo htmlspecialchars(implode(" ", $first_name)); ?>" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="lastName" class="form-label fw-bold">Soyadı</label>
                                            <input type="text" class="form-control form-control-lg" id="lastName"
                                                placeholder="<?php echo htmlspecialchars($last_name); ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">Email Adresi</label>
                                        <input type="email" class="form-control form-control-lg" id="email"
                                            placeholder="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="card sticky-top" style="top: 20px;">
                                <div class="card-header bg-primary text-white py-3">
                                    <h4 class="mb-0">Bilet Özeti</h4>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                                        <span>Bilet Ücreti(x<?php echo htmlspecialchars(count($seats)); ?>)</span>
                                        <span class="fw-bold"><?php $totalTicketPrice = (int) $trip['price'] * count($seats);
                                        echo htmlspecialchars($totalTicketPrice); ?>
                                            TL</span>
                                    </div>
                                    <div id="couponSection" hx-post="/api/applycoupon.php" hx-trigger="load" hx-swap="innerHTML"
                                        hx-include="#trip_id,#seatcount,#couponSelect">
                                    </div>
                                    <input name="seatcount" type="hidden" id="seatcount"
                                        value="<?php echo htmlspecialchars(count($seats)); ?>">
                                    <form action="/checkout.php" method="POST">
                                        <input type="hidden" name="trip_id" id="trip_id"
                                            value="<?php echo htmlspecialchars($_GET['trip_id']); ?>">
                                        <input type="hidden" name="csrf_token" id="csrf_token"
                                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <?php foreach ($seats as $seat): ?>
                                            <input type="hidden" name="seats[]" value="<?= htmlspecialchars($seat) ?>">
                                        <?php endforeach; ?>
                                        <div id="postdiv" class="mb-3" hx-post="/api/applycoupon.php" hx-target="#couponSection"
                                            hx-swap="innerHTML" hx-trigger="change from:#couponSelect"
                                            hx-include="#trip_id,#seatcount, #couponSelect">
                                            <label for="couponSelect" class="form-label fw-bold">Kupon Seç</label>
                                            <select class="form-select form-select-lg" id="couponSelect" name="coupon_id">
                                                <option value="default" selected>Kupon Kullanmadan Devam Et</option>
                                                <?php foreach ($userCoupons as $userCoupon):

                                                    $coupon = $paymentManager->getCouponById($userCoupon['coupon_id']);
                                                    if ($coupon['company_id'] === $trip['company_id']):
                                                        ?>
                                                        <option value="<?php echo htmlspecialchars(sendToAtlas($userCoupon['id'])); ?>">
                                                            <?php echo htmlspecialchars($coupon['code']); ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                            <small class="text-muted d-block mt-2">Kupon kodu seçiniz</small>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <input value="Satın Al" type="submit" class="btn btn-primary btn-lg py-3 fw-bold"
                                                type="button">
                                            </input>
                                    </form>
                                    <a href="/findtrip.php" class="btn btn-outline-secondary" type="button">
                                        Koltuk Seçimine Geri Dön
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
            </body>

            </html>



            <?php
        endif;
    endif;
else:
    echo "<script>setTimeout(() => location.href = '/needlogin.php', 1000)</script>";
endif; ?>