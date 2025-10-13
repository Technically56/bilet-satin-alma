<?php 
function displayTripBox(string $company_name, string $companyLogoPath, string $price, int $capacity, array $fullseats): string {
    ob_start();
    ?>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header p-0">
                <button class="btn btn-primary w-100 py-4 d-flex align-items-center justify-content-between gap-3 px-4" type="button" data-bs-toggle="collapse" data-bs-target="#busSeats">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?php echo htmlspecialchars($companyLogoPath); ?>" alt="Bus" class="rounded" style="width: 80px; height: 80px;">
                        <div class="text-start">
                            <div class="fw-bold fs-4"><?php echo htmlspecialchars($company_name); ?></div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold fs-3"><?php echo htmlspecialchars($price) . " TL"; ?></div>
                        <small class="d-block">koltuk başına fiyat</small>
                    </div>
                </button>
            </div>
            
            <div class="collapse show" id="busSeats">
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
                                <?php if (in_array($i * 3 + 1, $fullseats)) { ?>
                                    <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;"><?php echo $i * 3 + 1; ?></label>
                                <?php } else { ?> 
                                    <input type="checkbox" class="btn-check" id="seat<?php echo $i * 3 + 1; ?>" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="seat<?php echo $i * 3 + 1; ?>" style="width: 50px; height: 50px;"><?php echo $i * 3 + 1; ?></label>
                                <?php } ?>

                                <?php if (in_array($i * 3 + 2, $fullseats)) { ?>
                                    <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;"><?php echo $i * 3 + 2; ?></label>
                                <?php } else { ?> 
                                    <input type="checkbox" class="btn-check" id="seat<?php echo $i * 3 + 2; ?>" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="seat<?php echo $i * 3 + 2; ?>" style="width: 50px; height: 50px;"><?php echo $i * 3 + 2; ?></label>
                                <?php } ?>
                                
                                <div style="width: 30px;"></div>
                                
                                <?php if (in_array($i * 3 + 3, $fullseats)) { ?>
                                    <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;"><?php echo $i * 3 + 3; ?></label>
                                <?php } else { ?> 
                                    <input type="checkbox" class="btn-check" id="seat<?php echo $i * 3 + 3; ?>" autocomplete="off">
                                    <label class="btn btn-outline-success btn-sm" for="seat<?php echo $i * 3 + 3; ?>" style="width: 50px; height: 50px;"><?php echo $i * 3 + 3; ?></label>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
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
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>