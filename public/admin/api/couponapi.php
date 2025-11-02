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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION["user_id"]) && ($_SESSION['user_role'] === 'company') || $_SESSION['user_role'] === 'admin') {
        $user = $userManager->findById($_SESSION['user_id']);
        if ($_POST['operation'] === 'update') {
            if (isset($_POST['csrf_token']) && isset($_POST['couponId']) && isset($_POST['code']) && isset($_POST['discount']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['usage'])) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Csrf Token Hatası!</div>";
                    exit;
                }
                $couponId = getFromAtlas($_POST["couponId"]);
                $coupon = $paymentManager->getCouponById(getFromAtlas($_POST['couponId']));
                $code = $_POST["code"];
                $discount = $_POST["discount"];
                $date = $_POST["date"];
                $time = $_POST["time"];
                $usage_limit = $_POST["usage"];
                $now = new DateTime();
                $fullExpiry = DateTime::createFromFormat("Y-m-d H:i", $date . " " . $time);
                $timeDiff = $fullExpiry->getTimestamp() - $now->getTimestamp();

                if ($timeDiff < 3600) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon son kullanım tarihi şu andan en az 1 saat sonraya ayarlanmalıdır!</div>";
                    exit;
                }
                if (!($discount > 0 && $discount < 100)) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon indirim yüzdesi 0-99 arasında olmalıdır!</div>";
                    exit;
                }
                if (strlen($code) < 4) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>İndirim kodu en az 4 haneli olmalıdır.</div>";
                    exit;
                }
                if ($coupon['company_id'] !== $user['company_id']) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Bu kupona erişiminiz yok!</div>";
                    exit;
                }
                $discount = (float) $discount / 100;
                $result = $paymentManager->updateCoupon($couponId, $code, $discount, $usage_limit, $fullExpiry->format("Y-m-d H:i:s"), $coupon['company_id']);
                if ($result) {
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Kupon Başarıyla Güncellendi</div>";
                    if ($_SESSION['user_role'] === 'company') {
                        echo "<script>setTimeout(() => location.href = '/admin/company/dashboard.php', 500)</script>";
                        exit;
                    }
                    if ($_SESSION['user_role'] === 'admin') {
                        echo "<script>setTimeout(() => location.href = '/admin/site/admin_dashboard.php', 500)</script>";
                        exit;
                    }
                } else {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon Düzenlenemedi</div>";
                    exit;

                }
            }

        }
        if ($_POST['operation'] === 'create') {

            if (isset($_POST['csrf_token']) && isset($_POST['code']) && isset($_POST['discount']) && isset($_POST['date']) && isset($_POST['time']) && isset($_POST['usage'])) {
                if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Csrf Token Hatası!</div>";
                    exit;
                }
                $code = $_POST["code"];
                $discount = $_POST["discount"];
                $date = $_POST["date"];
                $time = $_POST["time"];
                $usage_limit = $_POST["usage"];
                $now = new DateTime();
                $fullExpiry = DateTime::createFromFormat("Y-m-d H:i", $date . " " . $time);
                $timeDiff = $fullExpiry->getTimestamp() - $now->getTimestamp();

                if ($timeDiff < 3600) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon son kullanım tarihi şu andan en az 1 saat sonraya ayarlanmalıdır!</div>";
                    exit;
                }
                if (!($discount > 0 && $discount < 100)) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon indirim yüzdesi 0-99 arasında olmalıdır!</div>";
                    exit;
                }
                if (strlen($code) < 4) {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>İndirim kodu en az 4 haneli olmalıdır.</div>";
                    exit;
                }
                $discount = ((float) $discount) / 100.0;
                $result = $paymentManager->createCoupon($code, $discount, $usage_limit, $fullExpiry->format("Y-m-d H:i:s"), $user['company_id']);
                if ($result) {
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Kupon Başarıyla Güncellendi</div>";
                    if ($_SESSION['user_role'] === 'company') {
                        echo "<script>setTimeout(() => location.href = '/admin/company/dashboard.php', 500)</script>";
                        exit;
                    }
                    if ($_SESSION['user_role'] === 'admin') {
                        echo "<script>setTimeout(() => location.href = '/admin/site/admin_dashboard.php', 500)</script>";
                        exit;
                    }
                } else {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon Oluşturulamadı!</div>";
                    exit;
                }
            }

        }
    }
    if ($_POST['operation'] === 'remove') {

        if (isset($_POST['csrf_token']) && isset($_POST['couponId'])) {
            if ($_SESSION['csrf_token'] !== $_POST['csrf_token']) {
                echo "<div class='alert alert-danger text-center py-2' role='alert'>Csrf Token Hatası!</div>";
                exit;
            }
            $coupon = $paymentManager->getCouponById(getFromAtlas($_POST['couponId']));
            $couponCompanyId = $coupon['company_id'] ?? NULL;
            $userCompanyId = $user['company_id'] ?? NULL;
            if ($userCompanyId === $couponCompanyId) {
                $result = $paymentManager->deleteCoupon($coupon['id']);
                if ($result) {
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Kupon Başarıyla Silindi</div>";
                    if ($_SESSION['user_role'] === 'company') {
                        echo "<script>setTimeout(() => location.href = '/admin/company/dashboard.php', 500)</script>";
                        exit;
                    }
                    if ($_SESSION['user_role'] === 'admin') {
                        echo "<script>setTimeout(() => location.href = '/admin/site/admin_dashboard.php', 500)</script>";
                        exit;
                    }
                } else {
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>Kupon Silme Hatası!</div>";
                    exit;
                }

            } else {
                echo "<div class='alert alert-danger text-center py-2' role='alert'>Bu kupona erişiminiz yok!</div>";
                exit;
            }
        } else {

        }
    }
}
?>