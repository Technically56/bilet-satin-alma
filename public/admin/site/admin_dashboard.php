<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>

<body class="bg-light">
    <?php include("../../includes/navbar.php"); ?>
    <?php
    session_start();
    require_once("../../includes/couponbox.php");
    require_once('../../includes/db/db.php');
    require_once('../../includes/db/CompanyOperations.php');
    require_once('../../includes/db/UserOperations.php');
    require_once('../../includes/idatlas/idatlas.php');
    require_once('../../includes/db/TicketOperations.php');
    require_once('../../includes/db/TripOperations.php');
    require_once('../../includes/db/PaymentOperations.php');

    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        http_response_code(404);
        include('../../notfound.php');
        exit;
    }

    $companyManager = new CompanyManager($pdo);
    $userManager = new UserManager($pdo);
    $ticketManager = new TicketManager($pdo);
    $tripManager = new TripManager($pdo);
    $paymentManager = new PaymentManager($pdo, $userManager, $ticketManager, $tripManager);
    ?>

    <div class="container-fluid">
        <div class="p-4">
            <div id="dashboard-section">
                <h2 class="mb-4">Dashboard</h2>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card bg-primary text-white text-center">
                            <div class="card-body">
                                <h3><?php echo count($companyManager->findAll()); ?></h3>
                                <p class="mb-0">Şirket</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white text-center">
                            <div class="card-body">
                                <h3><?php echo count($userManager->findAll()); ?></h3>
                                <p class="mb-0">Kullanıcı</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="companies-section">
                    <div id="delResultDiv"></div>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Şirket Yönetimi</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#companyModal">
                            Yeni Şirket
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="companies-table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Şirket Adı</th>
                                            <th>Logo</th>
                                            <th>Oluşturulma Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $companies = $companyManager->findAll();
                                        $maskedCompanies = array();
                                        foreach ($companies as $company) {
                                            $maskedId = sendToAtlas($company['id']);
                                            $maskedCompanies[$company['id']] = $maskedId;
                                            echo '<tr>
                                            <td>' . $maskedId . '</td>
                                            <td>' . htmlspecialchars($company['name']) . '</td>
                                            <td>' . ($company['logo_path'] ?: 'Yok') . '</td>
                                            <td>' . date('d.m.Y', strtotime($company['created_at'])) . '</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm me-2" onclick="editCompany(\'' . $maskedId . '\', \'' . htmlspecialchars($company['name'], ENT_QUOTES) . '\', \'' . htmlspecialchars($company['logo_path'], ENT_QUOTES) . '\')">
                                                    Düzenle
                                                </button>
                                                <button class="btn btn-danger btn-sm" hx-post="/admin/api/siteadminapi.php" 
                                                        hx-vals=\'{"operation": "delete_company", "id": "' . $maskedId . '"}\'
                                                        hx-confirm="Bu şirketi silmek istediğinizden emin misiniz?"
                                                        hx-target="#delResultDiv" hx-swap="innerHTML">
                                                    Sil
                                                </button>
                                            </td>
                                        </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="users-section">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Kullanıcı Yönetimi</h2>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#userModal">
                            Yeni Kullanıcı
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="users-table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ad Soyad</th>
                                            <th>E-posta</th>
                                            <th>Rol</th>
                                            <th>Bakiye</th>
                                            <th>Şirket</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $users = $userManager->findAll();
                                        foreach ($users as $user) {
                                            $maskedUserId = sendToAtlas($user['id']);
                                            $maskedCompanyId = '';
                                            $companyName = 'Yok';
                                            if ($user['company_id'] !== null) {
                                                $company = $companyManager->findById($user['company_id']);
                                                $maskedCompanyId = $maskedCompanies[$company['id']];
                                                $companyName = $company['name'];
                                            }
                                            $roleClass = $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'company' ? 'warning' : 'primary');

                                            echo '<tr>
                                            <td>' . $maskedUserId . '</td>
                                            <td>' . htmlspecialchars($user['full_name']) . '</td>
                                            <td>' . htmlspecialchars($user['email']) . '</td>
                                            <td><span class="badge bg-' . $roleClass . '">' . $user['role'] . '</span></td>
                                            <td>' . $user['balance'] . ' TL</td>
                                            <td>' . htmlspecialchars($companyName) . '</td>
                                            <td>
                                                <button class="btn btn-warning btn-sm me-2" onclick="editUser(\'' . $maskedUserId . '\', \'' . htmlspecialchars($user['full_name'], ENT_QUOTES) . '\', \'' . htmlspecialchars($user['email'], ENT_QUOTES) . '\', \'' . $user['role'] . '\', \'' . $maskedCompanyId . '\')">
                                                    Düzenle
                                                </button>
                                                <button class="btn btn-danger btn-sm" hx-post="/admin/api/siteadminapi.php" 
                                                        hx-vals=\'{"operation": "delete_user", "id": "' . $maskedUserId . '"}\'
                                                        hx-confirm="Bu kullanıcıyı silmek istediğinizden emin misiniz?"
                                                        hx-target="body" hx-swap="outerHTML">
                                                    Sil
                                                </button>
                                            </td>
                                        </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $loggedInUser = $userManager->findById($_SESSION['user_id']);
        $couponCompany = $companyManager->findById($loggedInUser['company_id']);
        $coupons = $paymentManager->getCouponsByCompany($couponCompany['id'] ?? NULL);
        echo renderCouponBox("Yönetici Kuponları", $coupons, $couponCompany['name'] ?? 'Yok');
        ?>
        <div class="modal fade" id="companyModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="companyModalTitle">Şirket Ekle/Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="companyResultDiv"></div>
                        <form id="companyForm">
                            <input type="hidden" id="companyId" name="id">
                            <div class="mb-3">
                                <label for="companyName" class="form-label">Şirket Adı</label>
                                <input type="text" class="form-control" id="companyName" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="companyLogo" class="form-label">Logo Yolu</label>
                                <input type="text" class="form-control" id="companyLogo" name="logo_path">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="saveCompanyBtn"
                            hx-post="/admin/api/siteadminapi.php" hx-target="#companyResultDiv" hx-swap="innerHTML"
                            hx-include="#companyForm">
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="userModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalTitle">Kullanıcı Ekle/Düzenle</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="userResultDiv"></div>
                        <form id="userForm">
                            <input type="hidden" id="userId" name="id">
                            <div class="mb-3">
                                <label for="userFullName" class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" id="userFullName" name="full_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="userEmail" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="userPassword" name="password">
                            </div>
                            <div class="mb-3">
                                <label for="userRole" class="form-label">Rol</label>
                                <select class="form-select" id="userRole" name="role" required>
                                    <option value="user">Kullanıcı</option>
                                    <option value="company">Şirket</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="userCompany" class="form-label">Şirket</label>
                                <select class="form-select" id="userCompany" name="company_id">
                                    <option value="">Şirket Seçin</option>
                                    <?php
                                    foreach ($companies as $company) {
                                        $maskedCompanyId = $maskedCompanies[$company['id']];
                                        echo '<option value="' . $maskedCompanyId . '">' . htmlspecialchars($company['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="saveUserBtn"
                            hx-post="/admin/api/siteadminapi.php" hx-target="#userResultDiv" hx-swap="innerHTML"
                            hx-include="#userForm">
                            Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
        <script>
            function editCompany(id, name, logoPath) {
                document.getElementById('companyModalTitle').textContent = 'Şirket Düzenle';
                document.getElementById('companyId').value = id;
                document.getElementById('companyName').value = name;
                document.getElementById('companyLogo').value = logoPath;

                // Update HTMX request for edit - only set operation, let HTMX read form values
                document.getElementById('saveCompanyBtn').setAttribute('hx-vals', JSON.stringify({
                    operation: 'update_company'
                }));

                new bootstrap.Modal(document.getElementById('companyModal')).show();
            }

            function editUser(id, fullName, email, role, companyId) {
                document.getElementById('userModalTitle').textContent = 'Kullanıcı Düzenle';
                document.getElementById('userId').value = id;
                document.getElementById('userFullName').value = fullName;
                document.getElementById('userEmail').value = email;
                document.getElementById('userRole').value = role;
                document.getElementById('userCompany').value = companyId;

                // Update HTMX request for edit - only set operation, let HTMX read form values
                document.getElementById('saveUserBtn').setAttribute('hx-vals', JSON.stringify({
                    operation: 'update_user'
                }));

                new bootstrap.Modal(document.getElementById('userModal')).show();
            }

            // Reset modals when closed
            document.getElementById('companyModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('companyForm').reset();
                document.getElementById('companyModalTitle').textContent = 'Şirket Ekle/Düzenle';
                document.getElementById('saveCompanyBtn').setAttribute('hx-vals', JSON.stringify({
                    operation: 'create_company'
                }));
            });

            document.getElementById('userModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('userForm').reset();
                document.getElementById('userModalTitle').textContent = 'Kullanıcı Ekle/Düzenle';
                document.getElementById('saveUserBtn').setAttribute('hx-vals', JSON.stringify({
                    operation: 'create_user'
                }));
            });

            // Set initial HTMX values
            document.getElementById('saveCompanyBtn').setAttribute('hx-vals', JSON.stringify({
                operation: 'create_company'
            }));

            document.getElementById('saveUserBtn').setAttribute('hx-vals', JSON.stringify({
                operation: 'create_user'
            }));
        </script>
        <?php include("../../includes/footer.php"); ?>
</body>

</html>