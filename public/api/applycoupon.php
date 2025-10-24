<?php
require_once '../includes/db/db.php';
require_once '../includes/db/PaymentOperations.php';
require_once '../includes/idatlas/idatlas.php';
require_once '../includes/db/UserOperations.php';
require_once('../includes/db/TripOperations.php');
require_once('../includes/db/TicketOperations.php');
require_once('../includes/db/CompanyOperations.php');

session_start();
$userManager = new UserManager($pdo);
$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
$companyManager = new CompanyManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['trip_id']) || !isset($_POST['seatcount']) || (int) $_POST['seatcount'] <= 0) {
        echo '<div class="alert alert-danger">Invalid request</div>';
        exit;
    }

    $seatcount = (int) $_POST['seatcount'];
    $trip = $tripManager->getTripById(getFromAtlas($_POST['trip_id']));

    if (!$trip) {
        echo '<div class="alert alert-danger">Trip not found</div>';
        exit;
    }

    $totalPrice = (int) $trip['price'] * $seatcount;
    $discountAmount = 0;
    $finalTotal = $totalPrice;
    $couponCode = '';

    if (isset($_POST['coupon_id']) && $_POST['coupon_id'] !== 'default') {
        $userCoupon = $paymentManager->getUserCoupon(getFromAtlas($_POST['coupon_id']));

        if ($userCoupon) {
            $coupon = $paymentManager->getCouponById($userCoupon['coupon_id']);


            if ($coupon && ($coupon['company_id'] === $trip['company_id'] || $coupon['company_id'] === 'all')) {
                $couponCode = $coupon['code'];


                $discountAmount = (int) $trip['price'] * ((float) $coupon['discount']);
                $finalTotal = $totalPrice - $discountAmount;
            }
        }
    }


    if ($discountAmount > 0) {
        ?>
        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
            <span class="text-success">İndirim (<?php echo htmlspecialchars($couponCode); ?>)</span>
            <span class="fw-bold text-success">-<?php echo number_format($discountAmount, 2); ?> TL</span>
        </div>
        <?php
    }
    ?>
    <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
        <span class="fs-5 fw-bold">Toplam</span>
        <span class="fs-4 fw-bold text-primary"><?php echo number_format($finalTotal, 2); ?> TL</span>
    </div>
    <small class="text-muted d-block mt-2">Fiyatlarımıza kdv dahildir.</small>
    <?php
}
?>