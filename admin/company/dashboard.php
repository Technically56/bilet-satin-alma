<?php
session_start(options: [
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../../includes/db/TripOperations.php');
require_once('../../includes/idatlas/idatlas.php');
require_once('../../includes/db/UserOperations.php');
require_once('../../includes/db/TicketOperations.php');
require_once('../../includes/db/PaymentOperations.php');
require_once('../../includes/db/db.php');
require_once('../../includes/admintripbox.php');
require_once('../../includes/db/CompanyOperations.php');
$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$userManager = new UserManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
$companyManager = new CompanyManager($pdo);


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
                                <h5 class="modal-title">Add New Trip</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="departure" class="form-label fw-bold">Departure City</label>
                                            <input type="text" class="form-control" id="departure" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="arrival" class="form-label fw-bold">Arrival City</label>
                                            <input type="text" class="form-control" id="arrival" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label for="tripDate" class="form-label fw-bold">Date</label>
                                            <input type="date" class="form-control" id="tripDate" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="departureTime" class="form-label fw-bold">Departure Time</label>
                                            <input type="time" class="form-control" id="departureTime" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="price" class="form-label fw-bold">Price</label>
                                            <input type="number" class="form-control" id="price" step="0.01" required>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary">Add Trip</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

                <?php include("../../includes/footer.php"); ?>
        </body>

        </html>
    <?php endif; endif; ?>