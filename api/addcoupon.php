<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once('../includes/db/UserOperations.php');
require_once('../includes/db/TicketOperations.php');
require_once('../includes/db/db.php');
require_once('../includes/idatlas/idatlas.php');
require_once('../includes/db/PaymentOperations.php');
require_once('../includes/db/TripOperations.php');
$userManager = new UserManager($pdo);
$ticketManager = new TicketManager($pdo);
$tripManager = new TripManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['couponCode']) && isset($_POST['csrf_token']) && ($_SESSION['csrf_token'] === $_POST['csrf_token'])) {
        $coupon = $paymentManager->getCouponByCode($_POST['couponCode']);
        $user = $userManager->findById($_SESSION['user_id']);
        print_r($user);
        print_r($coupon);
        if ($user !== null) {
            if ($coupon !== null) {
                $pdo->beginTransaction();
                $result = $paymentManager->addUserCoupon($user['id'], $coupon['id']);
                if ($result === true) {
                    $pdo->commit();
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Kupon Başarıyla Eklendi!</div>";
                    echo "<script>setTimeout(() => location.href = '/profile.php', 1000)</script>";
                } else {
                    $pdo->rollBack();
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon Eklenme Hatası</div>";
                    echo "<script>setTimeout(() => location.href = '/profile.php', 1000)</script>";
                }
            }
            echo "<h1>hello</h1>";
        }
    }
    echo "<h1>hello</h1>";
}
echo "<h1>hello</h1>";

?>