<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/403.html");// Carica il template HTML della pagina 403

if($db->isUserLog()!=false) {
    echo str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
} else {
    echo str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina); 
}

?>