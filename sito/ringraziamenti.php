<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/ringraziamenti.html");// Carica il template HTML della pagina 403

if($db->isUserLog() != false) {
    $userInfo = $db->GetUserInfo();
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
    $pagina = str_replace("[Nome]",$userInfo['nome'],$pagina);
} else {
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina); 
    $pagina = str_replace("[Nome]","",$pagina);
}
echo $pagina;

?>