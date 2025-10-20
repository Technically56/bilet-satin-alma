<?php
class TicketManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function getTicketById(string $ticket_id, string $company_id): ?array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM Tickets WHERE id = :id AND company_id = :company_id"
        );
        $statement->execute([
            ':id' => $ticket_id,
            ':company_id' => $company_id,
        ]);
        $ticket = $statement->fetch(PDO::FETCH_ASSOC);
        return $ticket ?: null;
    }

    public function getAllTickets(string $company_id): array
    {
        $statement = $this->pdo->prepare(
            "SELECT * FROM Tickets WHERE company_id = :company_id"
        );
        $statement->execute([':company_id' => $company_id]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createTicket(
        string $trip_id,
        string $user_id,
        int $seat_number,
        ?float $discount = null,
        ?string $id = null
    ): bool {
        try {
            $this->pdo->beginTransaction();


            $statement = $this->pdo->prepare("SELECT price FROM Trips WHERE id = :trip_id");
            $statement->execute([':trip_id' => $trip_id]);
            $trip = $statement->fetch(PDO::FETCH_ASSOC);
            $price = (int) $trip ? $trip['price'] : null;

            $uuid = $this->generateUuid();
            $total_price = $discount !== null ? ($price - $discount) : $price;

            $ticketStmt = $this->pdo->prepare("
                INSERT INTO Tickets (id, trip_id, user_id, status, total_price)
                VALUES (:id, :trip_id, :user_id, :status, :total_price)
            ");
            $result = $ticketStmt->execute([
                ':id' => $uuid,
                ':trip_id' => $trip_id,
                ':user_id' => $user_id,
                ':status' => 'active',
                ':total_price' => (int) $total_price
            ]);

            $seatStmt = $this->pdo->prepare("
                INSERT INTO Booked_Seats (id, ticket_id, seat_number)
                VALUES (:id, :ticket_id, :seat_number)
            ");
            $seatResult = $seatStmt->execute([
                ':id' => $this->generateUuid(),
                ':ticket_id' => $uuid,
                ':seat_number' => $seat_number
            ]);

            if ($result && $seatResult) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollBack();
                $_SESSION['debug'] = "result: " . ($result ? 'true' : 'false') . ", seatResult: " . ($seatResult ? 'true' : 'false');
                return false;
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function cancelTicket(string $ticket_id): bool
    {
        $statement = $this->pdo->prepare(
            "UPDATE Tickets SET status = :status WHERE id = :id"
        );
        return $statement->execute([
            ':id' => $ticket_id,
            ':status' => 'canceled'
        ]);
    }

    public function deleteTicket(string $ticket_id): bool
    {
        try {
            $this->pdo->beginTransaction();

            $seatDelete = $this->pdo->prepare(
                "DELETE FROM Booked_Seats WHERE ticket_id = :ticket_id"
            );
            $seatResult = $seatDelete->execute([':ticket_id' => $ticket_id]);

            $ticketDelete = $this->pdo->prepare(
                "DELETE FROM Tickets WHERE id = :id"
            );
            $ticketResult = $ticketDelete->execute([':id' => $ticket_id]);

            if ($seatResult && $ticketResult) {
                $this->pdo->commit();
                return true;
            } else {
                $this->pdo->rollBack();
                return false;
            }
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }

    public function updateTicket(
        string $ticket_id,
        string $user_id,
        string $status
    ): bool {
        $statement = $this->pdo->prepare("
            UPDATE Tickets
            SET user_id = :user_id, status = :status
            WHERE id = :id
        ");
        return $statement->execute([
            ':id' => $ticket_id,
            ':user_id' => $user_id,
            ':status' => $status,
        ]);
    }
}
?>