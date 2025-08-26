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
} else {
    $pagina=str_replace("[Degustazioni]",VisualizzaDegustazioni($tastings),$pagina);           
}

echo $pagina;


function CreaDegustazioneBrochure(int $id, string $img, string $nomeProdotto, string $descrizione, int $numeroPersone, DateTime $dataInizio, DateTime $dataFine, float $prezzo): string{
    
    $oggi = new DateTime("today");

    if ($oggi < $dataInizio || $oggi > $dataFine){
        //Prenotazione NON DISPONIBILE - non visualizzato
        return '';
    } else {
        //Prenotazione DISPONIBILE - viene visualizzato
        $TEMPLATE = '
            <div class="degustazione-card">
                <img src="' . $img . '" alt="Immagine del prodotto ' . $nomeProdotto . '">
                <div class="degustazione-contenuto">
                    <h3>' . $nomeProdotto . '</h3>
                    <p>' . $descrizione . '</p>
                    
                    <dl class="degustazione-dettagli">
                        <dt class="degustazione-persone">Disponibilit&agrave;</dt>';

            if ($numeroPersone < 1){
                $TEMPLATE .= '<dd><span class="persone-nondisponibile degustazione-bold">Non disponibile</span></dd>';
            } else{
                $TEMPLATE.= '<dd>Disponibile per <span class="degustazione-bold">' . $numeroPersone . '</span> persone</dd>';
            }
                    
        $TEMPLATE.= '
                <dt class="degustazione-prenotazione">Prenotazioni</dt>
                <dd>Prenotabile dal <span class="degustazione-bold">' . $dataInizio->format("d/m/Y") . '</span> al <span class="degustazione-bold">' . $dataFine->format("d/m/Y") . '</span></dd>
                        
                <dt class="degustazione-prezzo">Prezzo</dt>
                <dd class="prodotto-prezzo-testo">' . number_format($prezzo, 2, ',', '.') . ' €</dd>
            </dl>';

        if ($numeroPersone < 1) {
            $TEMPLATE .= '<p class="degustazione-nondisponibile">Prenotazione non disponibile!</p>';
        } else {
            $TEMPLATE.= '<div class="button-container degustazione-prenota">
                            <a href="degustazione.php?degustazione='.$id.'" class="bottoni-rossi">Prenota ora la degustazione</a>
                        </div>';
        }

        $TEMPLATE.= '</div></div>';

        return $TEMPLATE;
    }
}

function VisualizzaDegustazioni(array $tastings): string
{
    $tastings_html = "";

    foreach ($tastings as $value) {
        $tastings_html .= CreaDegustazioneBrochure(
            $value['id_degustazione'],
            $value['url_immagine'],
            $value['nome_prodotto'],
            $value['descrizione_degustazione'],
            (int) $value['disponibilita_persone'],
            new DateTime($value['data_inizio']),
            new DateTime($value['data_fine']),
            (float) $value['prezzo_degustazione']
        );
    }

    return $tastings_html;
}

?>