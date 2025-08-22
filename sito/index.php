<?php
require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/index.html");

if($db->isUserLog()!=false) {
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
} else {
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina); 
}

echo $pagina;
?>