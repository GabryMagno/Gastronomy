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

// Se utente ha cliccato il bottone per rimuovere un prodotto dai preferiti
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["up-rimuovi-preferiti"])) {
    $result = $db->DeleteOneFavoriteProduct($_POST['id_prodotto']);
    if (is_string($result) && ($result == "Execution error" || $result == "Connection error")) {
        header("Location: 500.php");
        exit();
    }

    // Redirect per evitare il problema del reinvio del form
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Prodotti Preferiti
$max_preferiti = 5;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$preferiti = $db->GetUserFavoritesProducts($id);

if($preferiti === false){
    header('Location: 500.php');
} elseif (empty($preferiti)){
    $pagina = str_replace('[Prodotti Preferiti]','<p class="nondisponibile">Nessun prodotto aggiunto ai preferiti.</p>',$pagina);
} else {
    $pagina=str_replace("[Prodotti Preferiti]",VisualizzaPreferito($preferiti, $id, $offset, $max_preferiti),$pagina);
}

function VisualizzaPreferito(array $preferiti, int $id, $offset, $max_preferiti): string {

    $conteggio = 0;
    $toShow = $max_preferiti + $offset;

    $preferito_html = '<div class="data-container" id="dc-prodotti-preferiti" aria-live="polite">
                        <ul class="list" id="user-profile-prodotti">';
    
    foreach ($preferiti as $index => $value) {
        if($index < $toShow){
            $preferito_html.= CreaVisualizzaPreferito(
                $value['id'],
                $value['nome'],
                $value['url_immagine'],
                $id
            );

            $conteggio++;
        }
    }
    

    $preferito_html.= '</ul></div>';
    $button_html = '';

    if ($offset > 0 || count($preferiti) > $max_preferiti){
        $button_html.=
        '<a class="bottoni-link" href="#prodotti-preferiti">Torna a inizio sezione</a>';
    }

    if(count($preferiti) > $offset + $max_preferiti){
        $nextOffset = $offset + $max_preferiti;
        $button_html .= "<form action=\"#dc-prodotti-preferiti\" method=\"get\">
                            <input type=\"hidden\" name=\"offset\" value=\"$nextOffset\">
                            <button type=\"submit\" class=\"bottoni-rossi\">Carica più preferiti</button>
                        </form>";

    }

    if ($button_html){
        $preferito_html.= '<div class="button-container">
                            '.$button_html.'
                        </div>';
    }

    return $preferito_html;
}

function CreaVisualizzaPreferito(int $idProdotto, string $nomeProdotto, string $url_immagine, int $idUtente){
    $TEMPLATE = '
        <li class="product-brochure">
            <img src="'.$url_immagine.'" alt="Immagine del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '">
                <h4 class="product-name">'.$nomeProdotto.'</h4>
                    <div class="brochure-links">
                        <a href="prodotto.php?prodotto='. urlencode($idProdotto) . '" title="Vai alla scheda del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Dettagli</a>
                        <form method="post">
                            <input type="hidden" name="id_prodotto" value="'.$idProdotto.'">
                            <button type="submit" name="up-rimuovi-preferiti" class="btn-elimina" title="Rimuovi '.Sanitizer::SanitizeGenericInput($nomeProdotto).' dai preferiti" aria-label="Rimuovi '.Sanitizer::SanitizeGenericInput($nomeProdotto).' dai preferiti">Rimuovi</button>
                        </form>
                    </div>
        </li>
    ';

    return $TEMPLATE;
}

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
    $conteggio = 0;

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

        $conteggio++;
    }

    $prenotazioni_html.= '</ul></div>';

    if ($conteggio >= 4){
        $prenotazioni_html.=
        '<div class="button-container">
            <a class="bottoni-link" href="#prodotti-prenotati">Torna a inizio sezione</a>
        </div>';
    }

    return $prenotazioni_html;
}

function CreaVisualizzaPrenotazione(int $idPrenotazione, string $nomeProdotto, DateTime $dataRitiro, int $quantita, string $unita, int $idProdotto){

    $oggi = new DateTime("today");
    $domani = new DateTime("tomorrow");

    if ($dataRitiro == $oggi || $dataRitiro == $domani){
        $TEMPLATE = '<li class="userprofile-brochure urgent">';
    } else{
        $TEMPLATE = '<li class="userprofile-brochure">';
    }
    
    $TEMPLATE .= '
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
                    <form method="get" action="conferma-scelta.php">
                        <input type="hidden" name="delete" value="delete-prodotto">
                        <input type="hidden" name="id-prenotazione" value="'.urlencode($idPrenotazione).'">
                        <button type="submit" class="btn-elimina" title="Elimina prenotazione del prodotto '.Sanitizer::SanitizeGenericInput($nomeProdotto).'" aria-label="Elimina prenotazione degustazione">Elimina</button>
                    </form>
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
    $conteggio = 0;
    
    $degustazione_html = '<div class="data-container" id="dc-degustazioni-prenotate" aria-live="polite">
                            <ul class="list" id="user-profile-degustazioni-prenotate">';

    foreach ($degustazioni as $value){
        $degustazione_html.= CreaVisualizzaDegustazione(
            $value['id_degustazione'],
            $value['id_prenotazione'],
            $value['nome_prodotto'],
            new DateTime($value['data_scelta']),
            $value['prezzo'],
            $value['numero_persone']
        );

        $conteggio++;
    }

    $degustazione_html.= '</ul></div>';

    if ($conteggio >= 4){
        $degustazione_html.=
        '<div class="button-container">
            <a class="bottoni-link" href="#degustazioni-prenotate">Torna a inizio sezione</a>
        </div>';
    }

    return $degustazione_html;
}

function CreaVisualizzaDegustazione(int $idDegustazione,int $idPrenotazione, string $nomeProdotto, DateTime $dataScelta, float $prezzo, int $numeroPersone){

    $oggi = new DateTime("today");
    $domani = new DateTime("tomorrow");

    if ($dataScelta == $oggi || $dataScelta == $domani){
        $TEMPLATE = '<li class="userprofile-brochure urgent">';
    } else{
        $TEMPLATE = '<li class="userprofile-brochure">';
    }
    

    $TEMPLATE .= '
                <div class="userprofile-brochure-content">
                    <h4>'.$nomeProdotto.'</h4>
                        <dl>
                            <dt>Data Scelta</dt>
                                <dd><time datetime="' . $dataScelta->format("Y-m-d") . '">' . $dataScelta->format("d/m/Y") . '</time></dd>
                            <dt>Persone</dt>
                                <dd>'.$numeroPersone.' '.($numeroPersone == 1 ? 'persona' : 'persone').'</dd>
                            <dt>Totale</dt>
                                <dd class="userprofile-brochure-prezzo">'.number_format($prezzo*$numeroPersone, 2, ',', '.') . ' €</dd>
                        </dl>
                </div>
                <div class="brochure-links">
                    <a href="degustazione.php?degustazione='. urlencode($idDegustazione) . '" title="Vai alla scheda degustazione del prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Dettagli</a>
                    <form method="get" action="conferma-scelta.php">
                        <input type="hidden" name="delete" value="delete-degustazione">
                        <input type="hidden" name="id-prenotazione" value="'.urlencode($idPrenotazione).'">
                        <button type="submit" class="btn-elimina" title="Elimina prenotazione degustazione" aria-label="Elimina prenotazione degustazione">Elimina</button>
                    </form>
                </div>
            </li>
        ';

    return $TEMPLATE;
}

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
    $conteggio = 0;
    
    $recensione_html = '<div class="data-container" id="dc-recensioni" aria-live="polite">
                        <ul class="list" id="user-profile-recensioni">';

    foreach ($recensioni as $value){
        $recensione_html.= CreaVisualizzaRecensioni(
            $value['id_prodotto'],
            $value['nome_prodotto'],
            new DateTime($value['data_recensione']),
            $value['valutazione']
        );

        $conteggio++;
    }

    $recensione_html.= '</ul></div>';

    if ($conteggio >= 4){
        $recensione_html.=
        '<div class="button-container">
            <a class="bottoni-link" href="#recensioni-prodotti">Torna a inizio sezione</a>
        </div>';
    }

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
                            <dd><span aria-hidden="true">';
    
    
    for ($i=0; $i < $valutazione; $i++) { 
        $TEMPLATE .= '★';
    }

    for ($i=$valutazione; $i < 5; $i++) { 
        $TEMPLATE .= '☆';
    }

    $TEMPLATE .= '</span> ('.$valutazione.' su 5)';

    $TEMPLATE .= '</dl></div>
                        <a href="prodotto.php?prodotto='. urlencode($idProdotto) . '#valutazione" title="Visualizza valutazione inserita per il prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Visualizza</a>
                </li>';

    return $TEMPLATE;
}

echo $pagina;

?>