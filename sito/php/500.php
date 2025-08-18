<?php

require_once "db.php";
use DB;
$db = new DB;

$pagina = file_get_contents("./html/500.html");// Carica il template HTML della pagina 403

if($db->isUserLog()!=false) {
    echo str_replace("{{to-profile}}","<a href=\"user-profile.php\">PROFILO</a>",$pagina);//accesso profilo andrà messo in tutte le pagine al posto del link areariservata.html
} else {
    echo str_replace("{{to-profile}}","<a href=\"areariservata.php\"><span lang=\"en\">LOGIN</span></a>",$pagina); 
}

?>