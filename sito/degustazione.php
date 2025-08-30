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
    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[IMMAGINE]","<img src=".$productInfo["url_immagine"]." alt=Immagine del prodotto".$productInfo["nome"].">",$pagina);
    $pagina = str_replace("[DESCRIZIONE]",$tastingInfo["descrizione"],$pagina);
    $start_date = new DateTime($tastingInfo["data_inizio"]);
    $end_date = new DateTime($tastingInfo["data_fine"]);
    $pagina = str_replace("[Data_Inizio]",$start_date->format("d-m-Y"),$pagina);
    $pagina = str_replace("[Data_Fine]",$end_date->format("d-m-Y"),$pagina);
    $pagina = str_replace("[Prezzo]",$tastingInfo["prezzo"]." &euro;",$pagina);
    $pagina = str_replace("[Numero_Persone]",$tastingInfo["disponibilita_persone"],$pagina);
    $pagina = str_replace("[PRODOTTO]","prodotto.php?prodotto=".$productInfo["id"],$pagina);

    if($tastingInfo["disponibilita_persone"] == 0){//Se la disponibilità è 0
        $pagina = str_replace("<form method=\"post\" id=\"prenotazione-degustazione\" class=\"form-bianco\">
                        <fieldset>
                            <legend>Prenotazione Degustazione</legend>

                            <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                            <input type=\"hidden\" name=\"id_degustazione\" value=\"[id_degustazione]\">

                            <label for=\"quantita\" class=\"form-label\">Numero persone</label>
                            <div id=\"quantita-unita\">
                                <input type=\"number\" id=\"quantita\" name=\"quantita\" min=\"1\" max=\"10\" required>
                                <span class=\"unita\">persona/e</span>
                            </div>
                            <small class=\"descrizione-quantita\">Disponibile per un massimo di [Numero_Persone] Persona/e.</small>

                            <div>
                                <label for=\"data-prenotazione\" class=\"form-label\" id=\"order-label\">Data prenotazione</label>
                                <input type=\"date\" id=\"data-prenotazione\" name=\"data_ritiro\" required>
                            </div>
                            <small class=\"descrizione-quantita\">Prenotabile dal [Data_Inizio] al [Data_Fine].</small>

                            <div class=\"button-container\">
                                <button type=\"submit\" aria-label=\"Prenota Prodotto\" class=\"bottoni-rossi\" id=\"submit-reservation\">Prenota</button>
                            </div>
                        </fieldset>
                    </form>","",$pagina);
    }else{
        $pagina = str_replace("<p class=\"degustazione-nondisponibile\" id=\"degustazione-singola\">Prenotazione non disponibile!</p>","",$pagina);
    }

    $isUserLogged = $db->IsUserLog();

    if(is_bool($isUserLogged) && $isUserLogged == false){//Se l'utente non è loggato
        $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    }else{
        $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
    }
    //GESTIONE PRENOTAZIONE DEGUSTAZIONE
}else{
    header("Location: degustazioni.php");
    exit();
}

echo $pagina;

?>