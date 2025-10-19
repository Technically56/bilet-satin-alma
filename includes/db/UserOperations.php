<?php
class UserManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    public function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE email = :email');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findById(string $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM User');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByCompany(string $companyId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE company_id = :company_id');
        $stmt->execute([':company_id' => $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllByRole(string $role): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM User WHERE role = :role');
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(string $full_name, string $email, string $password, ?string $id = null): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO User (id,full_name, email, password, role) VALUES (:id ,:full_name, :email, :password, "user")'
        );
        return $stmt->execute([
            ':id' => $id ?? $this->generateUuid(),
            ':full_name' => $full_name,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
    }

    public function update(string $id, string $full_name, string $email, string $password): bool
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare(
            'UPDATE User SET full_name = :full_name, email = :email, password = :password WHERE id = :id'
        );
        return $stmt->execute([
            ':full_name' => $full_name,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':id' => $id
        ]);
    }

    public function login(string $email, string $password): bool
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_fullname'] = $user['full_name'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            return true;
        }
        return false;
    }

    public function updateBalance(string $id, int $amount): bool
    {
        $stmt = $this->pdo->prepare('UPDATE User SET balance = balance + :amount WHERE id = :id');
        return $stmt->execute([':amount' => $amount, ':id' => $id]);
    }
    public function getBalance(string $id): int
    {
        $stmt = $this->pdo->prepare('SELECT balance FROM User WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['balance'] : 0;
    }

    public function updateRole(string $id, string $role): bool
    {
        $stmt = $this->pdo->prepare('UPDATE User SET role = :role WHERE id = :id');
        return $stmt->execute([':role' => $role, ':id' => $id]);
    }

    public function updateCompany(string $id, string $companyId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE User SET company_id = :company_id WHERE id = :id');
        return $stmt->execute([':company_id' => $companyId, ':id' => $id]);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM User WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
?>