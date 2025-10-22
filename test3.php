<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once("includes/admintripbox.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/htmx.org@1.9.12"></script>
</head>

<body>
    <?php echo renderAdminTripbox("pamukkale", "20-10-2025 10:00", "20-10-2025 22:00", "150", "Ankara", "Istanbul", 37, "5e078dd1-3520-4b8f-a6c3-209d8b8a8907", [1, 2, 5, 6]);
    echo renderAdminTripbox("pamukkale", "20-10-2025 10:00", "20-10-2025 22:00", "150", "Ankara", "Istanbul", 37, "5e078dd1-3520-4b8f-a6c3-209d8b8a8907", [1, 2, 5, 6]);
    echo password_hash("test", PASSWORD_DEFAULT);
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>