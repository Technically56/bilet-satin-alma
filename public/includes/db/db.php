<?php
$dsn = 'sqlite:/var/www/html/public/database/database.db';
$user = null;
$pass = null;
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO(dsn: $dsn, username: $user, password: $pass, options: $options);
    $pdo->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die("Database connection failed: " . $e->getMessage());

}
?>