<?php
require_once "db.php";

$db = new DB;

// Prova a fare il logout; LogoutUser gestisce già il caso in cui l'utente non sia loggato
$db->LogoutUser();

// Reindirizza alla pagina di login
header('Location: ../login.php');
exit();
?>