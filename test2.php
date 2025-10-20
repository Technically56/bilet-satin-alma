<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    include("includes/db/TripOperations.php");
    include("includes/db/db.php");
    include("includes/db/UserOperations.php");
    $userManager = new UserManager($pdo);
    $tripManager = new TripManager($pdo);
    session_start();
    echo "validcity" . $tripManager->isValidCity("asd") . "<br>";
    $trips = $tripManager->getTripsByCities("Ankara", "Istanbul", "2025-10-20 10:00:00");
    echo "<pre>";
    echo "<br>Trips:<br>";
    print_r($trips);
    echo "</pre>";
    echo $_SESSION["user_id"];
    echo "<br>";
    echo $_SESSION["user_role"];
    echo "<br>";
    echo $_SESSION["user_fullname"];
    echo "<br>";
    echo $userManager->getBalance($_SESSION["user_id"]);
    ?>
</body>

</html>