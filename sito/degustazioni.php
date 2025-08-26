<?php

require_once "php/db.php";

$db = new DB;

$pagina = file_get_contents("html/degustazioni.html");

$isUserLogged = $db->isUserLog(); //controllo se l'utente è loggato
$isLogged = $db->UserUsername(); //recupero l'username dell'utente loggato

if ($isUserLogged!=false) {//se l'utente è loggato, mostra il suo profilo
    $pagina=str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);//se l'utente è loggato, mostra il link al suo profilo
    $userInfo=$db->getUserInfo();//ottiene le informazioni dell'utente loggato
    if (is_string($userInfo) && (strcmp($userInfo,"Execution error")==0 || strcmp($userInfo,"User not found")==0 || strcmp($userInfo,"Connection error")==0)) {//se c'è un errore nell'ottenere le informazioni dell'utente, reindirizza alla pagina di errore
        header('Location: 500.php');
        exit();
    } else if(is_string($userInfo) && strcmp($userInfo,"User Is not logged")==0) {
        header('Location: login.php');
        exit();
    }
} else {//se l'utente non è loggato, mostra il link per il login
    $pagina=str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
}

$tastings = $db->GetTastings();
if($tastings === false){
    header('Location: 500.php');
} elseif (empty($tastings)){
    $pagina=str_replace("[Degustazioni]","<p class=\"nondisponibile\" id=\"nodegustazioni\">Nessuna degustazione da visualizzare!</p>",$pagina);
}

print_r($db->GetTastings());

echo $pagina;

?>