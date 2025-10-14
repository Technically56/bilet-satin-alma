<?php
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
    }
    function sendToAtlas(string $id_to_mask):string {
        if(!isset($_SESSION["ID_ATLAS"])){
            $_SESSION["ID_ATLAS"] = [];
        }
        $key = array_search($id_to_mask, $_SESSION["ID_ATLAS"]);
        if($key !== false){
            unset($_SESSION["ID_ATLAS"][$key]);
        }
        $display_id = "id_" . bin2hex(random_bytes(8));
        $_SESSION["ID_ATLAS"][$display_id] = $id_to_mask;
        return $display_id;
    }
    function getFromAtlas(string $display_id):string {
        return $_SESSION["ID_ATLAS"][$display_id] ?? '';
    }


?>