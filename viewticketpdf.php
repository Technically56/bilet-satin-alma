<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("includes/db/TicketOperations.php");
require_once("includes/db/CompanyOperations.php");
require_once("includes/db/UserOperations.php");
require_once("includes/db/db.php");
require_once("includes/db/TripOperations.php");
require_once("includes/tickettemplate.php");
require_once("includes/idatlas/idatlas.php");
require __DIR__ . '/vendor/autoload.php';

use Mpdf\Mpdf;


session_start(options: [
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);
$userManager = new UserManager($pdo);
$tripManager = new TripManager($pdo);
$companyManager = new CompanyManager($pdo);
$ticketManager = new TicketManager($pdo);

if (isset($_SESSION["user_id"]) && isset($_SESSION["user_role"])) {
    if ($_SERVER["REQUEST_METHOD"] === "GET") {
        if (isset($_GET['id'])) {
            $user = $userManager->findById($_SESSION['user_id']);
            $ticket = $ticketManager->getTicketById(getFromAtlas($_GET['id']));
            $trip = $tripManager->getTripById($ticket['trip_id']);
            $company = $companyManager->findById($trip['company_id']);
            $seat = $ticketManager->getSeatFromTicket($ticket['id']);

            if (!$ticket) {
                echo "<script>setTimeout(() => location.href = '/notfound.php', 50)</script>";
            }

            if ($ticket['user_id'] !== $_SESSION['user_id']) {
                die("Bu bilete erişiminiz yok!");
            }
            if (!$trip) {
                die("Sefer Bulunamadı.");

            }
            if (!$company) {
                die("Firma Bulunamadı");
            }
            $printed_ticket = renderTicketTemplate(
                $user['full_name'],
                $user['email'],
                $company['name'],
                $ticket['created_at'],
                $trip['departure_city'],
                $trip['destination_city'],
                $trip['departure_time'],
                $trip['arrival_time'],
                $seat['seat_number'],
                $ticket['total_price'],
                ((int) $trip['price'] - (int) $ticket['total_price']) === 0 ? ((int) $trip['price'] - (int) $ticket['total_price']) : null
            );
            $mpdf = new Mpdf();
            $mpdf->WriteHtml($printed_ticket);
            $mpdf->Output("ticket.pdf", "I");
        }
    }
} else {
    echo "<script>setTimeout(() => location.href = '/needlogin.php', 50)</script>";
}
?>