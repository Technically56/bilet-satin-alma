<?php
require_once('idatlas/idatlas.php');
session_start(options: [
    'cookie_path' => '/',
    'cookie_lifetime' => 3600,
    'cookie_secure' => false,
    'cookie_httponly' => true,
    'cookie_samesite' => 'lax',
]);
function renderCouponBox($boxtitle, array $coupons, string $company_name): string
{
    ob_start(); ?>
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?php echo htmlspecialchars($boxtitle); ?></h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCouponModal">
                Kupon Ekle
            </button>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <input type="hidden" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
                            id="csrf_token" name="csrf_token">
                        <thead>
                            <tr>
                                <th>Kod</th>
                                <th>Firma</th>
                                <th>İndirim</th>
                                <th>Son Kullanım Tarihi</th>
                                <th>Durum</th>
                                <th>Kalan Kullanım</th>
                                <th>Kuponları Yönet</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($coupons as $coupon):
                                $couponId = sendToAtlas($coupon['id']);
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($coupon['code']) ?></strong></td>
                                    <td><?php echo htmlspecialchars($company_name); ?></td>
                                    <td><?php echo htmlspecialchars((string) ((float) $coupon['discount']) * 100) . "%" ?></td>
                                    <td><?php $formattedExpiry = DateTime::createFromFormat("Y-m-d H:i:s", $coupon['expire_date']);
                                    echo htmlspecialchars($formattedExpiry->format("d-m-Y H:i")); ?>
                                    </td>
                                    <td><span class="badge bg-success">Aktif</span></td>
                                    <td><?php echo htmlspecialchars($coupon['usage_limit']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#updateCouponModal_<?php echo htmlspecialchars($couponId); ?>">Güncelle</button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#removeCouponModal_<?php echo htmlspecialchars($couponId); ?>">Kaldır</button>

                                    </td>
                                </tr>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Update Coupon Modal -->
        <div class="modal fade" id="updateCouponModal_<?php echo htmlspecialchars($couponId); ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Kuponu Düzenle</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" value="<?php echo htmlspecialchars($couponId); ?>" name="couponId"
                            id="coupon_<?php echo htmlspecialchars($couponId); ?>">
                        <div id="updateResultDiv_<?php echo htmlspecialchars($couponId); ?>"></div>
                        <form>
                            <input type="hidden" id="operationUpdate_<?php echo htmlspecialchars($couponId); ?>" value="update"
                                name="operation">
                            <div class="mb-3">
                                <label for="updateCouponCode_<?php echo htmlspecialchars($couponId); ?>"
                                    class="form-label fw-bold">Kupon Kodu</label>
                                <input type="text" class="form-control"
                                    id="updateCouponCode_<?php echo htmlspecialchars($couponId); ?>" name="code"
                                    value="<?php echo htmlspecialchars($coupon['code']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateDiscountValue_<?php echo htmlspecialchars($couponId); ?>"
                                    class="form-label fw-bold">İndirim Miktarı</label>
                                <input type="number" class="form-control" name="discount"
                                    id="updateDiscountValue_<?php echo htmlspecialchars($couponId); ?>"
                                    value="<?php echo htmlspecialchars((string) (((float) $coupon['discount']) * 100.0)); ?>"
                                    step="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateUsage_<?php echo htmlspecialchars($couponId); ?>"
                                    class="form-label fw-bold">Kullanım Mikatarı</label>
                                <input type="number" class="form-control" name="usage"
                                    id="updateUsage_<?php echo htmlspecialchars($couponId); ?>"
                                    value="<?php echo htmlspecialchars($coupon['usage_limit']); ?>" step="1" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateExpiryDate_<?php echo htmlspecialchars($couponId); ?>"
                                    class="form-label fw-bold">Son Kullanım Tarihi</label>
                                <input type="date" class="form-control" name="date"
                                    id="updateExpiryDate_<?php echo htmlspecialchars($couponId); ?>"
                                    value="<?php echo htmlspecialchars($formattedExpiry->format("Y-m-d")); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="updateExpiryTime_<?php echo htmlspecialchars($couponId); ?>"
                                    class="form-label fw-bold">Son Kullanım Saati</label>
                                <input type="time" class="form-control" name="time"
                                    id="updateExpiryTime_<?php echo htmlspecialchars($couponId); ?>"
                                    value="<?php echo htmlspecialchars($formattedExpiry->format("H:i")); ?>" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal Et</button>
                        <button type="button" class="btn btn-primary" hx-post="/admin/api/couponapi.php" hx-trigger="click"
                            hx-swap="innerHTML" hx-target="#updateResultDiv_<?php echo htmlspecialchars($couponId); ?>"
                            hx-include="
                            #operationUpdate_<?php echo htmlspecialchars($couponId); ?>,
                            #updateCouponCode_<?php echo htmlspecialchars($couponId); ?>,
                            #updateExpiryDate_<?php echo htmlspecialchars($couponId); ?>,
                            #updateDiscountValue_<?php echo htmlspecialchars($couponId); ?>,
                            #csrf_token,
                            #coupon_<?php echo htmlspecialchars($couponId); ?>,
                            #updateExpiryTime_<?php echo htmlspecialchars($couponId); ?>,
                            #updateUsage_<?php echo htmlspecialchars($couponId); ?>
                            ">Değişiklikleri Kaydet</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Remove Coupon Modal -->
        <div class="modal fade" id="removeCouponModal_<?php echo htmlspecialchars($couponId); ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Kuponu Kaldır</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div id="removeResultDiv_<?php echo htmlspecialchars($couponId); ?>"></div>
                        <p>Bu kuponu kaldırmak istediğinize emin misiniz?</p>
                        <p class="text-muted mb-0"><strong>Kod:</strong><?php echo htmlspecialchars(" " . $coupon['code']); ?>
                        </p>
                        <div class="alert alert-warning mt-3" role="alert">
                            <strong>Uyarı:</strong> Bu işlem geri alınamaz.
                        </div>
                        <input type="hidden" id="removeOperation_<?php echo htmlspecialchars($couponId); ?>" name="operation"
                            value="remove">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri Dön</button>
                        <button type="button" class="btn btn-danger" hx-post="/admin/api/couponapi.php" hx-swap="innerHTML"
                            hx-trigger="click"
                            hx-include="#removeOperation_<?php echo htmlspecialchars($couponId); ?>,#csrf_token,#coupon_<?php echo htmlspecialchars($couponId); ?>"
                            hx-target="#removeResultDiv_<?php echo htmlspecialchars($couponId); ?>">Kuponu
                            Kaldır</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="modal fade" id="addCouponModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Kupon Ekle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="resultAdd"></div>
                    <form>
                        <div class="mb-3">
                            <label for="couponCode" class="form-label fw-bold">Kupon Kodu</label>
                            <input type="text" class="form-control" id="couponCode" placeholder="" name="code" required>
                            <input type="hidden" name="operation" value="create" id="operation">
                        </div>
                        <div class="mb-3">
                            <label for="discountValue" class="form-label fw-bold">İndirim Yüzdesi</label>
                            <input type="number" class="form-control" id="discountValue" step="1" name="discount" required>
                        </div>
                        <div class="mb-3">
                            <label for="usageValue" class="form-label fw-bold">Kullanım Limiti</label>
                            <input type="number" class="form-control" id="discountValue" step="1" name="usage" required>
                        </div>
                        <div class="mb-3">
                            <label for="expiryDate" class="form-label fw-bold">Son Kullanım Tarihi</label>
                            <input type="date" class="form-control" id="expiryDate" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="expiryTime" class="form-label fw-bold">Son Kullanım Saati</label>
                            <input type="time" class="form-control" id="expiryTime" name="time" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri Dön</button>
                    <button type="button" class="btn btn-primary" hx-post="/admin/api/couponapi.php"
                        hx-include="#operation,#couponCode,#discountValue,#expiryDate,#expiryTime,#csrf_token"
                        hx-trigger="click" hx-swap="innerHTML" hx-target="#resultAdd">Kupon
                        Ekle</button>
                </div>
            </div>
        </div>
    </div>
    <?php return ob_get_clean();
} ?>