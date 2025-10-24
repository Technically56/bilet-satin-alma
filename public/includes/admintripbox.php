<?php
require_once("idatlas/idatlas.php");
require_once("db/TripOperations.php");


function renderAdminTripbox(string $company_name, string $departure_time, string $arrival_time, string $price, string $from, string $to, int $capacity, string $trip_id, array $fullseats): string
{
    require("db/db.php");
    ob_start();
    $uniqueId = sendToAtlas($trip_id);
    $tripManager = new TripManager($pdo);
    $cities = $tripManager->validCities();
    ?>
    <div class="card mb-4">
        <div class="card-header p-0">
            <button class="btn btn-primary w-100 py-4 d-flex align-items-center justify-content-between gap-3 px-4"
                type="button" data-bs-toggle="collapse" data-bs-target="#trip_<?php echo htmlspecialchars($uniqueId); ?>">
                <div class="d-flex align-items-center gap-3">
                    <img src="https://via.placeholder.com/80x80/0d6efd/ffffff?text=BUS" alt="Bus" class="rounded"
                        style="width: 80px; height: 80px;">
                    <div class="text-start">
                        <div class="fw-bold fs-4"><?php echo htmlspecialchars($from . "→" . $to) ?> </div>
                        <small
                            class="d-block"><?php echo htmlspecialchars($company_name) . " • " . htmlspecialchars("Kalkış: " . $departure_time) . " • " . htmlspecialchars("Varış: " . $arrival_time); ?>
                            • Koltukları Görüntülemek
                            İçin Tıklayınız</small>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fw-bold fs-3"><?php echo htmlspecialchars($price . " TL"); ?></div>
                    <small class="d-block">koltuk başına fiyat</small>
                    <small
                        class="d-block mt-1"><?php echo htmlspecialchars((string) count($fullseats) . "/" . $capacity); ?></small>
                </div>
            </button>
        </div>

        <div class="collapse" id="trip_<?php echo htmlspecialchars($uniqueId); ?>">
            <div class="card-body">
                <div class="text-center p-3 bg-light rounded mb-4">
                    <h5>Şoför</h5>
                </div>

                <div class="mb-4">
                    <?php
                    $totalrows = $capacity / 3;
                    for ($i = 0; $i < $totalrows; $i++) {
                        ?>
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <?php
                            if (in_array($i * 3 + 1, $fullseats)) { ?>
                                <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">
                                    <?= $i * 3 + 1 ?>
                                </label>
                            <?php } else { ?>
                                <label class="btn btn-outline-success btn-sm" style="width: 50px; height: 50px;">
                                    <?= $i * 3 + 1 ?>
                                </label>
                            <?php } ?>

                            <?php

                            if (in_array($i * 3 + 2, $fullseats)) { ?>
                                <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">
                                    <?= $i * 3 + 2 ?>
                                </label>
                            <?php } else { ?>
                                <label class="btn btn-outline-success btn-sm" style="width: 50px; height: 50px;">
                                    <?= $i * 3 + 2 ?>
                                </label>
                            <?php } ?>

                            <div style="width: 30px;"></div>

                            <?php

                            if (in_array($i * 3 + 3, $fullseats)) { ?>
                                <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">
                                    <?= $i * 3 + 3 ?>
                                </label>
                            <?php } else { ?>
                                <label class="btn btn-outline-success btn-sm" style="width: 50px; height: 50px;">
                                    <?= $i * 3 + 3 ?>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#updateTripModal_<?php echo htmlspecialchars($uniqueId); ?>">
                        Seferi Güncelle
                    </button>
                    <button class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#removeTripModal_<?php echo htmlspecialchars($uniqueId); ?>">
                        Seferi Sil
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateTripModal_<?php echo htmlspecialchars($uniqueId); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Seferi Düzenle</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="updateResultDiv_<?php echo htmlspecialchars($uniqueId); ?>"></div>
                    <form>
                        <input type="hidden" name="tripId" id="updateTrip_<?php echo htmlspecialchars($uniqueId); ?>"
                            value="<?php echo htmlspecialchars($uniqueId); ?>">
                        <input type="hidden" name="operation" id="updateOp_<?php echo htmlspecialchars($uniqueId); ?>"
                            value="update">
                        <input type="hidden" name="csrf_token" id="csrf_token_<?php echo htmlspecialchars($uniqueId); ?>"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="updateDeparture_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">
                                    <i class="bi bi-geo-alt-fill text-primary"></i> Kalkış Şehri
                                </label>
                                <select class="form-select form-select-lg"
                                    id="updateDeparture_<?php echo htmlspecialchars($uniqueId); ?>" name="from" required>
                                    <option value="" selected disabled>Şehir Seçin</option>
                                    <?php

                                    foreach ($cities as $city) {
                                        echo "<option value=\"$city\">$city</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="updateArrival_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">
                                    <i class="bi bi-geo-alt-fill text-primary"></i>Varış Şehri
                                </label>
                                <select class="form-select form-select-lg"
                                    id="updateArrival_<?php echo htmlspecialchars($uniqueId); ?>" name="to" required>
                                    <option value="" selected disabled>Şehir Seçin</option>
                                    <?php
                                    $cities = $tripManager->validCities();
                                    foreach ($cities as $city) {
                                        echo "<option value=\"$city\">$city</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="updateDepartureDate_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">
                                    Kalkış Tarihi</label>
                                <input type="date" class="form-control" name="departureDate"
                                    id="updateDepartureDate_<?php echo htmlspecialchars($uniqueId); ?>"
                                    value="<?php echo htmlspecialchars(date("Y-m-d")); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="updateDepartureTime_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">Kalkış Saati Saat</label>
                                <input type="time" class="form-control" name="departureTime"
                                    id="updateDepartureTime_<?php echo htmlspecialchars($uniqueId); ?>"
                                    value="<?php echo htmlspecialchars(date("H:i")); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="updatePrice_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">Fiyat</label>
                                <input type="number" class="form-control" name="price"
                                    id="updatePrice_<?php echo htmlspecialchars($uniqueId); ?>"
                                    value="<?php echo htmlspecialchars($price); ?>" step="1" required>
                            </div>
                            <div class="col-md-4">
                                <label for="updateArrivalDate_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">Varış Tarihi</label>
                                <input type="date" class="form-control" name="arrivalDate"
                                    id="updateArrivalDate_<?php echo htmlspecialchars($uniqueId); ?>"
                                    value="<?php echo htmlspecialchars(date("Y-m-d")); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="updateArrivalTime_<?php echo htmlspecialchars($uniqueId); ?>"
                                    class="form-label fw-bold">Varış Saati</label>
                                <input type="time" class="form-control" name="arrivalTime"
                                    id="updateArrivalTime_<?php echo htmlspecialchars($uniqueId); ?>"
                                    value="<?php echo htmlspecialchars(date("H:i")); ?>" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri Dön</button>
                    <button type="button" class="btn btn-primary" hx-post="/admin/api/companyapi.php" hx-trigger="click"
                        hx-target="#updateResultDiv_<?php echo htmlspecialchars($uniqueId);

                        ?>" hx-include="#updateTrip_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateOp_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateDeparture_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateArrival_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateTripDate_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateDepartureDate_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateDepartureTime_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updatePrice_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateArrivalDate_<?php echo htmlspecialchars($uniqueId); ?>,
                        #updateArrivalTime_<?php echo htmlspecialchars($uniqueId); ?>,
                        #csrf_token_<?php echo htmlspecialchars($uniqueId); ?>
                        ">Değişiklikleri
                        Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeTripModal_<?php echo htmlspecialchars($uniqueId); ?>" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Seferi Sil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="removeTripId_<?php echo htmlspecialchars($uniqueId); ?>" name="tripId"
                            value="<?php echo htmlspecialchars($uniqueId); ?>">
                        <input type="hidden" id="removeTripOp_<?php echo htmlspecialchars($uniqueId); ?>" name="operation"
                            value="remove">
                        <div id="removeResultDiv_<?php echo htmlspecialchars($uniqueId); ?>"></div>
                        <p>Bu Seferi Silmek İstediğinize Emin Misiniz?</p>
                        <div class="alert alert-warning" role="alert">
                            <strong>UYARI:</strong> Bu işlem geri alınamaz! Sefer kalkış tarihinden önce yapılan silmeler
                            için
                            tüm bilet sahiplerine ücret iadesi yapılır!
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Geri Dön</button>
                    <button type="button" hx-post="/admin/api/companyapi.php" hx-swap="innerHTML"
                        hx-target="#removeResultDiv_<?php echo htmlspecialchars($uniqueId); ?>"
                        hx-include="#removeTripId_<?php echo htmlspecialchars($uniqueId); ?>, #removeTripOp_<?php echo htmlspecialchars($uniqueId); ?>, #csrf_token_<?php echo htmlspecialchars($uniqueId); ?>"
                        hx-trigger="click" class="btn btn-danger">
                        Seferi Sil
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        (function () {
            const collapseElement = document.getElementById('trip_<?php echo $uniqueId; ?>');

            collapseElement.addEventListener('show.bs.collapse', function () {

                document.querySelectorAll('.trip-collapse').forEach(function (element) {
                    if (element.id !== '<?php echo $uniqueId; ?>' && element.classList.contains('show')) {
                        const bsCollapse = bootstrap.Collapse.getInstance(element);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        } else {
                            new bootstrap.Collapse(element, { toggle: false }).hide();
                        }
                    }
                });
            });
        })();
    </script>
    <?php return ob_get_clean();
} ?>