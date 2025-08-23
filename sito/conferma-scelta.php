<?php

require_once "php/db.php";//

$db = new DB;

$pagina = file_get_contents("html/conferma-scelta.html");
$isUserLogged=$db->isUserLog();

if($isUserLogged){
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
}elseif (is_string($userInfo) && (strcmp($userInfo,"Execution error")==0 || strcmp($userInfo,"User not found")==0 || strcmp($userInfo,"Connection error")==0)) {//se c'è un errore nell'ottenere le informazioni dell'utente, reindirizza alla pagina di errore
    header('Location: 500.php');
    exit();
} else if(is_string($userInfo) && strcmp($userInfo,"User Is not logged")==0) {
    header('Location: login.php');
    exit();
}

echo $pagina;

?>