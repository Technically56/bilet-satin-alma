<?php
require_once 'includes/db/db.php';
require_once 'includes/db/UserOperations.php';
require_once 'includes/db/TripOperations.php';
require_once 'includes/db/TicketOperations.php';
require_once 'includes/db/PaymentOperations.php';
require_once 'includes/idatlas/idatlas.php';
$userManager = new UserManager($pdo);
$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);

session_start([
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        $user_id = $_SESSION['user_id'];
        $trip_id = $_POST['trip_id'];
        $seats = $_POST['seats'];
        $csrf = $_POST['csrf_token'];
        if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
            die("CSRF tokeni geçersiz.");
        }
        if (empty($seats)) {
            die("Lütfen en az bir koltuk seçin.");
        }
        $pdo->beginTransaction();
        $result = $paymentManager->buyTicket($user_id, $trip_id, $seats);
        if ($result === "success") {
            $pdo->commit();
            #todo redirect to tickets page
        } else {
            $pdo->rollBack();
            die($result);
        }
    }
}
?>

<?php if ($_SERVER['REQUEST_METHOD'] === 'GET'):
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])): ?>




    <?php endif;
endif; ?>