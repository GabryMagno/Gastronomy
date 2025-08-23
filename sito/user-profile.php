<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/user-profile.html");

$isLogged_first = $db->isUserLog();//controllo se l'utente è loggato
$isLogged = $db->UserUsername();//recupero l'username dell'utente loggato

/*
if ((is_bool($isLogged_first) && $isLogged_first == false)||(is_bool($isLogged) && $isLogged == false)) {//controllo se l'utente non è loggato(sia con id che con username)
    header('Location: login.php');
    exit();
}
*/

$userInfo = $db->GetUserInfo();//recupero le informazioni dell'utente loggato



echo $pagina;

?>