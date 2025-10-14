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
            require_once 'includes/db/CompanyOperations.php';

            $companyManager = new CompanyManager($pdo);;
            $tripManager = new TripManager($pdo);

            if($_SERVER['REQUEST_METHOD'] === 'POST'){
                if (!empty($_POST['from']) && !empty($_POST['to']) && !empty($_POST['date'])) {
                    $from = $_POST['from'];
                    $to = $_POST['to'];
                    $date = $_POST['date'];
                    $datetime= DateTime::createFromFormat('Y-m-d H:i:s', $date);
                    if($tripManager->isValidCity($from) && $tripManager->isValidCity($to)){
                        if($datetime && $datetime->format('Y-m-d H:i:s') === $date){
                            $trips = $tripManager->getTripsByCities($from, $to, $date);
                            foreach($trips as $trip){
                                $bookedseats = $tripManager->getBookedSeats($trip['id']);
                                $company = $companyManager->findById($trip['company_id']);
                                displayTripBox($company['name'],$trip['logo_path'],$trip['price'],$trip['capacity'],$bookedseats);
                            }
                           
}}}}
                
            
            
            
            
            
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        ?>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
        <?php }?>    
</body>
</html>