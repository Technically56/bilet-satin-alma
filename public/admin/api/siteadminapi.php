<?php
session_start();
require_once('../../includes/db/db.php');
require_once('../../includes/db/CompanyOperations.php');
require_once('../../includes/db/UserOperations.php');
require_once('../../includes/idatlas/idatlas.php');


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo '<div class="alert alert-danger">Unauthorized access</div>';
    exit;
}


$companyManager = new CompanyManager($pdo);
$userManager = new UserManager($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$operation = $_POST['operation'] ?? '';

if ($method === 'POST') {
    switch ($operation) {
        case 'create_company':
            $name = $_POST['name'] ?? '';
            $logo_path = $_POST['logo_path'] ?? '';

            if (empty($name)) {
                echo '<div class="alert alert-danger">Şirket adı gereklidir!</div>';
                exit;
            }

            $result = $companyManager->create($name, $logo_path);
            if ($result) {
                echo '<div class="alert alert-success">Şirket başarıyla oluşturuldu!</div>';
                echo '<script>setTimeout(() => location.reload(), 1000)</script>';
                exit;
            } else {
                echo '<div class="alert alert-danger">Şirket oluşturulamadı!</div>';
                exit;
            }

            break;

        case 'update_company':
            $maskedId = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $logo_path = $_POST['logo_path'] ?? '';
            $id = getFromAtlas($_POST['id']);

            if (empty($maskedId) || empty($name)) {
                echo '<div class="alert alert-danger">Şirket ID ve adı gereklidir!</div>';
                exit;
            }

            $result = $companyManager->update($id, $name, $logo_path);
            if ($result) {
                echo $maskedId;
                echo '<br>';
                echo $id;
                echo '<br>';
                echo $name;
                echo '<br>';
                echo $logo_path;
                echo '<div class="alert alert-success">Şirket başarıyla güncellendi!</div>';
                echo '<script>setTimeout(() => location.reload(), 1000)</script>';
                exit;
            } else {
                echo '<div class="alert alert-danger">Şirket güncellenemedi!</div>';
                exit;
            }
            break;

        case 'delete_company':
            $maskedId = $_POST['id'] ?? '';
            $id = getFromAtlas($maskedId);

            if (empty($maskedId)) {
                echo '<div class="alert alert-danger">Şirket ID gereklidir!</div>';
                exit;
            }

            $result = $companyManager->delete($id);
            if ($result) {
                echo '<div class="alert alert-success">Şirket başarıyla silindi!</div>';
                echo '<script>setTimeout(() => location.reload(), 1000)</script>';
                exit;
            } else {
                echo '<div class="alert alert-danger">Şirket silinemedi!</div>';
                exit;
            }

            break;

        case 'create_user':
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $maskedCompanyId = $_POST['company_id'] ?? null;
            $company_id = $maskedCompanyId ? getFromAtlas($maskedCompanyId) : null;

            if (empty($full_name) || empty($email) || empty($password)) {
                echo '<div class="alert alert-danger">Ad soyad, e-posta ve şifre gereklidir!</div>';
                exit;
            }

            $result = $userManager->create($full_name, $email, $password, $role);
            if ($result && $company_id) {
                $userManager->updateCompany($userManager->findByEmail($email)['id'], $company_id);
            }

            if ($result) {
                echo '<div class="alert alert-success">Kullanıcı başarıyla oluşturuldu!</div>';
                echo '<script>setTimeout(() => location.reload(), 1000)</script>';
            } else {
                echo '<div class="alert alert-danger">Kullanıcı oluşturulamadı!</div>';
            }
            break;

        case 'update_user':
            $maskedId = $_POST['id'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $maskedCompanyId = $_POST['company_id'] ?? null;
            $company_id = $maskedCompanyId ? getFromAtlas($maskedCompanyId) : null;
            $id = getFromAtlas($maskedId);

            if (empty($maskedId) || empty($full_name) || empty($email)) {
                echo '<div class="alert alert-danger">ID, ad soyad ve e-posta gereklidir!</div>';
                return;
            }


            if (!empty($password)) {
                $result = $userManager->update($id, $full_name, $email, $password);
            } else {

                $user = $userManager->findById($id);
                $result = $userManager->update($id, $full_name, $email, $user['password']);
            }

            if ($result && $role) {
                $userManager->updateRole($id, $role);
            }
            if ($result && $company_id) {
                $userManager->updateCompany($id, $company_id);
            }

            if ($result) {
                echo '<div class="alert alert-success">Kullanıcı başarıyla güncellendi!</div>';
                echo '<script>setTimeout(() => location.reload(), 1000)</script>';
            } else {
                echo '<div class="alert alert-danger">Kullanıcı güncellenemedi!</div>';
            }
            break;

        case 'delete_user':
            $maskedId = $_POST['id'] ?? '';
            $id = getFromAtlas($maskedId);

            if (empty($maskedId)) {
                echo '<div class="alert alert-danger">Kullanıcı ID gereklidir!</div>';
                return;
            }

            $result = $userManager->delete($id);
            if ($result) {
                echo '<div class="alert alert-success">Kullanıcı başarıyla silindi!</div>';
                echo '<script>setTimeout(() => location.reload(), 1000)</script>';
            } else {
                echo '<div class="alert alert-danger">Kullanıcı silinemedi!</div>';
            }
            break;

        default:
            echo '<div class="alert alert-danger">Geçersiz işlem!</div>';
            break;
    }
} else {
    echo '<div class="alert alert-danger">Geçersiz istek!</div>';
}
?>