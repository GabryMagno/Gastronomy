<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/user-profile.html");

$isLogged_first = $db->isUserLog(); //controllo se l'utente è loggato
$isLogged = $db->UserUsername(); //recupero l'username dell'utente loggato

if ((is_bool($isLogged_first) && $isLogged_first == false)||(is_bool($isLogged) && $isLogged == false)) {//controllo se l'utente non è loggato(sia con id che con username)
    header('Location: login.php');
    exit();
}

$userInfo = $db->GetUserInfo(); //recupero le informazioni dell'utente loggato

if (is_string($userInfo) && (strcmp($userInfo,"Execution error") == 0 || strcmp($userInfo,"Connection error") == 0)) {
    header('Location: 500.php');
    exit();
} elseif (is_string($userInfo) && (strcmp($userInfo,"User is not logged") == 0 || strcmp($userInfo,"User not found") == 0)) {
    header('Location: login.php');
    exit();
}

//Sostituzione di eventuali Nome e Nome Cognome nella pagina
$pagina = str_replace("[Nome]",$userInfo['nome'],$pagina);
$pagina = str_replace("[Nome Cognome]",$userInfo['nome']." ".$userInfo['cognome'],$pagina);

// Eta dell'utente autenticato
$eta = new DateTime();
$oggi = new DateTime();
$birth = new DateTime($userInfo['data_nascita']);
$eta = $oggi->diff($birth)->y;
$pagina = str_replace("[Eta]",$eta." anni",$pagina);

// Data di iscrizione DA SISTEMARE!!!!
$data_iscrizione = new DateTime($userInfo['data_iscrizione']);
$pagina = str_replace(
    '[Data Iscrizione]',
    '<time datetime="' . $data_iscrizione->format("Y-m-d") . '">' . $data_iscrizione->format("d/m/Y") . '</time>',
    $pagina
);

echo $pagina;

?>