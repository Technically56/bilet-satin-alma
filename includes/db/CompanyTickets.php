<?php 
    class CompanyTicketManager{
        private PDO $pdo;
        private string $companyId;

        
        public function __construct(PDO $pdo, string $companyId){
            $this->pdo = $pdo;
            $this->companyId = $companyId;
        }
        public function generateUuid(): string {
            $data = random_bytes(16);
            $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
            $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }
        public function getAllTickets(): array {
            $statement = $this->pdo->prepare("SELECT * FROM Tickets WHERE company_id = :company_id");
            $statement->execute([':company_id' => $this->companyId]);
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        }
        public function createTicket(string $trip_id, string $user_id,string array $seat_numbers):bool {
            $this->pdo->beginTransaction();
        }
        
    }


?>