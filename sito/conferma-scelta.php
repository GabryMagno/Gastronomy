<?php

require_once "php/db.php";//

$db = new DB;

$pagina = file_get_contents("html/conferma-scelta.html");
$isUserLogged = $db->isUserLog();

if ($isUserLogged){
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
}elseif (is_string($isUserLogged) && (strcmp($isUserLogged,"Execution error")==0 || strcmp($isUserLogged,"User not found")==0 || strcmp($isUserLogged,"Connection error")==0)) {//se c'è un errore nell'ottenere le informazioni dell'utente, reindirizza alla pagina di errore
    header('Location: 500.php');
    exit();
} else if (is_string($isUserLogged) && strcmp($isUserLogged,"User Is not logged")==0) {
    header('Location: login.php');
    exit();
}

if (isset($_GET["delete"])) {
    $action = $_GET["delete"];
    unset($_GET["delete"]);

    if (strcmp($action,"delete-account") == 0) {
        $pagina = str_replace("[SCELTA]","account",$pagina);
        $pagina = str_replace("[DELETE]","delete-account",$pagina);
        $pagina = str_replace("[TITOLO]","ELIMINA <span lang=\"en\">ACCOUNT</span>",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare il tuo profilo?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto conferma cancellerai il tuo profilo in modo permanente: le tue degustazioni prenotate, i tuoi prodotti prenotati, i prodotti salvati e tutti i tuoi dati personali saranno eliminati immediatamente e non potranno più essere recuperati!",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-favorites") == 0) {
        $pagina = str_replace("[SCELTA]","preferiti",$pagina);
        $pagina = str_replace("[DELETE]","delete-favorites",$pagina);
        $pagina = str_replace("[TITOLO]","ELIMINA PREFERITI",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare tutti i prodotti che hai salvato come preferiti?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto conferma cancellerai tutti i prodotti che hai salvato come preferiti",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-tastings") == 0) {
        $pagina = str_replace("[SCELTA]","degustazioni",$pagina);
        $pagina = str_replace("[DELETE]","delete-tastings",$pagina);
        $pagina = str_replace("[TITOLO]","ELIMINA DEGUSTAZIONI",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare tutte le tue degustazioni?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto conferma cancellerai tutte le degustazioni da te prenotate",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-reservations") == 0) {
        $pagina = str_replace("[SCELTA]","prenotazioni",$pagina);
        $pagina = str_replace("[DELETE]","delete-reservations",$pagina);
        $pagina = str_replace("[TITOLO]","ELIMINA TUTTE LE PRENOTAZIONI",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare tutte le tue prenotazioni?",$pagina);
        echo $pagina;

    }elseif (strcmp($action,"logout-button")== 0) {
        $pagina = str_replace("eliminazione [SCELTA]","logout",$pagina);
        $pagina = str_replace("[DELETE]","logout",$pagina);
        $pagina = str_replace("[TITOLO]","LOGOUT",$pagina);
        $pagina = str_replace("ELIMINA","CONFERMA",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler effettuare il logout?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto conferma verrai disconnesso dal tuo profilo",$pagina);
        echo $pagina;

    } else {
        header('Location: 500.php');
        exit();
    }

} elseif (isset($_GET["delete-account"])) {
    $action = $_GET["delete-account"];
    unset ($_GET["delete-account"]);
    if (strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $userPath = $db->GetUserInfo()["url_immagine"];//eliminazione del logo dell'utente
	if(file_exists($userPath)) {
		unlink($userPath);
    }
    
    $result = $db->deleteUser();//dovrebbe a cascata eliminare tutto ciò che riguarda l'utente

    if (is_bool($result) && $result==true) {
        header('Location: index.php');
        exit();
    } else {
        header('Location: 500.php');
        exit();
    }

} elseif (isset($_GET["delete-favorites"])) {//Eliminazione prodotti favoriti
    $action = $_GET["delete-favorites"];
    unset ($_GET["delete-favorites"]);
    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result = $db->DeleteAllFavoritesProducts();
    if( is_bool($result) && $result == true) {
        header('Location: user-profile.php');
        exit();
    } else {
        header('Location: 500.php');
        exit();
    }

} elseif (isset($_GET["delete-tastings"])) {//Eliminazione degustazioni
    $action=$_GET["delete-tastings"];
    unset($_GET["delete-tastings"]);
    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result=$db->DeleteAllTastings();
    if(is_bool($result) && $result == true) {
        header('Location: user-profile.php');
        exit();
    } else{
        header('Location: 404.php');
        exit();
    }

} elseif (isset($_GET["delete-reservations"])) {//Eliminazione prenotazioni
    $action=$_GET["delete-reservations"];
    unset($_GET["delete-reservations"]);
    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result=$db->DeleteAllReservations();
    if(is_bool($result) && $result == true) {
        header('Location: user-profile.php');
        exit();
    } else {
        header('Location: 500.php');
        exit();
    }

   
}elseif (isset($_GET["logout"])){
    $action=$_GET["logout"];
    unset($_GET["logout"]);
    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result = $db->LogoutUser();
    if(is_bool($result) && $result == true) {
        header('Location: index.php');
        exit();
    } else {
        header('Location: 500.php');
        exit();
    }
} else {
    header('Location: 500.php');
    exit();
}

?>