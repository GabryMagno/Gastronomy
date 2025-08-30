<?php

require_once "php/db.php";
require_once "php/sanitizer.php";
require_once "change-page-products.php";

$db = new DB;

$people_reservation = null;
$date_reservation = null;

//GESTIONE GENERALE

$pagina = file_get_contents("html/degustazione.html");
if(isset($_GET["degustazione"])){
    $tasting = $_GET["degustazione"];
    $tastingInfo = $db->GetTastingInfo($_GET["degustazione"]);
    IF(is_string($tastingInfo) && $tastingInfo == "Tasting not found") {
        header("Location: 404.php");
        exit();
    }elseif(is_string($tastingInfo) && $tastingInfo == "Execution error" || $tastingInfo == "Connection error") {
        header("Location: 500.php");
        exit();
    }
    $productInfo = $db->GetProductInfo($tastingInfo["id_prodotto"]);
    unset($_GET["degustazione"]);
    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[IMMAGINE]","<img src=".$productInfo["url_immagine"]." alt=Immagine del prodotto".$productInfo["nome"].">",$pagina);
    $pagina = str_replace("[DESCRIZIONE]",$tastingInfo["descrizione"],$pagina);
    $start_date = new DateTime($tastingInfo["data_inizio"]);
    $end_date = new DateTime($tastingInfo["data_fine"]);
    $pagina = str_replace("[Data_Inizio]",$start_date->format("d-m-Y"),$pagina);
    $pagina = str_replace("[Data_Fine]",$end_date->format("d-m-Y"),$pagina);
    $pagina = str_replace("[Prezzo]",$tastingInfo["prezzo"]." &euro;",$pagina);
    $pagina = str_replace("[Numero_Persone]",$tastingInfo["disponibilita_persone"],$pagina);

    $isUserLogged = $db->IsUserLog();

    if(is_bool($isUserLogged) && $isUserLogged == false){//Se l'utente non è loggato
        $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    }else{
        $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
    }
    //GESTIONE PRENOTAZIONE DEGUSTAZIONE
}else{
    header("Location: degustazioni.php");
    exit();
}

echo $pagina;

?>