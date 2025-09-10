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
    $isUserLogged = $db->IsUserLog();

    

    if($tastingInfo["disponibilita_persone"] == 0 || $tastingInfo["data_inizio"] > date("Y-m-d")){//Se la disponibilità è 0
        $pagina = str_replace("[FORM PRENOTAZIONE]","",$pagina);
    }elseif((is_bool($isUserLogged) && $isUserLogged == false)){
        $pagina = str_replace("[FORM PRENOTAZIONE]","<p id=\"reservation-log\">Devi aver effettuato l'accesso per prenotare una degustazione! <a href=\"login.php\">ACCEDI</a> oppure <a href=\"register.php\">REGISTRATI</a></p>",$pagina);
    }else{
        $tomorrow = (new DateTime())->modify("+1 day");
        $pagina = str_replace("[FORM PRENOTAZIONE]","<form method=\"post\" id=\"prenotazione-degustazione\" class=\"form-bianco\">
                        <fieldset>
                            <legend>Prenotazione Degustazione</legend>

                            <label for=\"quantita\" class=\"form-label\">Numero persone</label>
                            <div id=\"quantita-unita\">
                                <input type=\"number\" id=\"quantita\" name=\"quantita\" min=\"1\" max=\"[Numero_Persone]\" required>
                                <span class=\"unita\">persona/e</span>
                            </div>
                            <small class=\"descrizione-quantita\">Disponibile per un massimo di [Numero_Persone] Persona/e.</small>
                            [quantity-error]

                            <div>
                                <label for=\"data-prenotazione\" class=\"form-label\" id=\"order-label\">Data prenotazione</label>
                                <input type=\"date\" id=\"data-prenotazione\" name=\"data_ritiro\" min=\"".$tomorrow->format("Y-m-d")."\" max=\"".$end_date->format("Y-m-d")."\" required>
                            </div>
                            <small class=\"descrizione-quantita\">Prenotabile dal [Data_Inizio] al [Data_Fine].</small>
                            [date-error]

                            <div class=\"button-container\">
                                <button type=\"submit\" aria-label=\"Prenota Prodotto\" class=\"bottoni-link\" id=\"submit-reservation\" name=\"submit-reservation\">Prenota</button>
                            </div>
                        </fieldset>
                    </form>",$pagina);
    }

    if($tastingInfo["disponibilita_persone"] > 0 && $tastingInfo["data_inizio"] <= date("Y-m-d")){//Se la disponibilità è 0
        $pagina = str_replace("<p class=\"degustazione-nondisponibile\" id=\"degustazione-singola\">Prenotazione non disponibile!</p>","",$pagina);
    }

    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[IMMAGINE]",'<img src="'.$productInfo["url_immagine"].'" alt="Immagine prodotto '.Sanitizer::SanitizeText($productInfo["nome"]).'">',$pagina);
    $pagina = str_replace("[DESCRIZIONE]",$tastingInfo["descrizione"],$pagina);
    $start_date = new DateTime($tastingInfo["data_inizio"]);
    $end_date = new DateTime($tastingInfo["data_fine"]);
    $pagina = str_replace("[Data_Inizio]",'<time datetime="'.$start_date->format("Y-m-d"). '">'.$start_date->format("d/m/Y").'</time>',$pagina);
    $pagina = str_replace("[Data_Fine]",'<time datetime="'.$end_date->format("Y-m-d"). '">'.$end_date->format("d/m/Y").'</time>',$pagina);
    $pagina = str_replace("[Prezzo]",$tastingInfo["prezzo"]." &euro;",$pagina);
    $pagina = str_replace("<dd>Disponibile per <span class=\"degustazione-bold\">[Numero_Persone]</span> Persone</dd>",
                        $tastingInfo["disponibilita_persone"] == 0 ? "<dd class=\"error error-left\">Non disponibile</dd>" : "<dd>Disponibile per <span class=\"degustazione-bold\">". $tastingInfo["disponibilita_persone"]. "</span> Persone</dd>",$pagina);
    $pagina = str_replace("[PRODOTTO]","prodotto.php?prodotto=".$productInfo["id"],$pagina);
    $pagina = str_replace("[Numero_Persone]",$tastingInfo["disponibilita_persone"],$pagina);
   
    $isUserLogged = $db->IsUserLog();
    if(is_bool($isUserLogged) && $isUserLogged == false){//Se l'utente non è loggato
        $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    }else{
        //GESTIONE PRENOTAZIONE DEGUSTAZIONE
    //FORM PRENOTAZIONE
        if(isset($_POST["submit-reservation"])){
            $errorFound = false;

            $people = (int)$_POST["quantita"];
            if($people < 1){
                $errorFound = true; 
                $pagina = str_replace("[quantity-error]","<p class=\"error\" id=\"quantity-error\">Il numero di persone per cui prenotare questa degustazione deve essere superiore o uguale a una persona</p>",$pagina);
            }elseif($people > $tastingInfo["disponibilita_persone"]){
                $errorFound = true; 
                $pagina = str_replace("[quantity-error]","<p class=\"error\" id=\"quantity-error\">Il numero di persone per cui prenotare prenotare questa degustazione deve essere inferiore o uguale a ".$tastingInfo["disponibilita_persone"].($tastingInfo["disponibilita_persone"] == 1 ? " persona</p>" : " persone</p>"),$pagina);
            }else{
                $pagina = str_replace("[quantity-error]","",$pagina);
            }


            $date_reservation = $_POST["data_ritiro"];
            $start = $tastingInfo["data_inizio"];
            $end = $tastingInfo["data_fine"];
            $today = new DateTime();
            $reservationDate = new DateTime($date_reservation);
            $start_date = new DateTime($start);
            $end_date = new DateTime($end);
            if($reservationDate < $start_date){//controllo che la data sia successiva o uguale a quella di inizio della degustazione
                $errorFound = true; 
                $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">La prenotazione può essere effettuata solo nei giorni successivi al : ". $start_date->format("d-m-Y")."</p>", $pagina);
            }elseif($reservationDate < $today){//controllo che la data sia successiva o uguale a quella di oggi(non ha senso prenotare nel passato)
                $errorFound = true;
                $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">La prenotazione può essere effettuata oggi : ". $today->format("d-m-Y")." e nei giorni precedenti al : ". $end_date->format("d-m-Y")."</p>", $pagina);
            }else if($reservationDate > $end_date){//controllo che la data sia precedente o uguale a quella di fine degustazione
                $errorFound = true; 
                $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">L'ordine può essere ritirato entro e non oltre il : ". $end_date->format("d-m-Y")."</p>", $pagina);
            }else{
                $UserReserved = $db->userAlreadyReserved($tasting, $date_reservation);
                if(is_string($UserReserved) && ($UserReserved == "Connection error" || $UserReserved == "Execution error")){
                    header("Location: 500.php"); 
                    exit();
                }elseif(is_string($UserReserved) && $UserReserved == "User is not logged in"){
                    header("Location: login.php"); 
                    exit();
                }elseif(is_bool($UserReserved) && $UserReserved == true){
                    $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">Hai già una prenotazione per questa degustazione in questa data : ". $reservationDate->format("d-m-Y") ."</p>", $pagina);
                }else{
                    $pagina = str_replace("[date-error]","", $pagina);
                }

            }

            if($errorFound == true){//ci sono stati errori
                $pagina = str_replace("[quantita]",$people, $pagina);
                $pagina = str_replace("[data_ritiro]",$date_reservation, $pagina); 
            }else{//se ci sono errori

                $addReservation = $db->AddTasting($tasting, $people, $date_reservation);
                if(is_bool($addReservation) && $addReservation == true) {//controllo se la modifica delle informazioni è andata a buon fine
                    header('Location: degustazione.php?degustazione='. $tasting . '');
                    exit();
                }elseif(is_string($addReservation) && $addReservation == "User already has a reservation for this tasting on the selected date"){//se l'utente ha già una prenotazione per questa degustazione in quella data
                    $pagina = str_replace("[date-error2]","<p class=\"error\" id=\"date-error\">Hai già una prenotazione per questa degustazione in questa data : ". $reservationDate->format("d-m-Y") ."</p>", $pagina);
                } else { //se ci sono stati errori l'utente viene reindirizzato alla pagina di errore 500
                   header("Location: 500.php"); 
                   exit();
                }
            }

        }
        $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
        if(!isset($_POST["submit-reservation"])){
            $pagina = str_replace("[quantity-error]","",$pagina);
            $pagina = str_replace("[date-error]","",$pagina);
            $pagina = str_replace("[date-error2]","",$pagina);
        }
    }
}else{
    header("Location: degustazioni.php");
    exit();
}

echo $pagina;

?>