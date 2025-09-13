<?php

require_once "php/db.php";
require_once "php/sanitizer.php";
require_once "change-page-tastings.php";


$db = new DB;

$order = isset($_GET['ordina']) ? Sanitizer::SanitizeGenericInput($_GET['ordina']) : null;// ordine (per data --> tipo di degustazione(passate, presente o futura))
$pageSystem = new ChangePageTastings($db, $order);// crea l'oggetto per gestire il cambio di pagina
$_SESSION["previous-page"]="<a href=\"./degustazioni.php\">DEGUSTAZIONI</a>";// link alla pagina precedente

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

$currentPage = isset($_GET['page']) ? Sanitizer::SanitizeGenericInput(Sanitizer::IntFilter($_GET['page'])) : 1;// pagina corrente

$tastings = $pageSystem->GetCurrentPage($currentPage);// prendi i prodotti della pagina corrente

$d_tastings = "";// stringa che conterrà i prodotti da mostrare
if ($tastings != null) {// se ci sono prodotti, crea le brochure per ogni prodotto
    foreach ($tastings as $tasting) {// per ogni prodotto
        $d_tastings .= CreaDegustazioneBrochure($tasting["url_immagine"], $tasting["nome"], $tasting["prezzo"], $tasting["id_degustazione"], $tasting["descrizione"], $tasting["disponibilita_persone"], new DateTime($tasting["data_inizio"]), new DateTime($tasting["data_fine"]));
    }
} else {// altrimenti mostra un messaggio di errore
    $d_tastings = "<p class=\"nondisponibile\" id=\"nodegustazioni\">Nessuna degustazione da visualizzare!</p>";
}

if ($order) {
    $pagina = str_replace('value="'.$order.'"', 'value="'.$order.'" selected', $pagina);
}
$pagina = str_replace("id=\"select-for\" name=\"ordina\"", "id=\"select-for\" name=\"ordina\" onchange=\"this.form.submit()\"", $pagina);
$pagina = str_replace("[Degustazioni]", $d_tastings, $pagina);
$pagina = str_replace("[Buttons]", $pageSystem->CreateButtons(), $pagina);



/*$tastings = $db->GetTastings();

if($tastings === false){
    header('Location: 500.php');
} elseif (empty($tastings)){
    $pagina=str_replace("[Degustazioni]","<p class=\"nondisponibile\" id=\"nodegustazioni\">Nessuna degustazione da visualizzare!</p>",$pagina);
} else {
    $pagina=str_replace("[Degustazioni]",VisualizzaDegustazioni($tastings),$pagina);           
}*/

echo $pagina;


function CreaDegustazioneBrochure( string $img, string $nomeProdotto, float $prezzo, int $id,string $descrizione,int $numeroPersone, DateTime $dataInizio, DateTime $dataFine): string{
    $today =  new DateTime("now");
    
    //Prenotazione DISPONIBILE - viene visualizzato perchè data odierna compresa nell'intervallo di possibilità prenotazione
    $TEMPLATE = '
        <div class="degustazione-card">
            <img src="' . $img . '" alt="Immagine del prodotto ' . $nomeProdotto . '">
            <div class="degustazione-contenuto">
                <h3>' . $nomeProdotto . '</h3>
                <p>' . $descrizione . '</p>
                
                <dl class="degustazione-dettagli">
                    <div class="row"><dt class="degustazione-persone">Disponibilit&agrave;</dt>';

    if ($numeroPersone < 1 || $today > $dataFine) {
        $TEMPLATE .= '<dd><span class="persone-nondisponibile degustazione-bold">Non disponibile</span></dd></div>';
    } else{
        $TEMPLATE.= '<dd>Disponibile per <span class="degustazione-bold">' . $numeroPersone . '</span> persone</dd></div>';
    }
            
    $TEMPLATE.= '
            <div class="row"><dt class="degustazione-prenotazione">Prenotazioni</dt>
            <dd>Prenotabile dal <span class="degustazione-bold"><time datetime="'.$dataInizio->format("Y-m-d") . '">' . $dataInizio->format("d/m/Y") . '</time></span> al <span class="degustazione-bold"><time datetime="'.$dataFine->format("Y-m-d") . '">' . $dataFine->format("d/m/Y") . '</time></span></dd></div>
                    
            <div class="row"><dt class="degustazione-prezzo">Prezzo</dt>
            <dd class="prodotto-prezzo-testo">' . number_format($prezzo, 2, ',', '.') . ' €</dd></div>
        </dl>';

    if ($numeroPersone < 1 || $dataInizio > $today || $dataFine < $today) {
        $TEMPLATE .= '<p class="degustazione-nondisponibile">Prenotazione non disponibile!</p>';
    } else {
        $TEMPLATE.= '<div class="button-container degustazione-prenota">
                        <a href="degustazione.php?degustazione='.$id.'" class="bottoni-link">Prenota ora la degustazione</a>
                    </div>';
    }

    $TEMPLATE.= '</div></div>';

    return $TEMPLATE;

}

?>