<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../includes/db/db.php");
require_once("../includes/db/UserOperations.php");
require_once("../includes/idatlas/idatlas.php");

$userManager = new UserManager($pdo);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['fullName']) && isset($_POST['email']) && isset($_POST['currentPassword']) && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
        $fullname = $_POST['fullName'];
        $email = $_POST['email'];
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        echo $confirmPassword;
        echo $newPassword;
        if ($confirmPassword !== $newPassword) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Şifreler Eşleşmiyor!</div>";
            exit;
        }
        $user = $userManager->findById($_SESSION["user_id"]);
        if (!$user) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Lütfen Giriş Yapın!</div>";
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Lütfen geçerli bir email adresi girin!</div>";
            exit;
        }
        if ($userManager->findByEmail($email) && $email !== $user['email']) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Bu mail başka bir kullanıcı tarafından kullanılıyor!</div>";
            exit;
        }
        if (strlen($newPassword) < 8) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Lütfen şifre uzunluğunuzun 8 den uzun olduğuna emin olun!</div>";
            exit;
        }
        if (!password_verify($currentPassword, $user['password'])) {
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Lütfen mevcut şifrenizi düzgün girdiğinizden emin olun!</div>";
            exit;
        }
        $pdo->beginTransaction();

        $result = $userManager->update($user['id'], $fullname, $email, $newPassword);
        if ($result) {
            $pdo->commit();
            echo "<div class='alert alert-success text-center py-2' role='alert'>Bilgileriniz Başarıyla Güncellendi!</div>";
            echo "<script>setTimeout(() => location.href = '/profile.php', 1000)</script>";
            exit;
        } else {
            $pdo->rollBack();
            echo "<div class='alert alert-danger text-center py-2' role='alert'>Bilinmeyen Hata</div>";
            exit;
        }
    }

}
?>