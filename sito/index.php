<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/index.html");

echo $pagina;

?>