<?php
require_once("includes/idatlas/idatlas.php");
function displayTripBox(string $company_name, string $companyLogoPath, string $departure_time, string $price, int $capacity, string $trip_id, array $fullseats): string
{
    $uniqueId = sendToAtlas($trip_id);
    ob_start();
    ?>
    <div class="container mt-5 trip-container">
        <div class="card">
            <div class="card-header p-0">
                <button
                    class="btn btn-primary w-100 py-4 d-flex align-items-center justify-content-between gap-3 px-4 trip-toggle"
                    type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $uniqueId; ?>">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?php echo htmlspecialchars($companyLogoPath); ?>" alt="Bus" class="rounded"
                            style="width: 80px; height: 80px;">
                        <div class="text-start">
                            <div class="fw-bold fs-4">
                                <?php echo htmlspecialchars($company_name) . htmlspecialchars($departure_time); ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold fs-3"><?php echo htmlspecialchars($price) . " TL"; ?></div>
                        <small class="d-block">koltuk başına fiyat</small>
                    </div>
                </button>
            </div>

            <div class="collapse trip-collapse" id="<?php echo $uniqueId; ?>">
                <div class="card-body">
                    <form action="checkout.php" method="GET">
                        <input type="hidden" name="trip_id" value="<?php echo htmlspecialchars($uniqueId); ?>">

                        <div class="text-center p-3 bg-light rounded mb-4">
                            <h5>Şoför</h5>
                        </div>

                        <div class="mb-4">
                            <?php
                            $totalrows = $capacity / 3;
                            echo print_r($fullseats);
                            for ($i = 0; $i < $totalrows; $i++) {
                                ?>
                                <div class="d-flex justify-content-center align-items-center mb-2">
                                    <?php if (in_array($i * 3 + 1, $fullseats)) { ?>
                                        <label class="btn btn-danger btn-sm disabled"
                                            style="width: 50px; height: 50px;"><?php echo $i * 3 + 1; ?></label>
                                    <?php } else { ?>
                                        <input type="checkbox" class="btn-check" name="seats[]" value="<?php echo $i * 3 + 1; ?>"
                                            id="<?php echo $uniqueId . '_seat_' . ($i * 3 + 1); ?>" autocomplete="off">
                                        <label class="btn btn-outline-success btn-sm"
                                            for="<?php echo $uniqueId . '_seat_' . ($i * 3 + 1); ?>"
                                            style="width: 50px; height: 50px;"><?php echo $i * 3 + 1; ?></label>
                                    <?php } ?>

                                    <?php if (in_array($i * 3 + 2, $fullseats)) { ?>
                                        <label class="btn btn-danger btn-sm disabled"
                                            style="width: 50px; height: 50px;"><?php echo $i * 3 + 2; ?></label>
                                    <?php } else { ?>
                                        <input type="checkbox" class="btn-check" name="seats[]" value="<?php echo $i * 3 + 2; ?>"
                                            id="<?php echo $uniqueId . '_seat_' . ($i * 3 + 2); ?>" autocomplete="off">
                                        <label class="btn btn-outline-success btn-sm"
                                            for="<?php echo $uniqueId . '_seat_' . ($i * 3 + 2); ?>"
                                            style="width: 50px; height: 50px;"><?php echo $i * 3 + 2; ?></label>
                                    <?php } ?>

                                    <div style="width: 30px;"></div>

                                    <?php if (in_array($i * 3 + 3, $fullseats)) { ?>
                                        <label class="btn btn-danger btn-sm disabled"
                                            style="width: 50px; height: 50px;"><?php echo $i * 3 + 3; ?></label>
                                    <?php } else { ?>
                                        <input type="checkbox" class="btn-check" name="seats[]" value="<?php echo $i * 3 + 3; ?>"
                                            id="<?php echo $uniqueId . '_seat_' . ($i * 3 + 3); ?>" autocomplete="off">
                                        <label class="btn btn-outline-success btn-sm"
                                            for="<?php echo $uniqueId . '_seat_' . ($i * 3 + 3); ?>"
                                            style="width: 50px; height: 50px;"><?php echo $i * 3 + 3; ?></label>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="d-flex justify-content-center gap-3 flex-wrap mt-4 mb-4">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-success" style="width: 30px; height: 30px;"></span>
                                <span>Available</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary" style="width: 30px; height: 30px;"></span>
                                <span>Selected</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-danger" style="width: 30px; height: 30px;"></span>
                                <span>Booked</span>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                Rezervasyon Yap
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const collapseElement = document.getElementById('<?php echo $uniqueId; ?>');

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
    <?php
    return ob_get_clean();
}
?>