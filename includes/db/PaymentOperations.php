<?php
class PaymentManager
{
    private PDO $pdo;
    private UserManager $userManager;
    private TicketManager $ticketManager;

    private TripManager $tripManager;
    public function __construct(PDO $pdo, UserManager $userManager, TicketManager $ticketManager, TripManager $tripManager)
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
    public function buyTicket(string $trip_id, array $seats , ?string $coupon_id): string
    {
        $user_id = $_SESSION['user_id'];
        if(!$user_id){
            return 'login error';
        }
        $user = $this->userManager->findById($user_id);
        if(empty($user)){
            return 'user doesnt exist';
        }
        $trip = $this->tripManager->getTripById($trip_id);
        $trip_price = (int)$trip['price'];

        if(!empty($coupon_id)){
            $userCoupon = $this->getUserCoupons($coupon_id);
            if(!empty($userCoupon)){
                $coupon = $this->getCouponById($userCoupon['id']);
                if($coupon['usage_limit'] > 0){
                    $total_price = ($trip_price * count($seats)) - (int)$coupon['discount'];
                }
            }
        }else{
            $total_price = $trip_price * count($seats);
        }
        
        
        if (!$user) {
            return "Kullanıcı bulunamadı.";
        }
        if (!$trip) {
            return "Sefer bulunamadı.";
        }
        if ($user['balance'] < $total_price) {
            return "Yetersiz bakiye. Lütfen hesabınıza para ekleyin.";
        }
        if (array_intersect($seats, $this->tripManager->getBookedSeats($trip_id))) {
            return "Seçilen koltuklardan bazıları zaten satılmış. Lütfen başka koltuklar seçin.";
        }
        $this->userManager->updateBalance($user_id, $user['balance'] - $total_price);
        foreach ($seats as $seat) {
            $this->ticketManager->createTicket($trip_id, $user_id, $seat, $trip['company_id']);
            #add payment check here
        }

        return "success";
    }
    public function getUserCoupons(string $user_id): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM User_Coupons WHERE user_id = :user_id");
        $statement->execute(['user_id' => $user_id]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCouponById(string $coupon_id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM Coupons WHERE id = :coupon_id");
        $statement->execute(['coupon_id' => $coupon_id]);
        $coupon = $statement->fetch(PDO::FETCH_ASSOC);
        return $coupon ?: null;
    }
    public function deleteUserCoupon(string $coupon_id): bool{
        $statement = $pdo->prepare("DELETE FROM User_Coupons WHERE id = :coupon_id");
        return $pdo->execute(['coupon_id'=> $coupon_id]);
    }
}
?>