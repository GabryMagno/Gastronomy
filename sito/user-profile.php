<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/user-profile.html");

echo $pagina;

?>