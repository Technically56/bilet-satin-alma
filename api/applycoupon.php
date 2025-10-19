<?php
require_once '../includes/db/db.php';
require_once '../includes/db/PaymentOperations.php';
require_once '../includes/idatlas/idatlas.php';
require_once '../UserOperations.php';
session_start();
$userManager = new UserManager($pdo);
$tripManager = new TripManager($pdo);
$ticketManager = new TicketManager($pdo);
$paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
$companyManager = new CompanyManager($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST'):
    if (isset($_POST['trip_id']) && isset($_POST['coupon_id']) && isset($_POST['seatcount']) && ((int) $_POST['seatcount'] > 0)):
        $userCoupon = $paymentManager->getUserCoupon(getFromAtlas($_POST['coupon_id']));
        $trip = $tripManager->getTripById(getFromAtlas($_POST['trip_id']));
        $seatcount = (int) $_POST['seatcount'];
        if ($userCoupon && $trip):
            $coupon = $paymentManager->getCouponById($userCoupon['coupon_id']);
            if ($coupon['company_id'] === $trip['company_id'] || $coupon['company_id'] === 'all'):
                ?>

                <div class="d-flex justify-content-between mb-4 pb-3 border-bottom">
                    <span class="text-success">İndirim (<?php echo htmlspecialchars($coupon['code']); ?>)</span>
                    <span class="fw-bold text-success">-<?php echo htmlspecialchars((int) $coupon['discount']); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-5 fw-bold">Toplam</span>
                    <span
                        class="fs-4 fw-bold text-primary"><?php echo htmlspecialchars(((int) $trip['price'] * $seatcount)) - (int) $coupon['discount']; ?></span>
                    <small class="text-muted d-block mt-2">Fiyatlarımıza kdv dahildir.</small>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (isset($trip_id) && empty($coupon_id) && isset($seatcount)):
        $trip = $tripManager->getTripById(getFromAtlas($_POST['trip_id']));
        $seatcount = (int) $_POST['seatcount'];
        if ($seatcount > 0): ?>
            <div class="d-flex justify-content-between mb-4">
                <span class="fs-5 fw-bold">Toplam</span>
                <span class="fs-4 fw-bold text-primary"><?php echo htmlspecialchars(((int) $trip['price'] * $seatcount)); ?></span>
                <small class="text-muted d-block mt-2">Fiyatlarımıza kdv dahildir.</small>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>