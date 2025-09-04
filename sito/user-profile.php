<?php

require_once "php/db.php";
require_once "php/sanitizer.php";

$db = new DB;

$pagina = file_get_contents("html/user-profile.html");

$isLogged_first = $db->isUserLog(); //controllo se l'utente è loggato
$isLogged = $db->UserUsername(); //recupero l'username dell'utente loggato

if ((is_bool($isLogged_first) && $isLogged_first == false)) {//controllo se l'utente non è loggato(sia con id che con username)
    header('Location: login.php');
    exit();
}

$userInfo = $db->GetUserInfo(); //recupero le informazioni dell'utente loggato

if (is_string($userInfo) && (strcmp($userInfo,"Execution error") == 0 || strcmp($userInfo,"Connection error") == 0)) {
    header('Location: 500.php');
    exit();
} elseif (is_string($userInfo) && (strcmp($userInfo,"User is not logged") == 0 || strcmp($userInfo,"User not found") == 0)) {
    header('Location: login.php');
    exit();
}

//Immagine Utente
if ($userInfo["url_immagine"]) {//controllo se l'utente ha un'immagine profilo
    $pagina = str_replace("[ImmagineProfilo]",'<img src="'.$userInfo['url_immagine'].'" alt="Foto profilo di '.$userInfo['nome']." ".$userInfo['cognome'].'" class="user-avatar">',$pagina);
} else {
    //immagine di default
    $pagina = str_replace("[ImmagineProfilo]",'<img src="assets/img/users_logos/default.webp" alt="Foto profilo di '.$userInfo['nome']." ".$userInfo['cognome'].'" class="user-avatar">',$pagina);
}

//Sostituzione di eventuali Nome e Nome Cognome nella pagina
$pagina = str_replace("[USERNAME]",$userInfo['username'],$pagina);
$pagina = str_replace("[Nome]",$userInfo['nome'],$pagina);
$pagina = str_replace("[Nome Cognome]",$userInfo['nome']." ".$userInfo['cognome'],$pagina);

// Eta dell'utente autenticato
$eta = new DateTime();
$oggi = new DateTime();
$birth = new DateTime($userInfo['data_nascita']);
$eta = $oggi->diff($birth)->y;
$pagina = str_replace("[Eta]",$eta." anni",$pagina);

// Data di iscrizione
$data_iscrizione = new DateTime($userInfo['data_iscrizione']);
$pagina = str_replace(
    '[Data Iscrizione]',
    '<time datetime="' . $data_iscrizione->format("Y-m-d") . '">' . $data_iscrizione->format("d/m/Y") . '</time>',
    $pagina
);


//SEZIONI PAGINA

$id = $db->IsUserLog();

// Prodotti Preferiti
$preferiti = $db->GetUserFavoritesProducts($id);

if($preferiti === false){
    header('Location: 500.php');
} elseif (empty($preferiti)){
    $pagina = str_replace('[Prodotti Preferiti]','<p class="nondisponibile">Nessun prodotto aggiunto ai preferiti.</p>',$pagina);
} else {
    $pagina=str_replace("[Prodotti Preferiti]",VisualizzaPreferito($preferiti, $id),$pagina);           
}

function VisualizzaPreferito(array $preferiti, int $id): string {
        $preferito_html = '<div class="data-container" id="dc-prodotti-preferiti" aria-live="polite">
                        <ul class="list" id="user-profile-prodotti">';

    foreach ($preferiti as $value){
        $preferito_html.= CreaVisualizzaPreferito(
            $value['id'],
            $value['nome'],
            $value['url_immagine'],
            $id
        );
    }

    $preferito_html.= '</ul></div>';

    return $preferito_html;
}

function CreaVisualizzaPreferito(int $idProdotto, string $nomeProdotto, string $url_immagine, int $idUtente){
    $TEMPLATE = '
        <li class="product-brochure">
            <img src="'.$url_immagine.'" alt="Immagine del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '">
                <h4 class="product-name">'.$nomeProdotto.'</h4>
                    <div class="brochure-links">
                        <a href="prodotto.php?prodotto='. urlencode($idProdotto) . '" title="Vai alla scheda del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Dettagli</a>
                        <form method="post" id="up-rimuovi-preferiti">
                            <button type="submit" class="btn-elimina" id="up-rimuovi-preferiti" title="Rimuovi '.Sanitizer::SanitizeGenericInput($nomeProdotto).' dai preferiti" aria-label="Rimuovi '.Sanitizer::SanitizeGenericInput($nomeProdotto).' dai preferiti">Rimuovi</button>
                                <input type="hidden" name="id_utente" value="'.$idUtente.'">
                                <input type="hidden" name="id_prodotto" value="'.$idProdotto.'">
                        </form>
                    </div>
            </li>
    ';

    return $TEMPLATE;
}

/*
    <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

    <div class="button-container">
        <button class="load-more bottoni-rossi" id="lm-prodotti-preferiti" aria-controls="dc-prodotti-preferiti" aria-label="Carica più prodotti selezionati come preferiti">
            Carica di pi&ugrave;
        </button>
        <a class="bottoni-rossi" href="#prodotti-preferiti">Torna a inizio sezione</a>
    </div>
*/



// Prenotazioni
$prenotazioni = $db->GetUserReservation($id);

if($prenotazioni === false){
    header('Location: 500.php');
} elseif (empty($prenotazioni)){
    $pagina = str_replace('[Prodotti Prenotati]','<p class="nondisponibile">Nessun prodotto prenotato.</p>',$pagina);
} else {
    $pagina=str_replace("[Prodotti Prenotati]",VisualizzaPrenotazione($prenotazioni),$pagina);           
}

function VisualizzaPrenotazione(array $prenotazioni): string {
    $prenotazioni_html = '<div class="data-container" id="dc-prodotti-prenotati" aria-live="polite">
                            <ul class="list" id="user-profile-prodotti-prenotati">';

    foreach ($prenotazioni as $value){
        $prenotazioni_html.= CreaVisualizzaPrenotazione(
            $value['id_prenotazione'],
            $value['nome_prodotto'],
            new DateTime($value['data_ritiro']),
            $value['quantita'],
            $value['unita'],
            $value['id_prodotto']
        );
    }

    $prenotazioni_html.= '</ul></div>';

    return $prenotazioni_html;
}

function CreaVisualizzaPrenotazione(int $idPrenotazione, string $nomeProdotto, DateTime $dataRitiro, int $quantita, string $unita, int $idProdotto){

    $TEMPLATE = '
            <li class="userprofile-brochure">
                <div class="userprofile-brochure-content">
                    <h4>'.$nomeProdotto.'</h4>
                    <dl>
                        <dt>Data ritiro</dt>
                            <dd><time datetime="' . $dataRitiro->format("Y-m-d") . '">' . $dataRitiro->format("d/m/Y") . '</time></dd>
                        <dt>Quantit&agrave;</dt>
                            <dd>'.$quantita.' '.($quantita > 1 || $unita == "kg" ? TipoUnita($unita) : $unita).'</dd>
                    </dl>
                </div>
                <div class="brochure-links">
                    <a href="prodotto.php?prodotto='. urlencode($idProdotto) . '" title="Vai alla scheda del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Dettagli</a>
                    <a href="#" class="btn-elimina">Elimina</a>
                </div>
            </li>
        ';

    return $TEMPLATE;
}

function TipoUnita(string $unita): string {
    switch ($unita) {
        case "porzione":
            return "porzioni";
        case "vaschetta":
            return "vaschette";
        case "pezzo":
            return "pezzi";
        case "kg":
            return '<abbr title="Chilogrammi">kg</abbr>';
        default:
            return "";
    }
}

/*
                <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

                <div class="button-container">
                    <button class="load-more bottoni-rossi" id="lm-prodotti-prenotati" aria-controls="dc-prodotti-prenotati" aria-label="Carica più prodotti prenotati">
                        Carica di pi&ugrave;
                    </button>
                    <a class="bottoni-rossi" href="#prodotti-prenotati">Torna a inizio sezione</a>
                </div>
*/


// Degustazioni
$degustazioni = $db->GetUserTastings($id);

if($degustazioni === false){
    header('Location: 500.php');
} elseif (empty($degustazioni)){
    $pagina = str_replace('[Degustazioni Prossime]','<p class="nondisponibile">Nessuna degustazione prenotata.</p>',$pagina);
} else {
    $pagina=str_replace("[Degustazioni Prossime]",VisualizzaDegustazione($degustazioni),$pagina);           
}

function VisualizzaDegustazione(array $degustazioni): string {
    $degustazione_html = '<div class="data-container" id="dc-degustazioni-prenotate" aria-live="polite">
                            <ul class="list" id="user-profile-degustazioni-prenotate">';

    foreach ($degustazioni as $value){
        $degustazione_html.= CreaVisualizzaDegustazione(
            $value['id_degustazione'],
            $value['nome_prodotto'],
            new DateTime($value['data_scelta']),
            $value['prezzo'],
            $value['numero_persone']
        );
    }

    $degustazione_html.= '</ul></div>';

    return $degustazione_html;
}

function CreaVisualizzaDegustazione(int $idDegustazione, string $nomeProdotto, DateTime $dataScelta, float $prezzo, int $numeroPersone){

    $TEMPLATE = '
            <li class="userprofile-brochure">
                <div class="userprofile-brochure-content">
                    <h4>'.$nomeProdotto.'</h4>
                        <dl>
                            <dt>Data Scelta</dt>
                                <dd><time datetime="' . $dataScelta->format("Y-m-d") . '">' . $dataScelta->format("d/m/Y") . '</time></dd>
                            <dt>Persone</dt>
                                <dd>'.$numeroPersone.' '.($numeroPersone == 1 ? 'persona' : 'persone').'</dd>
                            <dt>Prezzo</dt>
                                <dd class="userprofile-brochure-prezzo">'.number_format($prezzo, 2, ',', '.') . ' €</dd>
                        </dl>
                </div>
                <div class="brochure-links">
                    <a href="degustazione.php?degustazione='. urlencode($idDegustazione) . '" title="Vai alla scheda degustazione del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Dettagli</a>
                    <a href="#" class="btn-elimina">Elimina</a>
                </div>
            </li>
        ';

    return $TEMPLATE;
}

/*
                <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

                <div class="button-container">
                    <button class="load-more bottoni-rossi" id="lm-degustazioni-prenotate" aria-controls="dc-degustazioni-prenotate" aria-label="Carica ulteriori degustazioni prenotate">
                        Carica di pi&ugrave;
                    </button>
                    <a class="bottoni-rossi" href="#degustazioni-prenotate">Torna a inizio sezione</a>
                </div>
*/

// Recensioni
$recensioni = $db->GetUserReviews($id);

if($recensioni === false){
    header('Location: 500.php');
} elseif (empty($recensioni)){
    $pagina = str_replace('[Recensioni Prodotti]','<p class="nondisponibile">Nessuna recensione inserita.</p>',$pagina);
} else {
    $pagina=str_replace("[Recensioni Prodotti]",VisualizzaRecensione($recensioni),$pagina);           
}

function VisualizzaRecensione(array $recensioni): string {
    $recensione_html = '<div class="data-container" id="dc-recensioni" aria-live="polite">
                        <ul class="list" id="user-profile-recensioni">';

    foreach ($recensioni as $value){
        $recensione_html.= CreaVisualizzaRecensioni(
            $value['id_prodotto'],
            $value['nome_prodotto'],
            new DateTime($value['data_recensione']),
            $value['valutazione']
        );
    }

    $recensione_html.= '</ul></div>';

    return $recensione_html;
}

function CreaVisualizzaRecensioni(int $idProdotto, string $nomeProdotto, DateTime $dataRecensione, int $valutazione){

    $TEMPLATE = '
        <li class="userprofile-brochure">
            <div class="userprofile-brochure-content">
                <h4>'.$nomeProdotto.'</h4>
                    <dl>
                        <dt>Data</dt>
                            <dd><time datetime="' . $dataRecensione->format("Y-m-d") . '">' . $dataRecensione->format("d/m/Y") . '</time></dd>
                        <dt>Valutazione</dt>
                            <dd aria-label="Valutazione: '.$valutazione.' su 5 stelle">';
    
    
    for ($i=0; $i < $valutazione; $i++) { 
        $TEMPLATE .= '★';
    }

    for ($i=$valutazione; $i < 5; $i++) { 
        $TEMPLATE .= '☆';
    }

    //$TEMPLATE .= '<small> ('.$valutazione.' su 5)</small>';

    $TEMPLATE .= '</dl></div>
                        <a href="prodotto.php?prodotto='. urlencode($idProdotto) . '#valutazione" title="Visualizza valutazione inserita per il prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Visualizza</a>
                </li>';

    return $TEMPLATE;
}

/*
                <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

                <div class="button-container">
                    <button class="load-more bottoni-rossi" id="lm-recensioni" aria-controls="dc-recensioni" aria-label="Carica ulteriori recensioni da te scritte.">
                        Carica di pi&ugrave;
                    </button>
                    <a class="bottoni-rossi" href="#recensioni-prodotti">Torna a inizio sezione</a>
                </div>
*/

echo $pagina;

?>