<?php
class TripManager
{
    private PDO $pdo;
    private array $validCities;
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->validCities = array(
            '',
            'Adana',
            'Adıyaman',
            'Afyon',
            'Ağrı',
            'Amasya',
            'Ankara',
            'Antalya',
            'Artvin',
            'Aydın',
            'Balıkesir',
            'Bilecik',
            'Bingöl',
            'Bitlis',
            'Bolu',
            'Burdur',
            'Bursa',
            'Çanakkale',
            'Çankırı',
            'Çorum',
            'Denizli',
            'Diyarbakır',
            'Edirne',
            'Elazığ',
            'Erzincan',
            'Erzurum',
            'Eskişehir',
            'Gaziantep',
            'Giresun',
            'Gümüşhane',
            'Hakkari',
            'Hatay',
            'Isparta',
            'Mersin',
            'Istanbul',
            'İzmir',
            'Kars',
            'Kastamonu',
            'Kayseri',
            'Kırklareli',
            'Kırşehir',
            'Kocaeli',
            'Konya',
            'Kütahya',
            'Malatya',
            'Manisa',
            'Kahramanmaraş',
            'Mardin',
            'Muğla',
            'Muş',
            'Nevşehir',
            'Niğde',
            'Ordu',
            'Rize',
            'Sakarya',
            'Samsun',
            'Siirt',
            'Sinop',
            'Sivas',
            'Tekirdağ',
            'Tokat',
            'Trabzon',
            'Tunceli',
            'Şanlıurfa',
            'Uşak',
            'Van',
            'Yozgat',
            'Zonguldak',
            'Aksaray',
            'Bayburt',
            'Karaman',
            'Kırıkkale',
            'Batman',
            'Şırnak',
            'Bartın',
            'Ardahan',
            'Iğdır',
            'Yalova',
            'Karabük',
            'Kilis',
            'Osmaniye',
            'Düzce'
        );
    }
    public function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    public function getTripById(string $id): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM Trips WHERE id = :id");
        $statement->execute([
            ':id' => $id
        ]);
        $trip = $statement->fetch(PDO::FETCH_ASSOC);
        return $trip ?: null;
    }
    public function getTripsByCities(string $departure_city, string $destination_city, string $departure_date): ?array
    {
        $startOfDay = date('Y-m-d H:i:s', strtotime($departure_date));
        $endOfDay = date('Y-m-d 23:59:59', strtotime($departure_date));
        $currentTime = date('Y-m-d H:i:s');

        $query = "
                SELECT *
                FROM Trips
                WHERE departure_city = :departure_city
                AND destination_city = :destination_city
                AND departure_time BETWEEN :start_of_day AND :end_of_day
                AND departure_time >= :current_time
                ORDER BY departure_time ASC
            ";

        $statement = $this->pdo->prepare($query);
        $statement->execute([
            ':departure_city' => $departure_city,
            ':destination_city' => $destination_city,
            ':start_of_day' => $startOfDay,
            ':end_of_day' => $endOfDay,
            ':current_time' => $currentTime
        ]);

        $trips = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $trips ?: null;
    }

    public function getTripsByCompany(string $company): ?array
    {
        $statement = $this->pdo->prepare("SELECT * FROM Trips WHERE company_id = :company_id");
        $statement->execute([
            ':company_id' => $company
        ]);
        $trip = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $trip ?: null;
    }





    public function getAllTrips(): array
    {
        $statement = $this->pdo->query("SELECT * FROM Trips");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function isValidCity(string $city): bool
    {
        return in_array($city, $this->validCities);
    }
    public function createTrip(string $company_id, string $destination_city, string $arrival_time, string $departure_time, string $departure_city, int $price, int $capacity, ?string $id = null): bool
    {
        if (!$this->isValidCity($destination_city) || !$this->isValidCity($departure_city)) {
            return false;
        }
        if (!strtotime($arrival_time) || !strtotime($departure_time)) {
            return false;
        }
        if (strtotime($arrival_time) === false || strtotime($departure_time) === false) {
            return false;
        }
        if (strtotime($arrival_time) <= strtotime($departure_time)) {
            return false;
        }

        $this->pdo->beginTransaction();
        try {
            $statement = $this->pdo->prepare("INSERT INTO Trips (id, company_id, destination_city, arrival_time, departure_time, departure_city, price, capacity) VALUES (:id, :company_id, :destination_city, :arrival_time, :departure_time, :departure_city, :price, :capacity)");
            $result = $statement->execute([
                ':id' => $id ?? $this->generateUuid(),
                ':company_id' => $company_id,
                ':destination_city' => $destination_city,
                ':arrival_time' => $arrival_time,
                ':departure_time' => $departure_time,
                ':departure_city' => $departure_city,
                ':price' => $price,
                ':capacity' => $capacity
            ]);
            if ($result) {
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
    public function updateTrip(string $id, string $company_id, string $destination_city, string $arrival_time, string $departure_time, string $departure_city, int $price, int $capacity): bool
    {
        if (!$this->isValidCity($destination_city) || !$this->isValidCity($departure_city)) {
            return false;
        }
        if (!strtotime($arrival_time) || !strtotime($departure_time)) {
            return false;
        }
        if (strtotime($arrival_time) === false || strtotime($departure_time) === false) {
            return false;
        }
        if (strtotime($arrival_time) <= strtotime($departure_time)) {
            return false;
        }
        $statement = $this->pdo->prepare("UPDATE Trips SET destination_city = :destination_city, arrival_time = :arrival_time, departure_time = :departure_time, departure_city = :departure_city, price = :price, capacity = :capacity WHERE id = :id AND company_id = :company_id");
        return $statement->execute([
            ':id' => $id,
            ':company_id' => $company_id,
            ':destination_city' => $destination_city,
            ':arrival_time' => $arrival_time,
            ':departure_time' => $departure_time,
            ':departure_city' => $departure_city,
            ':price' => $price,
            ':capacity' => $capacity
        ]);
    }
    public function deleteTrip(string $id): bool
    {
        $this->pdo->beginTransaction();
        try {
            $deleteticketsstatement = $this->pdo->prepare("DELETE FROM Tickets WHERE trip_id = :trip_id");
            $ticketsResult = $deleteticketsstatement->execute([':trip_id' => $id]);
            $deletetripstatement = $this->pdo->prepare("DELETE FROM Trips WHERE id = :id AND company_id = :company_id");
            $tripResult = $deletetripstatement->execute([':id' => $id]);
            if ($ticketsResult && $tripResult) {
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
    public function getBookedSeats(string $trip_id): array
    {
        $fullSeats = array();
        $statement = $this->pdo->prepare("SELECT * FROM Tickets WHERE trip_id = :trip_id AND status = 'active'");
        $statement->execute([':trip_id' => $trip_id]);
        $tickets = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($tickets as $ticket) {
            $seatStatement = $this->pdo->prepare("SELECT seat_number FROM Booked_Seats WHERE ticket_id = :ticket_id");
            $seatStatement->execute([':ticket_id' => $ticket['id']]);
            $seat = $seatStatement->fetch(PDO::FETCH_ASSOC);
            $fullSeats[] = $seat['seat_number'];
        }
        return $fullSeats;
    }
    public function validCities(): array
    {
        return $this->validCities;
    }

}
?>