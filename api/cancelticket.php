<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../includes/db/db.php");
require_once("../includes/db/TicketOperations.php");
require_once("../includes/db/UserOperations.php");
require_once("../includes/idatlas/idatlas.php");

$ticketManager = new TicketManager($pdo);
$userManager = new UserManager($pdo);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["ticket"]) && $_POST["csrf_token"]) {
        $ticket_id = getFromAtlas($_POST["ticket"]);
        $user = $userManager->findById($_SESSION["user_id"]);
        if ($user && $ticket_id) {
            $ticket = $ticketManager->getTicketById($ticket_id);
            if ($ticket['user_id'] === $user['id']) {
                $pdo->beginTransaction();
                $result = $ticketManager->updateTicket($ticket_id, $user['id'], 'canceled');
                $seatresult = $ticketManager->deleteSeatFromTicket($ticket_id);
                if ($result && $seatresult) {
                    $userManager->updateBalance($user['id'], (int) $ticket['total_price']);
                    $pdo->commit();
                    echo "<div class='alert alert-success text-center py-2' role='alert'>Biletiniz Başarılı Bir Şekilde İptal Edilidi!</div>";
                    echo "<script>setTimeout(() => location.href = '/profile.php', 1000)</script>";
                } else {
                    $pdo->rollBack();
                    echo "<div class='alert alert-danger text-center py-2' role='alert'>$message</div>";
                }
            }
        }
    }
}
?>