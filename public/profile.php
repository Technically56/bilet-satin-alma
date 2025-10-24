<?php
session_start(options: [
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);

?>
<!DOCTYPE html>
<html lang="en">

<?php

require_once('includes/db/UserOperations.php');
require_once('includes/db/TicketOperations.php');
require_once('includes/db/db.php');
require_once('includes/idatlas/idatlas.php');
require_once('includes/db/PaymentOperations.php');
require_once('includes/db/TripOperations.php');

$userManager = new UserManager($pdo);
$ticketManager = new TicketManager($pdo);
$tripManager = new TripManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === "user"):
    if ($_SERVER['REQUEST_METHOD'] === 'GET'):
        $user = $userManager->findById($_SESSION['user_id']);
        if (!$user) {
            die('User Not Found');
        }

        $activeTickets = $ticketManager->getUserTickets($user['id'], 'active');
        $canceledTickets = $ticketManager->getUserTickets($user['id'], 'canceled');
        $expiredTickets = $ticketManager->getUserTickets($user['id'], 'expired');
        $allTickets = $ticketManager->getUserTickets($user['id'], '');
        $coupons = $paymentManager->getUserCoupons($user['id']);
        ?>

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Hesabım</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://unpkg.com/htmx.org@1.9.12"></script>
        </head>

        <body class="bg-light">
            <?php include("includes/navbar.php") ?>
            <div class="container my-5">
                <div class="row">
                    <div class="col-lg-4 mb-4">
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0">Hesap Bilgileri</h4>
                            </div>
                            <div class="card-body text-center">
                                <div class="mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor"
                                        class="text-primary" viewBox="0 0 16 16">
                                        <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                        <path fill-rule="evenodd"
                                            d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                                    </svg>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($user['full_name']) ?></h5>
                                <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <div>
                                        <h6 class="text-primary mb-0">
                                            <?php echo htmlspecialchars($userManager->getBalance($user['id'])); ?>
                                        </h6>
                                        <small class="text-muted">TL</small>
                                    </div>
                                    <div class="vr"></div>
                                    <div>
                                        <h6 class="text-primary mb-0">
                                            <?php echo htmlspecialchars($allTickets ? count($allTickets) : 0); ?>
                                        </h6>
                                        <small class="text-muted">Aktif Bilet</small>
                                    </div>
                                </div>
                                <a href="/addcoins.php" class="btn btn-outline-primary btn-sm w-100">
                                    Bakiye Yükle
                                </a>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0">Kullanıcı Bilgilerini Güncelle</h4>
                            </div>
                            <div class="card-body">
                                <div id="updateInfo" hx-post="/api/updateuser.php" hx-target="#updateInfo" hx-swap="innerHTML"
                                    hx-include="#fullname, #email, #currentPassword, #newPassword, #confirmPassword, #csrf_token"
                                    hx-trigger="click from:#applyButton">
                                </div>
                                <form>
                                    <input type="hidden" id="csrf_token" name="csrf_token"
                                        value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                    <div class="mb-3">
                                        <label for="fullname" class="form-label fw-bold">İsim-Soyisim</label>
                                        <input type="text" class="form-control" id="fullname" name="fullName"
                                            value="<?php echo htmlspecialchars($user['full_name']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold">E-posta Adresi</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?php echo htmlspecialchars($user['email']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label fw-bold">Mevcut Şifre</label>
                                        <input type="password" class="form-control" id="currentPassword" name="currentPassword"
                                            placeholder="Mevcut Şifrenizi Giriniz">
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label fw-bold">Yeni Şifre</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword"
                                            placeholder="Yeni Şifrenizi Giriniz">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label fw-bold">Yeni Şifre Tekrar</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                            placeholder="Yeni Şifrenizi Tekrar Giriniz">
                                    </div>
                                    <div class="d-grid">
                                        <button type="button" id="applyButton" class="btn btn-primary">Değişiklikleri
                                            Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div
                                class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                                <h4 class="mb-0">Kuponlarım</h4>
                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#addCouponModal">
                                    Kupon Ekle
                                </button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($coupons)): ?>
                                    <?php foreach ($coupons as $userCoupon):
                                        $coupon = $paymentManager->getCouponById($userCoupon['coupon_id']);
                                        if (!$coupon) {
                                            continue;
                                        } ?>

                                        <div class="card mb-3 border-success">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <h6 class="mb-1 text-success fw-bold">
                                                            <?php echo htmlspecialchars($coupon['code']); ?>
                                                        </h6>
                                                    </div>
                                                    <span class="badge bg-success">Aktif</span>
                                                </div>
                                                <p class="text-muted small mb-0">Son Kulanım Tarihi:
                                                    <?php $couponTime = new DateTime($coupon['expire_date']);
                                                    echo htmlspecialchars($couponTime->format("d-m-Y H:i")); ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="currentColor"
                                            class="text-muted mb-3" viewBox="0 0 16 16">
                                            <path
                                                d="M2.97 1.35A1 1 0 0 1 3.73 1h8.54a1 1 0 0 1 .76.35l2.609 3.044A1.5 1.5 0 0 1 16 5.37v.255a2.375 2.375 0 0 1-4.25 1.458A2.371 2.371 0 0 1 9.875 8 2.37 2.37 0 0 1 8 7.083 2.37 2.37 0 0 1 6.125 8a2.37 2.37 0 0 1-1.875-.917A2.375 2.375 0 0 1 0 5.625V5.37a1.5 1.5 0 0 1 .361-.976l2.61-3.045zm1.78 4.275a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0 1.375 1.375 0 1 0 2.75 0V5.37a.5.5 0 0 0-.12-.325L12.27 2H3.73L1.12 5.045A.5.5 0 0 0 1 5.37v.255a1.375 1.375 0 0 0 2.75 0 .5.5 0 0 1 1 0zM1.5 8.5A.5.5 0 0 1 2 9v6h1v-5a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1v5h6V9a.5.5 0 0 1 1 0v6h.5a.5.5 0 0 1 0 1H.5a.5.5 0 0 1 0-1H1V9a.5.5 0 0 1 .5-.5zM4 15h3v-5H4v5zm5-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-3zm3 0h-2v3h2v-3z" />
                                        </svg>
                                        <p class="text-muted mb-0">Hiç Kupon Yok!</p>
                                        <small class="text-muted">Kupon Eklemek İçin Kupon Ekleme Butonununu Kullanın.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white py-3">
                                <h4 class="mb-0">Biletlerim</h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($activeTickets)): ?>
                                    <?php
                                    $index = 0;
                                    foreach ($activeTickets as $ticket):
                                        $trip = $ticketManager->getTripFromTicket($ticket['id']);
                                        if (!$trip) {
                                            echo "<div class='alert alert-danger text-center py-2' role='alert'>Bu bilet ile ilgili bir sorun oluştu.</div>";
                                            continue;
                                        }
                                        $maskedTicketId = sendToAtlas($ticket['id']);
                                        $seat = $ticketManager->getSeatFromTicket($ticket['id']);
                                        $departureTime = DateTime::createFromFormat("Y-m-d H:i:s", $trip['departure_time']);
                                        $now = new DateTime();
                                        $timeDiff = ($departureTime->getTimestamp() - $now->getTimestamp());
                                        if ($timeDiff < 0) {
                                            $ticketManager->updateTicket($ticket['id'], $_SESSION['user_id'], 'expired');
                                            continue;
                                        }
                                        ?>
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div>
                                                        <h5 class="mb-1">
                                                            <?php echo htmlspecialchars($trip['departure_city']); ?> →
                                                            <?php echo htmlspecialchars($trip['destination_city']); ?>
                                                        </h5>
                                                        <p class="text-muted mb-1">
                                                            <strong>Kalkış Tarihi:</strong>
                                                            <?php echo htmlspecialchars($departureTime->format("d-m-Y H:i")); ?>
                                                        </p>
                                                        <p class="text-muted mb-1">
                                                            <strong>Koltuk:</strong>
                                                            <span
                                                                class="badge bg-primary"><?php echo htmlspecialchars($seat['seat_number']); ?></span>
                                                        </p>
                                                    </div>
                                                    <span class="badge bg-success px-3 py-2">Aktif</span>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="/viewticketpdf.php?id=<?php echo htmlspecialchars($maskedTicketId); ?>"
                                                        class="btn btn-outline-primary btn-sm">
                                                        Bilet PDF'ini Görüntüle
                                                    </a>
                                                    <?php if ($timeDiff > 3600): ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#cancelModal<?php echo $index; ?>">
                                                            Bileti İptal Et
                                                        </button>
                                                    <?php else: ?>
                                                        <small class="text-muted">İptal süresi doldu (kalkışa 1 saatten az
                                                            kaldı)</small>
                                                    <?php endif; ?>
                                                    <?php ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" id="cancelModal<?php echo $index; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Bilet İptali</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Bu bileti iptal etmek istediğinize emin misiniz?</p>
                                                        <div id="replaceDiv<?php echo $index; ?>">
                                                            <div class="alert alert-warning" role="alert">
                                                                <strong>Not:</strong> Ücret iadeniz 24 saat içinde bakiyenize
                                                                yapılacaktır.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri
                                                            Dön</button>
                                                        <input type="hidden" id="ticketInput<?php echo $index; ?>" name="ticket"
                                                            value="<?php echo htmlspecialchars($maskedTicketId); ?>">
                                                        <input type="hidden" id="csrf_token_cancel<?php echo $index; ?>"
                                                            name="csrf_token"
                                                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                                        <button hx-post="/api/cancelticket.php" type="button"
                                                            hx-include="#ticketInput<?php echo $index; ?>, #csrf_token_cancel<?php echo $index; ?>"
                                                            hx-swap="innerHTML" hx-target="#replaceDiv<?php echo $index; ?>"
                                                            class="btn btn-danger">
                                                            Bileti İptal Et
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <?php
                                        $index++;
                                    endforeach;
                                    ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor"
                                            class="text-muted mb-3" viewBox="0 0 16 16">
                                            <path
                                                d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z" />
                                            <path
                                                d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                        </svg>
                                        <h5 class="text-muted">Henüz Bilet Yok</h5>
                                        <p class="text-muted">Seyahat rezervasyonu yapmaya başlayın</p>
                                        <a href="/findtrip.php" class="btn btn-primary">Sefer Bul</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="addCouponModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Kupon Ekle</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="couponResponseDiv"></div>
                            <form>
                                <div class="mb-3">
                                    <label for="couponCode" class="form-label fw-bold">Kupon Kodu</label>
                                    <input type="text" class="form-control" id="couponCode" name="couponCode"
                                        placeholder="Kupon kodunu giriniz" required>
                                </div>
                                <input type="hidden" id="csrf_token_coupon" name="csrf_token"
                                    value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                            <button type="button" class="btn btn-primary" hx-post="/api/addcoupon.php"
                                hx-include="#couponCode, #csrf_token_coupon" hx-target="#couponResponseDiv" hx-trigger="click"
                                hx-swap="innerHTML">
                                Kupon Ekle
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <?php include("includes/footer.php") ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        </body>

        </html>

        <?php
    endif;
else:
    echo "<script>setTimeout(() => location.href = '/needlogin.php', 50)</script>";
endif;
?>