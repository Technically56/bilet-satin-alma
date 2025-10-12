<?php 
class CompanyManager{
    private PDO $pdo;
    public function __construct(PDO $pdo){
            $this->pdo = $pdo;
    }
    public function generateUuid(): string {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    public function create(string $name, string $logopath,?string $id = null): bool {
        $statement = $this->pdo->prepare("INSERT INTO Bus_Company (id, name, logopath) VALUES (:id, :name, :logopath)");
        return $statement->execute([
            ':id' => $id ?? $this->generateUuid(),
            ':name' => $name,
            ':logopath' => $logopath
        ]);
    }
    public function findById(string $id): ?array {
        $statement = $this->pdo->prepare("SELECT * FROM Bus_Company WHERE id = :id");
        $statement->execute([':id' => $id]);
        $company = $statement->fetch(PDO::FETCH_ASSOC);
        return $company ?: null;
    }
    public function findAll(): array {
        $statement = $this->pdo->query("SELECT * FROM Bus_Company");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function findByName(string $name): ?array {
        $statement = $this->pdo->prepare("SELECT * FROM Bus_Company WHERE name = :name");
        $statement->execute([':name' => $name]);
        $company = $statement->fetch(PDO::FETCH_ASSOC);
        return $company ?: null;
    }
    public function update(string $id, string $name, string $logopath): bool {
        $statement = $this->pdo->prepare("UPDATE Bus_Company SET name = :name, logopath = :logopath WHERE id = :id");
        return $statement->execute([
            ':id' => $id,
            ':name' => $name,
            ':logopath' => $logopath
        ]);
    }
    public function delete(string $id): bool {
        $statement = $this->pdo->prepare("DELETE FROM Bus_Company WHERE id = :id");
        return $statement->execute([':id' => $id]);
    }
    
}
?>