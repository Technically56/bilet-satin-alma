<?php
session_start();
require_once('../../includes/db/TripOperations.php');
require_once('../../includes/idatlas/idatlas.php');
require_once('../../includes/db/UserOperations.php');
require_once('../../includes/db/TicketOperations.php');
require_once('../../includes/db/PaymentOperations.php');
require_once('../../includes/db/db.php');

$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$userManager = new UserManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);


if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'company') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user = $userManager->findById($_SESSION['user_id']);
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Geçersiz CSRF Tokeni!</div>";
            exit;
        }
        if ($_POST['operation'] === 'update') {
            if (isset($_POST['tripId']) && isset($_POST['from']) && isset($_POST['to']) && isset($_POST['departureDate']) && isset($_POST['departureTime']) && isset($_POST['price']) && isset($_POST['price']) && isset($_POST['arrivalDate']) && isset($_POST['arrivalTime'])) {
                $tripId = getFromAtlas($_POST['tripId']);
                $trip = $tripManager->getTripById($tripId);

                if (!$trip) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Geçersiz Id!</div>";
                    exit;
                }
                if ($trip['company_id'] !== $user['company_id']) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Bu Bilete Erişiminiz Yok</div>";
                    exit;
                }

                $from = $_POST['from'];
                $to = $_POST['to'];
                $price = $_POST['price'];
                $departureDate = $_POST['departureDate'];
                $departureTime = $_POST['departureTime'];
                $arrivalDate = $_POST['arrivalDate'];
                $arrivalTime = $_POST['arrivalTime'];
                $now = new DateTime();
                $fullArrivalDate = DateTime::createFromFormat("Y-m-d H:i", $arrivalDate . " " . $arrivalTime);
                $fullDepartureDate = DateTime::createFromFormat("Y-m-d H:i", $departureDate . " " . $departureTime);
                $diffToNowFromDepart = $fullDepartureDate->getTimestamp() - $now->getTimestamp();
                $diffToArrival = $fullArrivalDate->getTimestamp() - $fullDepartureDate->getTimestamp();
                if (!$tripManager->isValidCity($from) || !$tripManager->isValidCity($to)) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Geçersiz Şehir İsmi!</div>";
                    exit;
                }
                if ((!$fullArrivalDate && !$fullDepartureDate) || $diffToNowFromDepart < 3600 || $diffToArrival < 3600) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Geçersiz Tarihler, en erken 1 kalkış saatı en erken 1 saat sonraya ayarlanabilir ve kalkış ile varış saatleri arası minimum 1 saat!</div>";
                    exit;
                }
                if ((int) $price <= 0) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Fiyat 0 veya 0'dan küçük olamaz!</div>";
                    exit;
                }
                $result = $tripManager->updateTrip($trip['id'], $user['company_id'], $to, $fullArrivalDate->format("Y-m-d H:i"), $fullDepartureDate->format("Y-m-d H:i"), $from, $price, $trip['capacity']);
                if ($result) {
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Sefer Başarıyla Güncellendi!</div>";
                    echo "<script>setTimeout(() => location.href = '/admin/company/dashboard.php', 500)</script>";
                    exit;
                } else {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Fiyat 0 veya 0'dan küçük olamaz!</div>";
                    exit;

                }
            } else {
                echo "<div class='alert alert-danger text-center py-2' role='alert'>Tüm Değerleri Girdiğinize Emin Olun</div>";
                exit;
            }
        }
        if ($_POST['operation'] == 'remove') {
            if (isset($_POST['tripId'])) {
                $trip = $tripManager->getTripById(getFromAtlas($_POST['tripId']));
                if ($trip['company_id'] === $user['company_id']) {
                    $paymentManager->refundTrip($trip['id']);
                    $tripManager->deleteTrip($trip['id'], $user['company_id']);
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Sefer Başarıyla Güncellendi!</div>";
                    echo "<script>setTimeout(() => location.href = '/admin/company/dashboard.php', 500)</script>";
                    exit;
                } else {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Bilinemeyen Silme hatası</div>";
                }
            }
        }
        if ($_POST['operation'] === 'create') {
            if (
                isset($_POST['from']) &&
                isset($_POST['to']) &&
                isset($_POST['departureDate']) &&
                isset($_POST['departureTime']) &&
                isset($_POST['arrivalDate']) &&
                isset($_POST['arrivalTime']) &&
                isset($_POST['price']) &&
                isset($_POST['capacity'])
            ) {
                $from = $_POST['from'];
                $to = $_POST['to'];
                $price = $_POST['price'];
                $capacity = $_POST['capacity'];
                $departureDate = $_POST['departureDate'];
                $departureTime = $_POST['departureTime'];
                $arrivalDate = $_POST['arrivalDate'];
                $arrivalTime = $_POST['arrivalTime'];

                $now = new DateTime();
                $fullDepartureDate = DateTime::createFromFormat("Y-m-d H:i", $departureDate . " " . $departureTime);
                $fullArrivalDate = DateTime::createFromFormat("Y-m-d H:i", $arrivalDate . " " . $arrivalTime);

                if (!$tripManager->isValidCity($from) || !$tripManager->isValidCity($to)) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Geçersiz Şehir İsmi!</div>";
                    exit;
                }

                if (!$fullDepartureDate || !$fullArrivalDate) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Geçersiz Tarih Formatı!</div>";
                    exit;
                }

                $diffToNowFromDepart = $fullDepartureDate->getTimestamp() - $now->getTimestamp();
                $diffToArrival = $fullArrivalDate->getTimestamp() - $fullDepartureDate->getTimestamp();

                if ($diffToNowFromDepart < 3600 || $diffToArrival < 3600) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>
                Geçersiz Tarihler! Kalkış Tarihi Şu andan En Erken 1 saat sonrasına ayarlanmalı ve Varış Tarihi İle Arasınd En az 1 saat olmalıdır.
            </div>";
                    exit;
                }

                if ((int) $price <= 0) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Fiyat 0 veya 0'dan küçük olamaz!</div>";
                    exit;
                }

                if ((int) $capacity <= 0) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kapasite 0 veya 0'dan küçük olamaz!</div>";
                    exit;
                }

                $result = $tripManager->createTrip(
                    $user['company_id'],
                    $to,
                    $fullArrivalDate->format("Y-m-d H:i:s"),
                    $fullDepartureDate->format("Y-m-d H:i:s"),
                    $from,
                    $price,
                    $capacity
                );

                if ($result) {
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Sefer Başarıyla Oluşturuldu!</div>";
                    echo "<script>setTimeout(() => location.href = '/admin/company/dashboard.php', 500)</script>";
                    exit;
                } else {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Sefer oluşturulamadı. Lütfen tekrar deneyin.</div>";
                    exit;
                }
            } else {
                echo "<div class='alert alert-danger text-center py-2' role='alert'>Eksik Parametreler!</div>";
                exit;
            }
        }

    }
}
?>