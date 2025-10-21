<!DOCTYPE html>
<html lang="en">

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('includes/db/UserOperations.php');
require_once('includes/db/TicketOperations.php');
require_once('includes/db/db.php');
require_once('includes/idatlas/idatlas.php');

$userManager = new UserManager($pdo);
$ticketManager = new TicketManager($pdo);

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

                        <div class="card">
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
                                            placeholder="Enter current password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label fw-bold">Yeni Şifre</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword"
                                            placeholder="Enter new password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label fw-bold">Yeni Şifre Tekrar</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword"
                                            placeholder="Confirm new password">
                                    </div>
                                    <div class="d-grid">
                                        <button type="button" id="applyButton" class="btn btn-primary">Değişiklikleri
                                            Kaydet</button>
                                    </div>
                                </form>
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
                                                        <small class="text-muted">İptal süresi doldu (kalkışa 1 saatten az kaldı)</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cancel Modal -->
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
            <?php include("includes/footer.php") ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        </body>

        </html>

        <?php
    endif;
endif;
?>