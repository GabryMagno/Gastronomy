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

//Sostituzione di eventuali Nome e Nome Cognome nella pagina
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
// Prodotti Preferiti

$id = $db->IsUserLog();

$preferiti = $db->GetUserFavoritesProducts($id);

if($preferiti === false){
    header('Location: 500.php');
} elseif (empty($preferiti)){
    $pagina = str_replace('[Prodotti Preferiti]','<p class="nondisponibile">Nessun prodotto aggiunto ai preferiti.</p>',$pagina);
} else {
    $pagina=str_replace("[Prodotti Preferiti]",VisualizzaPreferito($preferiti),$pagina);           
}

function VisualizzaPreferito(array $preferiti): string {
    $preferito_html = '<div class="data-container" id="dc-prodotti-preferiti" aria-live="polite">
                        <ul class="list" id="user-profile-prodotti">';

    foreach ($preferiti as $value){
        $preferito_html.= CreaVisualizzaPreferito(
            $value['id'],
            $value['nome'],
            $value['url_immagine']
        );
    }

    $preferito_html.= '</ul></div>';

    return $preferito_html;
}

function CreaVisualizzaPreferito(int $idProdotto, string $nomeProdotto, string $url_immagine){
    $TEMPLATE = '
        <li class="product-brochure">
            <img src="'.$url_immagine.'" alt="Immagine del prodotto ' . $nomeProdotto . '">
                <h4 class="product-name">'.$nomeProdotto.'</h4>
                    <div class="brochure-links">
                        <a href="prodotto.php?prodotto='. urlencode($idProdotto) . '" title="Vai alla scheda prodotto ' . Sanitizer::SanitizeGenericInput($nomeProdotto) . '" class="btn-dettagli">Dettagli</a>
                        <a href="#" class="btn-elimina">Rimuovi</a>
                    </div>
            </li>
    ';

    return $TEMPLATE;
}

/*
                <div class="data-container" id="dc-prodotti-preferiti" aria-live="polite">
                    <ul class="list" id="user-profile-prodotti">
                        <li class="product-brochure">
                            <img src="assets/img/primo.png" alt="Nuovo prodotto">
                            <h4 class="product-name">[Nome prodotto]</h4>
                            <div class="brochure-links">
                                <a href="prodotto.php" class="btn-dettagli">Dettagli</a>
                                <a href="#" class="btn-elimina">Rimuovi</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

                <div class="button-container">
                    <button class="load-more bottoni-rossi" id="lm-prodotti-preferiti" aria-controls="dc-prodotti-preferiti" aria-label="Carica più prodotti aggiunti ai preferiti">
                        Carica di pi&ugrave;
                    </button>
                    <a class="bottoni-rossi" href="#prodotti-preferiti">Torna a inizio sezione</a>
                </div>
*/



// Prenotazioni
$pagina = str_replace('[Prodotti Prenotati]','<p class="nondisponibile">Nessun prodotto prenotato.</p>',$pagina);

/*
                <div class="data-container" id="dc-prodotti-prenotati" aria-live="polite">
                    <ul class="list" id="user-profile-prodotti-prenotati">
                        <li class="userprofile-brochure">
                            <div class="userprofile-brochure-content">
                                <img src="assets/img/primo.png" alt="Nuovo prodotto">
                                <h4>[Nome prodotto]</h5>
                                <dl>
                                    <dt>Data ritiro</dt>
                                    <dd>[dd-mm-yyyy]</dd>
                                    <dt>Quantit&agrave;</dt>
                                    <dd>[qta] [unita]</dd>
                                </dl>
                            </div>
                            <div class="brochure-links">
                                <a href="prodotto.php" class="btn-dettagli">Dettagli</a>
                                <a href="#" class="btn-elimina">Elimina</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

                <div class="button-container">
                    <button class="load-more bottoni-rossi" id="lm-prodotti-prenotati" aria-controls="dc-prodotti-prenotati" aria-label="Carica più prodotti prenotati">
                        Carica di pi&ugrave;
                    </button>
                    <a class="bottoni-rossi" href="#prodotti-prenotati">Torna a inizio sezione</a>
                </div>
*/


// Degustazioni
$pagina = str_replace('[Degustazioni Prossime]','<p class="nondisponibile">Nessuna degustazione prenotata.</p>',$pagina);

/*
                <div class="data-container" id="dc-degustazioni-prenotate" aria-live="polite">
                    <ul class="list" id="user-profile-degustazioni-prenotate">
                        <li class="userprofile-brochure">
                            <div class="userprofile-brochure-content">
                                <h4>[Nome degustazione]</h4>
                                <dl>
                                    <dt>Data</dt>
                                    <dd>[dd-mm-yyyy]</dd>
                                    <dt>Persone</dt>
                                    <dd>[NP]</dd>
                                    <dt>Prezzo</dt>
                                    <dd class="userprofile-brochure-prezzo">[Prezzo]</dd>
                                </dl>
                            </div>
                            <div class="brochure-links">
                                <a href="degustazione.php" class="btn-dettagli">Dettagli</a>
                                <a href="#" class="btn-elimina">Elimina</a>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="a11y-status sr-only nondisponibile" role="status" aria-live="polite"></div>

                <div class="button-container">
                    <button class="load-more bottoni-rossi" id="lm-degustazioni-prenotate" aria-controls="dc-degustazioni-prenotate" aria-label="Carica ulteriori degustazioni prenotate">
                        Carica di pi&ugrave;
                    </button>
                    <a class="bottoni-rossi" href="#degustazioni-prenotate">Torna a inizio sezione</a>
                </div>
*/

// Recensioni
$pagina = str_replace('[Recensioni Prodotti]','<p class="nondisponibile">Nessuna recensione inserita.</p>',$pagina);

/*
                <div class="data-container" id="dc-recensioni" aria-live="polite">
                    <ul class="list" id="user-profile-recensioni">
                        <li class="userprofile-brochure">
                            <div class="userprofile-brochure-content">
                                <h4>[Nome prodotto]</h4>
                                <dl>
                                    <dt>Data</dt>
                                    <dd>[dd-mm-yyyy]</dd>
                                    <dt>Valutazione</dt>
                                    <dd aria-label="Valutazione: 4 su 5 stelle">[Valutazione]</dd>
                                </dl>
                            </div>
                            <a href="prodotto.php#valutazione">Visualizza</a>
                        </li>
                    </ul>
                </div>

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