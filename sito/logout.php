<?php
require_once "php/db.php";

$db = new DB;

echo "Sono entrato in logout";

// Prova a fare il logout; LogoutUser gestisce già il caso in cui l'utente non sia loggato
$db->LogoutUser();

// Reindirizza alla pagina di login
header('Location: login.php');
exit();
?>