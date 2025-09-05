<?php

require_once "php/db.php";
require_once "php/sanitizer.php";

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
        $pagina = str_replace("[SCELTA]","Cancella account",$pagina);
        $pagina = str_replace("[DELETE]","delete-account",$pagina);
        $pagina = str_replace("[TITOLO]","Elimina <span lang=\"en\">Account</span>",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare il tuo profilo?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto elimina cancellerai il tuo profilo in modo permanente: le tue degustazioni prenotate, i tuoi prodotti prenotati, i prodotti salvati e tutti i tuoi dati personali saranno eliminati immediatamente e non potranno più essere recuperati!",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-favorites") == 0) {
        $pagina = str_replace("[SCELTA]","Elimina preferiti",$pagina);
        $pagina = str_replace("[DELETE]","delete-favorites",$pagina);
        $pagina = str_replace("[TITOLO]","Elimina Preferiti",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare tutti i prodotti che hai salvato come preferiti?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto elimina cancellerai tutti i prodotti che hai salvato come preferiti.",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-tastings") == 0) {
        $pagina = str_replace("[SCELTA]","Elimina degustazioni",$pagina);
        $pagina = str_replace("[DELETE]","delete-tastings",$pagina);
        $pagina = str_replace("[TITOLO]","Elimina Degustazioni",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare tutte le tue degustazioni?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto elimina cancellerai tutte le degustazioni da te prenotate.",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-reservations") == 0) {
        $pagina = str_replace("[SCELTA]","Elimina prenotazioni",$pagina);
        $pagina = str_replace("[DELETE]","delete-reservations",$pagina);
        $pagina = str_replace("[TITOLO]","Elimina Prenotazioni",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare tutte le tue prenotazioni?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto elimina cancellerai tutti i prodotti da te prenotati.",$pagina);
        echo $pagina;

    }elseif (strcmp($action,"logout-button")== 0) {
        $pagina = str_replace("[SCELTA]","<span lang=\"en\">Logout</span>",$pagina);
        $pagina = str_replace("[DELETE]","logout",$pagina);
        $pagina = str_replace("[TITOLO]","<span lang=\"en\">Logout</span>",$pagina);
        $pagina = str_replace("ELIMINA","CONFERMA",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler effettuare il <span lang=\"en\">logout</span>?",$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Se cliccherai sul tasto conferma verrai disconnesso dal tuo profilo.",$pagina);
        echo $pagina;

    } elseif (strcmp($action,"delete-degustazione")==0){
        $pagina = str_replace("[SCELTA]","Elimina prenotazione degustazione",$pagina);
        $pagina = str_replace("[DELETE]","delete-degustazione",$pagina);
        $pagina = str_replace("[TITOLO]","Elimina Prenotazione Degustazione",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare la degustazione prenotata?",$pagina);
        $pagina = str_replace("[VALORE-NASCOSTO]",Sanitizer::IntFilter($_GET['id-prenotazione']),$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Cliccando sul tasto Elimina verrà annullata la prenotazione della degustazione precedentemente selezionata.",$pagina);

        echo $pagina;
    
    } elseif (strcmp($action,"delete-prodotto")==0){
        $pagina = str_replace("[SCELTA]","Elimina prenotazione prodotto",$pagina);
        $pagina = str_replace("[DELETE]","delete-prodotto",$pagina);
        $pagina = str_replace("[TITOLO]","Elimina Prenotazione Prodotto",$pagina);
        $pagina = str_replace("[MESSAGGIO DI AVVISO]","Sei sicuro di voler eliminare il prodotto prenotato?",$pagina);
        $pagina = str_replace("[VALORE-NASCOSTO]",Sanitizer::IntFilter($_GET['id-prenotazione']),$pagina);
        $pagina = str_replace("[MESSAGGIO DI CANCELLAZIONE]","Cliccando sul tasto Elimina verrà annullata la prenotazione del prodotto precedentemente selezionato.",$pagina);

        echo $pagina;
    
    } else {
        header('Location: 500.php');
        exit();
    }

} elseif (isset($_POST["delete-account"])) {
    $action = $_POST["delete-account"];
    unset ($_POST["delete-account"]);
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

} elseif (isset($_POST["delete-favorites"])) {//Eliminazione prodotti favoriti
    $action = $_POST["delete-favorites"];
    unset ($_POST["delete-favorites"]);
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

} elseif (isset($_POST["delete-tastings"])) {//Eliminazione degustazioni
    $action=$_POST["delete-tastings"];
    unset($_POST["delete-tastings"]);
    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result=$db->DeleteAllTastings();
    if(is_bool($result) && $result == true) {
        header('Location: user-profile.php');
        exit();
    } else{
        header('Location: 500.php');
        exit();
    }

} elseif (isset($_POST["delete-reservations"])) {//Eliminazione prenotazioni
    $action=$_POST["delete-reservations"];
    unset($_POST["delete-reservations"]);
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

   
} elseif (isset($_POST["logout"])){
    $action=$_POST["logout"];
    unset($_POST["logout"]);
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
} else if (isset($_POST['delete-degustazione'])){ //Eliminazione della singola degustazione
    $action = $_POST['delete-degustazione'];
    $id = Sanitizer::IntFilter($_POST['int-value']);
    unset($_POST);

    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result = $db->DeleteOneTasting($id);

    if(is_bool($result) && $result == true) {
        header('Location: user-profile.php');
        exit();
    } else {
        header('Location: 500.php');
        exit();
    }

} else if (isset($_POST['delete-prodotto'])){ //Eliminazione del singolo prodotto prenotato
    $action = $_POST['delete-prodotto'];
    $id = Sanitizer::IntFilter($_POST['int-value']);
    unset($_POST);

    if(strcmp($action,"true") != 0) {
        header('Location: 500.php');
        exit();
    }

    $result = $db->DeleteOneReservation($id);

    if(is_bool($result) && $result == true) {
        header('Location: user-profile.php');
        exit();
    } else {
        //header('Location: 500.php');
        //exit();
        echo $result;
    }

} else {
    header('Location: 500.php');
    exit();
}

?>