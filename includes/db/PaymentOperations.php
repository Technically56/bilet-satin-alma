<?php
class PaymentManager
{
    private PDO $pdo;
    private UserManager $userManager;
    private TicketManager $ticketManager;
    public function __construct(PDO $pdo, UserManager $userManager, TicketManager $ticketManager)
    {
        $this->pdo = $pdo;
        $this->userManager = $userManager;
        $this->ticketManager = $ticketManager;
    }
    public function addFunds(string $user_id, int $balance, string $card_number, string $expiry, string $cvv): bool
    {
        if (!$this->luhnvalidate($card_number)) {
            return false;
        }
        if (strtotime($expiry) < time()) {
            return false;
        }
        if (!preg_match('/^\d{3,4}$/', $cvv)) {
            return false;
        }
        return $this->userManager->updateBalance($user_id, $balance);
    }
    public function luhnvalidate(string $number): bool
    {
        $number = preg_replace('/\D/', '', $number);
        $sum = 0;
        $alt = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = (int) $number[$i];
            if ($alt) {
                $n *= 2;
                if ($n > 9) {
                    $n -= 9;
                }
            }
            $sum += $n;
            $alt = !$alt;
        }
        return ($sum % 10 == 0);
    }
    public function buyTicket(string $user_id, string $trip_id, array $seats): string
    {
        return "#TODO";
    }
}
?>