<?php

require_once "php/db.php";
require_once "php/sanitizer.php";
require_once "change-page-products.php";

$db = new DB;

$people_reservation = null;
$date_reservation = null;

//GESTIONE GENERALE

$pagina = file_get_contents("html/degustazione.html");
if(isset($_GET["degustazione"])){
    $tasting = $_GET["degustazione"];
    $tastingInfo = $db->GetTastingInfo($_GET["degustazione"]);
    IF(is_string($tastingInfo) && $tastingInfo == "Tasting not found") {
        header("Location: 404.php");
        exit();
    }elseif(is_string($tastingInfo) && $tastingInfo == "Execution error" || $tastingInfo == "Connection error") {
        header("Location: 500.php");
        exit();
    }elseif($tastingInfo["disponibilita_persone"] == 0){
        header("Location: degustazioni.php");
            exit();
    }else{
        $start_date = new DateTime($tastingInfo["data_inizio"]);
        $end_date = new DateTime($tastingInfo["data_fine"]);
        if($start_date >= $end_date) {
            header("Location: 403.php");
            exit();
        }
    }
    $productInfo = $db->GetProductInfo($tastingInfo["id_prodotto"]);
    unset($_GET["degustazione"]);
    
    if($tastingInfo["disponibilita_persone"] == 0){//Se la disponibilità è 0
        $pagina = str_replace("<form method=\"post\" id=\"prenotazione-degustazione\" class=\"form-bianco\">
                        <fieldset>
                            <legend>Prenotazione Degustazione</legend>

                            <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                            <input type=\"hidden\" name=\"id_degustazione\" value=\"[id_degustazione]\">

                            <label for=\"quantita\" class=\"form-label\">Numero persone</label>
                            <div id=\"quantita-unita\">
                                <input type=\"number\" id=\"quantita\" name=\"quantita\" min=\"1\" max=\"[Numero_Persone]\" required>
                                <span class=\"unita\">persona/e</span>
                            </div>
                            <small class=\"descrizione-quantita\">Disponibile per un massimo di [Numero_Persone] Persona/e.</small>
                            [quantity-error]

                            <div>
                                <label for=\"data-prenotazione\" class=\"form-label\" id=\"order-label\">Data prenotazione</label>
                                <input type=\"date\" id=\"data-prenotazione\" name=\"data_ritiro\" required>
                            </div>
                            <small class=\"descrizione-quantita\">Prenotabile dal [Data_Inizio] al [Data_Fine].</small>
                            [date-error]

                            <div class=\"button-container\">
                                <button type=\"submit\" aria-label=\"Prenota Prodotto\" class=\"bottoni-rossi\" id=\"submit-reservation\">Prenota</button>
                            </div>
                        </fieldset>
                    </form>","",$pagina);
    }else{
        $pagina = str_replace("<p class=\"degustazione-nondisponibile\" id=\"degustazione-singola\">Prenotazione non disponibile!</p>","",$pagina);
    }

    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[IMMAGINE]","<img src=".$productInfo["url_immagine"]." alt=Immagine del prodotto".$productInfo["nome"].">",$pagina);
    $pagina = str_replace("[DESCRIZIONE]",$tastingInfo["descrizione"],$pagina);
    $start_date = new DateTime($tastingInfo["data_inizio"]);
    $end_date = new DateTime($tastingInfo["data_fine"]);
    $pagina = str_replace("[Data_Inizio]",$start_date->format("d-m-Y"),$pagina);
    $pagina = str_replace("[Data_Fine]",$end_date->format("d-m-Y"),$pagina);
    $pagina = str_replace("[Prezzo]",$tastingInfo["prezzo"]." &euro;",$pagina);
    $pagina = str_replace("<dd>Disponibile per <span class=\"degustazione-bold\">[Numero_Persone]</span> Persone</dd>",
                        $tastingInfo["disponibilita_persone"] == 0 ? "<dd class=\"error error-left\">Non disponibile</dd>" : "<dd>Disponibile per <span class=\"degustazione-bold\">". $tastingInfo["disponibilita_persone"]. "</span> Persone</dd>",$pagina);
    $pagina = str_replace("[PRODOTTO]","prodotto.php?prodotto=".$productInfo["id"],$pagina);
    $pagina = str_replace("[Numero_Persone]",$tastingInfo["disponibilita_persone"],$pagina);

    $isUserLogged = $db->IsUserLog();

    if(is_bool($isUserLogged) && $isUserLogged == false){//Se l'utente non è loggato
        $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    }else{
        $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
    }
    //GESTIONE PRENOTAZIONE DEGUSTAZIONE
    //FORM PRENOTAZIONE
    if(isset($_POST["submit-reservation"])){
        $errorFound = false;

        $people = (int)$_POST["quantita"];
        if($people < 1){
            $errorFound = true; 
            $pagina = str_replace("[quantity-error]","<p class=\"error\" id=\"quantity-error\">Il numero di persone per cui prenotare questa degustazione deve essere superiore o uguale a una persona</p>",$pagina);
        }elseif($quantity > $tastingInfo["disponibilità_persone"]){
            $errorFound = true; 
            $pagina = str_replace("[quantity-error]","<p class=\"error\" id=\"quantity-error\">Il numero di persone per cui prenotare prenotare questa degustazione deve essere inferiore o uguale a ".$tastingInfo["disponibilità_persone"].$tastingInfo["disponibilità_persone"] == 1 ? " persona</p>" : "persone</p>",$pagina);
        }else{
            $pagina = str_replace("[quantity-error]","",$pagina);
        }


        $date_reservation = $_POST["data_ritiro"];
        $today = new DateTime();
        $today1 = new DateTime();
        $tomorrow = (clone $today)->modify('+1 day');
        $reservationDate = new DateTime($date_reservation);
        if($reservationDate < $tastingInfo["data_inizio"]){
            $errorFound = true; 
            $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">La prenotazione di questai: ". $today1->format("d-m-Y")."</p>", $pagina);
        }else if($reservationDate > $tastingInfo["data_fine"]){
            $errorFound = true; 
            $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">L'ordine può essere ritirato solo nei giorni successivi ad oggi: ". $today1->format("d-m-Y")."</p>", $pagina);
        }else{
            $pagina = str_replace("[date-error]","",$pagina);
        }

        if($errorFound == true){//ci sono stati errori
            $pagina = str_replace("[quantita-ordine]",$quantity, $pagina);
            $pagina = str_replace("[data-ordine]",$date_reservation, $pagina); 
        }else{
            $addReservation = $db->AddReservation($productInfo["id"], $quantity, $date_reservation);
            if(is_bool($addReservation) && $addReservation == true) {//controllo se la modifica delle informazioni è andata a buon fine
                header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');//reindirizza alla pagina delle informazioni utente
                exit();
            } else { 
                header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');
                exit();
            }
        }

    }
}else{
    header("Location: degustazioni.php");
    exit();
}

echo $pagina;

?>