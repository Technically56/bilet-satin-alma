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
        $this->tripManager = $tripManager;
    }
    public function addFunds(string $user_id, int $balance, string $card_number): bool
    {
        if (!$this->luhnvalidate($card_number)) {
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
    public function buyTicket(string $trip_id, array $seats, ?string $coupon_id): string
    {
        $user_id = $_SESSION['user_id'];
        $discount = null;
        if (!$user_id) {
            return 'login error';
        }
        $user = $this->userManager->findById($user_id);
        if (empty($user)) {
            return 'user doesnt exist';
        }
        $trip = $this->tripManager->getTripById($trip_id);
        $trip_price = (int) $trip['price'];

        if (!empty($coupon_id)) {
            $userCoupon = $this->getUserCoupon($coupon_id);
            if (!empty($userCoupon)) {
                $coupon = $this->getCouponById($userCoupon['coupon_id']);
                if ($coupon['company_id'] != '') {
                    if ($coupon['company_id'] !== $trip['company_id']) {
                        return "Bu kupon bu şirket için geçerli değil.";
                    }
                    if ($coupon['usage_limit'] > 0) {
                        $discount = (int) ($trip_price * (float) $coupon['discount']);
                        $total_price = ($trip_price * count($seats)) - $discount;
                    }
                } elseif ($coupon['company_id'] === '') {
                    $total_price = ($trip_price * count($seats)) - ($trip_price * (float) $coupon['discount']);
                }
            }
        } else {
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

        foreach ($seats as $seat) {
            if ($discount !== null) {
                $result = $this->ticketManager->createTicket($trip_id, $user_id, (int) $seat, $discount);
                $this->deleteUserCoupon($userCoupon['id']);
                $this->updateCouponUsage($coupon['id']);
                $discount = null;
            } else {
                $result = $this->ticketManager->createTicket($trip_id, $user_id, (int) $seat, );
            }
            if (!$result) {
                return "noresult";
            }
        }

        $this->userManager->updateBalance($user_id, -$total_price);
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
    public function deleteUserCoupon(string $coupon_id): bool
    {
        $statement = $this->pdo->prepare("DELETE FROM User_Coupons WHERE id = :coupon_id");
        return $statement->execute(['coupon_id' => $coupon_id]);
    }
    public function getUserCoupon(string $coupon_id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM User_Coupons WHERE id = :coupon_id");
        $statement->execute(['coupon_id' => $coupon_id]);
        $coupon = $statement->fetch(PDO::FETCH_ASSOC);
        return $coupon ?: null;
    }
    public function updateCouponUsage(string $coupon_id): bool
    {
        $statement = $this->pdo->prepare("UPDATE Coupons SET usage_limit = usage_limit - 1 WHERE id = :coupon_id AND usage_limit > 0");
        return $statement->execute(['coupon_id' => $coupon_id]);
    }
    public function getCouponByCode(string $coupon_code): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM Coupons WHERE code = :code');
        $statement->execute([':code' => $coupon_code]);
        return $statement->fetch(PDO::FETCH_ASSOC) ?? null;
    }
    public function addUserCoupon(string $user_id, string $coupon_code): bool
    {
        $statement = $this->pdo->prepare('INSERT INTO User_Coupons(id,user_id,coupon_id) VALUES(:id, :user_id, :coupon_id)');
        return $statement->execute(['id' => $this->userManager->generateUuid(), 'user_id' => $user_id, 'coupon_id' => $coupon_code]);
    }
    public function getCouponsByCompany(?string $company_id): array
    {
        if ($company_id) {
            $statement = $this->pdo->prepare('SELECT * FROM Coupons WHERE company_id = :company_id');
            $statement->execute(['company_id' => $company_id]);
            return $statement->fetchAll(PDO::FETCH_ASSOC) ?? [];
        } else {
            $statement = $this->pdo->prepare('SELECT * FROM Coupons WHERE company_id IS NULL');
            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC) ?? [];
        }





    }

    public function refundTrip($trip_id): bool
    {
        $statement = $this->pdo->prepare('SELECT * FROM Tickets WHERE trip_id = :trip_id');
        $statement->execute(['trip_id' => $trip_id]);
        $result = $statement->fetchAll(PDO::FETCH_ASSOC) ?? [];
        if (!empty($result)) {
            $this->pdo->beginTransaction();
            foreach ($result as $ticket) {
                $refund = $this->userManager->updateBalance($ticket['user_id'], $ticket['total_price']);
                if (!$refund) {
                    $this->pdo->rollBack();
                    return 0;
                }
            }
            $this->pdo->commit();
            return 1;
        } else {
            return 0;
        }
    }
    public function createCoupon(string $code, float $discount, int $usage_limit, string $expire_date, ?string $company_id = null): bool
    {
        $this->pdo->beginTransaction();
        $statement = $this->pdo->prepare('INSERT INTO Coupons(id,code,discount,usage_limit,expire_date,company_id) VALUES (:id,:code,:discount,:usage_limit,:expire_date,:company_id)');
        $result = $statement->execute(['id' => $this->tripManager->generateUuid(), 'code' => $code, 'discount' => $discount, 'usage_limit' => $usage_limit, 'expire_date' => $expire_date, 'company_id' => $company_id]);
        if ($result) {
            $this->pdo->commit();
            return true;
        } else {
            $this->pdo->rollBack();
            return false;
        }
    }
    public function deleteCoupon(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Coupons WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }
    public function updateCoupon(string $id, string $code, float $discount, int $usage_limit, string $expire_date, ?string $company_id = null): bool
    {
        $this->pdo->beginTransaction();
        $statement = $this->pdo->prepare('UPDATE Coupons SET code = :code, discount = :discount, usage_limit = :usage_limit, expire_date = :expire_date, company_id = :company_id WHERE id = :id');
        $result = $statement->execute(['id' => $id, 'code' => $code, 'discount' => $discount, 'usage_limit' => $usage_limit, 'expire_date' => $expire_date, 'company_id' => $company_id]);
        if ($result) {
            $this->pdo->commit();
            return true;
        } else {
            $this->pdo->rollBack();
            return false;
        }
    }
}
?>