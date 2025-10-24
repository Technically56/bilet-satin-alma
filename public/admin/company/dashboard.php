<?php
session_start(options: [
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);

require_once('../../includes/db/TripOperations.php');
require_once('../../includes/idatlas/idatlas.php');
require_once('../../includes/db/UserOperations.php');
require_once('../../includes/db/TicketOperations.php');
require_once('../../includes/db/PaymentOperations.php');
require_once('../../includes/db/db.php');
require_once('../../includes/admintripbox.php');
require_once('../../includes/db/CompanyOperations.php');
require_once("../../includes/couponbox.php");
$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$userManager = new UserManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
$companyManager = new CompanyManager($pdo);
$cities = $tripManager->validCities();

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'company'):
    if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
        <?php $user = $userManager->findById($_SESSION['user_id']); ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Firma Seferleri Yönetimi</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://unpkg.com/htmx.org@1.9.12"></script>
        </head>

        <body class="bg-light">
            <?php include("../../includes/navbar.php"); ?>
            <div class="container my-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Mevcut Seferler</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTripModal">
                        Sefer Ekle
                    </button>
                </div>

                <?php
                $trips = $tripManager->getTripsByCompany($user['company_id']);
                $company = $companyManager->findById($user['company_id']);
                if ($trips) {
                    foreach ($trips as $trip) {
                        echo renderAdminTripbox($company['name'], $trip['departure_time'], $trip['arrival_time'], $trip['price'], $trip['departure_city'], $trip['destination_city'], $trip['capacity'], $trip['id'], $tripManager->getBookedSeats($trip['id']));
                    }
                } else {
                    echo "<div class='alert alert-warning text-center py-2' role='alert'>Hiç Sefer Yok</div>";

                }

                ?>
                <div class="modal fade" id="addTripModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Yeni Sefer Ekle</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="addResultDiv"></div>
                                <input type="hidden" id="operationInput" value="create" name="operation">
                                <input type="hidden" id="csrf_token"
                                    value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>" name="csrf_token">
                                <form>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="addDeparture" class="form-label fw-bold">
                                                <i class="bi bi-geo-alt-fill text-primary"></i> Kalkış Şehri
                                            </label>
                                            <select class="form-select form-select-lg" id="addDeparture" name="from" required>
                                                <option value="" selected disabled>Şehir Seçin</option>
                                                <?php
                                                foreach ($cities as $city) {
                                                    echo "<option value=\"$city\">$city</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="addArrival" class="form-label fw-bold">
                                                <i class="bi bi-geo-alt-fill text-primary"></i> Varış Şehri
                                            </label>
                                            <select class="form-select form-select-lg" id="addArrival" name="to" required>
                                                <option value="" selected disabled>Şehir Seçin</option>
                                                <?php
                                                foreach ($cities as $city) {
                                                    echo "<option value=\"$city\">$city</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="addDepartureDate" class="form-label fw-bold">Kalkış Tarihi</label>
                                            <input type="date" class="form-control" id="addDepartureDate" name="departureDate"
                                                required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="addDepartureTime" class="form-label fw-bold">Kalkış Saati</label>
                                            <input type="time" class="form-control" id="addDepartureTime" name="departureTime"
                                                required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="addArrivalDate" class="form-label fw-bold">Varış Tarihi</label>
                                            <input type="date" class="form-control" id="addArrivalDate" name="arrivalDate"
                                                required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="addArrivalTime" class="form-label fw-bold">Varış Saati</label>
                                            <input type="time" class="form-control" id="addArrivalTime" name="arrivalTime"
                                                required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="addPrice" class="form-label fw-bold">Fiyat</label>
                                            <input type="number" class="form-control" id="addPrice" step="1" name="price"
                                                required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="addCapacity" class="form-label fw-bold">Yolcu Kapasitesi(3 ün
                                                katı)</label>
                                            <input type="number" class="form-control" id="addCapacity" step="3" name="capacity"
                                                required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri Dön</button>
                                <button type="button" class="btn btn-primary" hx-post="/admin/api/companyapi.php"
                                    hx-target="#addResultDiv" hx-swap="innerHTML" hx-include="#operationInput,
                                    #addDeparture,
                                    #addArrival,
                                    #addDepartureDate,
                                    #addDepartureTime,
                                    #addArrivalTime,
                                    #addArrivalDate,
                                    #addPrice,
                                    #addFrom,
                                    #addTo,
                                    #addCapacity,
                                    #csrf_token
                                    ">Sefer
                                    Ekle</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $loggedInUser = $userManager->findById($_SESSION['user_id']);
                $couponCompany = $companyManager->findById($loggedInUser['company_id']);
                $coupons = $paymentManager->getCouponsByCompany($couponCompany['id'] ?? NULL);
                echo renderCouponBox("Firma Kuponları", $coupons, $couponCompany['name'] ?? 'Yok');
                ?>?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

                <?php include("../../includes/footer.php"); ?>
        </body>

        </html>
        <?php

    endif;
else:
    http_response_code(404);
    include('../../notfound.php');
    exit;
endif; ?>