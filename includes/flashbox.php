<?php 
    if(!empty($_SESSION['flash_message'])){
        $message = htmlspecialchars(string: $_SESSION['flash_message']);
        if($_SESSION['alert_type'] === 'success') {
            echo "<div class='alert alert-success text-center py-2' role='alert'>$message</div>";
            unset($_SESSION['flash_message']);
            unset($_SESSION['alert_type']);
        }
        else {
        echo "<div class='alert alert-danger text-center py-2' role='alert'>$message</div>";
        unset($_SESSION['flash_message']);
        unset($_SESSION['alert_type']);
    }
}
?>