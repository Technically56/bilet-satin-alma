<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
    <title>Sefer Arama</title>
</head>

<body>
    <?php session_start([
        'cookie_path' => '/',
        'cookie_lifetime' => 3600,
        'cookie_secure' => false,
        'cookie_httponly' => true,
        'cookie_samesite' => 'lax',
    ]);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) { ?>
        <?php

        require_once 'includes/db/db.php';
        require_once 'includes/db/TripOperations.php';
        require_once 'includes/db/CompanyOperations.php';
        require_once 'includes/tripbox.php';

        $companyManager = new CompanyManager($pdo);
        ;
        $tripManager = new TripManager($pdo);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!empty($_POST['from']) && !empty($_POST['to']) && !empty($_POST['date'])) {
                $from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_SPECIAL_CHARS);
                $to = filter_input(INPUT_POST, 'to', FILTER_SANITIZE_SPECIAL_CHARS);
                $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
                $date = $date . ' ' . '00:00:00';
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $date);
                echo $datetime->format('Y-m-d H:i:s');
                if ($tripManager->isValidCity($from) && $tripManager->isValidCity($to)) {
                    if ($datetime && $datetime->format('Y-m-d H:i:s') === $date) {
                        $trips = $tripManager->getTripsByCities($from, $to, $date);
                        if (!empty($trips)) {
                            foreach ($trips as $trip) {
                                $bookedseats = $tripManager->getBookedSeats($trip['id']);
                                $company = $companyManager->findById($trip['company_id']);
                                $time = new DateTime($trip['departure_time']);
                                echo displayTripBox(
                                    $company['name'],
                                    $company['logo_path'],
                                    $time->format("H:i"),
                                    $trip['price'],
                                    $trip['capacity'],
                                    $trip['id'],
                                    $bookedseats
                                );
                            }
                        } else {
                            echo "<div class='alert alert-warning text-center' role='alert'>Sefer bulunamadı.</div>";
                        }

                    } else {
                        echo "<div class='alert alert-warning text-center' role='alert'>Datetime</div>";
                    }
                } else {
                    echo "<div class='alert alert-warning text-center' role='alert'>Datetime</div>";
                }
            } else {
                echo "<div class='alert alert-warning text-center' role='alert'>Validcity</div>";
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
            <?php include 'includes/navbar.php'; ?>
            <div class="container mt-5">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-search"></i> Otobüs Seferi Ara
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <form action="findtrip.php" method="POST" id="searchForm">
                            <div class="row g-3 d-flex justify-content-center">
                                <!-- From City -->
                                <div class="col-md-6 col-lg-3">
                                    <label for="fromCity" class="form-label fw-bold">
                                        <i class="bi bi-geo-alt-fill text-primary"></i> Nereden
                                    </label>
                                    <select class="form-select form-select-lg" id="from" name="from" required>
                                        <option value="" selected disabled>Şehir Seçin</option>
                                        <?php
                                        $cities = $tripManager->validCities();
                                        foreach ($cities as $city) {
                                            echo "<option value=\"$city\">$city</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- To City -->
                                <div class="col-md-6 col-lg-3">
                                    <label for="toCity" class="form-label fw-bold">
                                        <i class="bi bi-geo-fill text-success"></i> Nereye
                                    </label>
                                    <select class="form-select form-select-lg" id="to" name="to" required>
                                        <option value="" selected disabled>Şehir Seçin</option>
                                        <?php
                                        foreach ($cities as $city) {
                                            echo "<option value=\"$city\">$city</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Date -->
                                <div class="col-md-6 col-lg-3">
                                    <label for="travelDate" class="form-label fw-bold">
                                        <i class="bi bi-calendar-event text-info"></i> Tarih
                                    </label>
                                    <input type="date" class="form-control form-control-lg" id="date" name="date" required>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="d-grid">
                                        <button hx-post="findtrip.php" hx-include="#to #from #date" hx-target="#result"
                                            hx-swap="innerHTML" type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-search"></i> Sefer Ara
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden field for ISO 8601 datetime -->
                            <input type="hidden" id="isoDateTime" name="datetime">
                        </form>
                    </div>
                </div>
            </div>
            <div class="container mt-4" id="result"></div>
            <script>
                // Update hidden ISO 8601 datetime field when date or time changes
                const dateInput = document.getElementById('travelDate');
                const timeInput = document.getElementById('travelTime');
                const isoInput = document.getElementById('isoDateTime');

                function updateISO() {
                    const date = dateInput.value;
                    const time = timeInput.value || "00:00"; // default to midnight if no time
                    if (date) {
                        isoInput.value = `${date}T${time}`;
                    }
                }

                dateInput.addEventListener('change', updateISO);
                timeInput.addEventListener('change', updateISO);
            </script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <?php include('includes/footer.php'); ?>
        <?php endif; ?>
    <?php } ?>
</body>

</html>