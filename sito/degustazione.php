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
}
$isUserLogged = $db->IsUserLog();

if(is_bool($isUserLogged) && $isUserLogged == false){//Se l'utente non è loggato
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
}else{
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
}

echo $pagina;

?>