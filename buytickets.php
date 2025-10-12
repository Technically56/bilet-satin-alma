<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php session_start(); 
        if($_SESSION['user_id'] != '' && $_SESSION['role'] != ''){?>
        <?php
            require_once 'includes/db/db.php';
            require_once 'includes/db/TripOperations.php';
            $tripManager = new TripManager($pdo);

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                if (!empty($_POST['from']) && !empty($_POST['to']) && !empty($_POST['date'])) {
                    $from = $_POST['from'];
                    $to = $_POST['to'];
                    $date = $_POST['date'];
                    $datetime= DateTime::createFromFormat('Y-m-d H:i:s', $date);
                    if(tripManager->isValidCity($from) && tripManager->isValidCity($to)){
                        if($datetime && $datetime->format('Y-m-d H:i:s') === $date){
                            $trips = $tripManager->getTripsByCities($from, $to, $date);
                            // Sonuç gösterme işlemini yap
                            /*
                            <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Selector</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#busSeats">
                    <span class="fw-bold">🚌 Bus Seat Selection (2+1 Configuration)</span>
                    <small class="d-block">Click to expand/collapse</small>
                </button>
            </div>
            
            <div class="collapse show" id="busSeats">
                <div class="card-body">
                    <div class="text-center p-3 bg-light rounded mb-4">
                        <h5>🚗 Driver</h5>
                    </div>
                    
                    <div class="mb-4">
                        <!-- Row 1 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat1" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat1" style="width: 50px; height: 50px;">1</label>
                            <input type="checkbox" class="btn-check" id="seat2" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat2" style="width: 50px; height: 50px;">2</label>
                            <div style="width: 30px;"></div>
                            <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">3</label>
                        </div>
                        
                        <!-- Row 2 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat4" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat4" style="width: 50px; height: 50px;">4</label>
                            <input type="checkbox" class="btn-check" id="seat5" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat5" style="width: 50px; height: 50px;">5</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat6" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat6" style="width: 50px; height: 50px;">6</label>
                        </div>
                        
                        <!-- Row 3 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">7</label>
                            <input type="checkbox" class="btn-check" id="seat8" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat8" style="width: 50px; height: 50px;">8</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat9" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat9" style="width: 50px; height: 50px;">9</label>
                        </div>
                        
                        <!-- Row 4 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat10" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat10" style="width: 50px; height: 50px;">10</label>
                            <input type="checkbox" class="btn-check" id="seat11" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat11" style="width: 50px; height: 50px;">11</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat12" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat12" style="width: 50px; height: 50px;">12</label>
                        </div>
                        
                        <!-- Row 5 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat13" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat13" style="width: 50px; height: 50px;">13</label>
                            <input type="checkbox" class="btn-check" id="seat14" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat14" style="width: 50px; height: 50px;">14</label>
                            <div style="width: 30px;"></div>
                            <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">15</label>
                        </div>
                        
                        <!-- Row 6 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat16" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat16" style="width: 50px; height: 50px;">16</label>
                            <input type="checkbox" class="btn-check" id="seat17" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat17" style="width: 50px; height: 50px;">17</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat18" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat18" style="width: 50px; height: 50px;">18</label>
                        </div>
                        
                        <!-- Row 7 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat19" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat19" style="width: 50px; height: 50px;">19</label>
                            <input type="checkbox" class="btn-check" id="seat20" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat20" style="width: 50px; height: 50px;">20</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat21" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat21" style="width: 50px; height: 50px;">21</label>
                        </div>
                        
                        <!-- Row 8 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">22</label>
                            <input type="checkbox" class="btn-check" id="seat23" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat23" style="width: 50px; height: 50px;">23</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat24" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat24" style="width: 50px; height: 50px;">24</label>
                        </div>
                        
                        <!-- Row 9 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat25" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat25" style="width: 50px; height: 50px;">25</label>
                            <input type="checkbox" class="btn-check" id="seat26" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat26" style="width: 50px; height: 50px;">26</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat27" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat27" style="width: 50px; height: 50px;">27</label>
                        </div>
                        
                        <!-- Row 10 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <label class="btn btn-danger btn-sm disabled" style="width: 50px; height: 50px;">28</label>
                            <input type="checkbox" class="btn-check" id="seat29" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat29" style="width: 50px; height: 50px;">29</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat30" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat30" style="width: 50px; height: 50px;">30</label>
                        </div>
                        
                        <!-- Row 11 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat31" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat31" style="width: 50px; height: 50px;">31</label>
                            <input type="checkbox" class="btn-check" id="seat32" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat32" style="width: 50px; height: 50px;">32</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat33" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat33" style="width: 50px; height: 50px;">33</label>
                        </div>
                        
                        <!-- Row 12 -->
                        <div class="d-flex justify-content-center align-items-center mb-2">
                            <input type="checkbox" class="btn-check" id="seat34" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat34" style="width: 50px; height: 50px;">34</label>
                            <input type="checkbox" class="btn-check" id="seat35" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm ms-1" for="seat35" style="width: 50px; height: 50px;">35</label>
                            <div style="width: 30px;"></div>
                            <input type="checkbox" class="btn-check" id="seat36" autocomplete="off">
                            <label class="btn btn-outline-success btn-sm" for="seat36" style="width: 50px; height: 50px;">36</label>
                        </div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                        */}}}}
                
            
            
            
            
            
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        ?>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
        <?php }?>    
</body>
</html>