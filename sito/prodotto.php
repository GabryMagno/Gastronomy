<?php

require_once "php/db.php";
require_once "php/sanitizer.php";
require_once "change-page-products.php";

$db = new DB;

$comment = "";
$grade = null;
$quantity_reservation = null;
$date_reservation = null;

//GESTIONE GENERALE

$pagina = file_get_contents("html/prodotto.html");
if(isset($_GET["prodotto"])){
    $product = $_GET["prodotto"];
    $productInfo = $db->GetProductInfo($_GET["prodotto"]);
    IF(is_string($productInfo) && $productInfo == "Product not found") {
        header("Location: 404.php");
        exit();
    }else if(is_string($productInfo) && ($productInfo == "Connection error" || $productInfo == "Execution error")) {
        header("Location: 500.php");
        exit();
    }
    unset($_GET["prodotto"]);

    $ingredients = $db->GetProductIngredients($productInfo["id"]);
    $ingredientsHTML = "";
    if(is_string($ingredients) && ($ingredients == "Execution error" || $ingredients == "Connection error")) {
        header('Location: 500.php');
        exit();
    }
    if(is_string($ingredients) && $ingredients == "No ingredients found for this product") $ingredientsHTML = "<span class=\"persone-nondisponibile\">Ingredienti non inseriti</span>";
    else{
        foreach($ingredients as $ingredient){
            $ingredientsHTML .= "<li>". $ingredient["quantita"] . (($ingredient["unita_misura"] == "num_el" || $ingredient["unita_misura"] == null)? "" : $ingredient["unita_misura"]. " di") ."  ". $ingredient["ingrediente"] ."</li>";
        }
    }
    //CONTROLLO VEGANO/VEGETARIANO/CELICACO
    $infoGenreProduct = $db->IsProductVeganVegetarianCeliac($productInfo["id"]);
    if(is_string($infoGenreProduct) && ($infoGenreProduct == "Execution error" || $infoGenreProduct == "Connection error")) {
        header('Location: 500.php');
        exit();
    }else if(is_string($infoGenreProduct) && $infoGenreProduct == "Product not found"){
        header("Location: 404.php");
        exit();
    }else{
        if($infoGenreProduct["vegano"] == 0){
            $pagina = str_replace("<img src=\"assets/img/icone/vegano-verde.svg\" alt=\"icona verde con indicazione prodotto vegano\">
                        <span class=\"diet-label\">Vegano</span>","<img src=\"assets/img/icone/vegano-rosso.svg\" alt=\"icona rossa con indicazione prodotto non vegano\">
                        <span class=\"diet-label\">Non vegano</span>",$pagina);
        }

        if($infoGenreProduct["vegetariano"] == 0){
            $pagina = str_replace(" <img src=\"assets/img/icone/vegetariano-verde.svg\" alt=\"icona verde con indicazione prodotto vegetariano\">
                        <span class=\"diet-label\">Vegetariano</span>","<img src=\"assets/img/icone/vegetariano-rosso.svg\" alt=\"icona rossa con indicazione prodotto non vegetariano\">
                        <span class=\"diet-label\">Non vegetariano</span>",$pagina);
        }

        if($infoGenreProduct["celiaco"] == 0){
            $pagina = str_replace("<img src=\"assets/img/icone/celiaco-verde.svg\" alt=\"icona verde con indicazione prodotto per persone celiache\">
                        <span class=\"diet-label\">Celiaco</span>","<img src=\"assets/img/icone/celiaco-rosso.svg\" alt=\"icona rossa con indicazione prodotto non adatto a persone celiache\">
                        <span class=\"diet-label\">Non celiaco</span>",$pagina);
        }
    }
    $pagina = str_replace("[IMAGE]","<img src=". $productInfo["url_immagine"] ." alt=\"\">",$pagina);
    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[Categoria]",ucfirst($productInfo["categoria"]),$pagina);
    $pagina = str_replace("[Valutazione]",(number_format((float)$db->AverageGradeProduct($productInfo["id"]),1) == 0 ? "X" : number_format((float)$db->AverageGradeProduct($productInfo["id"]),1)),$pagina);
    $pagina = str_replace("[Prezzo]",$productInfo["prezzo"],$pagina);
    $pagina = str_replace("[Descrizione]",$productInfo["descrizione"],$pagina);
    $pagina = str_replace("[INGREDIENTI]",$ingredientsHTML,$pagina);
    $pagina = str_replace("[Unita]",($productInfo["unita"]=="kg"? '<abbr title="chilogrammo">kg</abbr>':$productInfo["unita"]),$pagina);
    
    //GESTIONE COMMENTI ALTRI UTENTI

    $max_comment = 4;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $comments = $db->GetProductComments($productInfo["id"], $max_comment + $offset + 1);
    if(is_string($comments) && $comments == "No comments found"){//se non ci sono commenti sul prodotto
        $pagina = str_replace("[OTHER COMMENTS]","<p class=\"nondisponibile singolo-prodotto\">Per ora non ci sono commenti di altri utenti su questo prodotto!</p>",$pagina);
        $pagina = str_replace("[COMMENTS BUTTONS]","",$pagina);
    } else {
        $commentNumber = 0;
        $commentList = "";

        foreach($comments as $comment) {
            if($commentNumber <  $max_comment + $offset){
                $otherUser = $db->GetUserInfo($comment["utente"]);
                if(!is_array($otherUser)) {
                    // Utente non loggato o non trovato → utente di default
                    $otherUser = [
                        'username' => $comment['username'] ?? 'Utente sconosciuto',
                        'url_immagine' => 'assets/img/users_logos/default.webp'
                    ];
                }

                // Sovrascrive l'immagine con quella del commento se presente
                if(!empty($comment['url_immagine'])) {
                    $otherUser['url_immagine'] = $comment['url_immagine'];
                }else $otherUser['url_immagine'] = "assets/img/users_logos/default.webp";
                if($commentNumber == 0) {
                    $commentList .= "<div id=\"return-comment\" class=\"recensione-card\">";
                } else {
                    $commentList .= "<div class=\"recensione-card\">";
                }
                $commentList .= "<div class=\"recensione-foto\">
                                <img src=".$otherUser['url_immagine']. " alt=\"Foto profilo di".$comment["username"]."\">
                            </div>";
                $date = new DateTime($comment["data"]);

                $commentList .= '<div class="recensione-contenuto">
                                    <h5 class="recensione-cliente">' . htmlspecialchars($comment["username"]) . '</h5>
                                    <span class="recensione-data">
                                        <time datetime="' . $date->format("Y-m-d") . '">' . $date->format("d/m/Y") . '</time>
                                    </span>
                                    <p class="recensione-testo">' . htmlspecialchars($comment["commento"]) . '</p>
                                    <p class="recensione-valutazione">Valutazione: <span aria-hidden="true">';

                for ($i=0; $i < $comment["voto"]; $i++) { 
                    $commentList .= '★';
                }

                for ($i=$comment["voto"]; $i < 5; $i++) { 
                    $commentList .= '☆';
                }

                $commentList.= '</span> ('.$comment["voto"].' su 5)
                                </p></div></div>';

                $commentNumber++;
            }          
        }
        $pagina = str_replace("[OTHER COMMENTS]",$commentList,$pagina);

        $moreCommentsForm = "";
        if($db->IsUserLog()){
            $UserCommented = $db->GetReview($db->IsUserLog(),$productInfo["id"]);
            if(is_string($UserCommented) && ($UserCommented == "Connection error" || $UserCommented == "Execution error")){
                header('Location: 500.php');
                exit();
            }
        }
        if(count($comments) > $max_comment + $offset) {
            $nextOffset = $offset + $max_comment;
            $moreCommentsForm = '<form action="#return-comment" id="more-comments" method="get">';
            $moreCommentsForm .= '<input type="hidden" name="prodotto" value="'.$productInfo["id"].'">';
            $moreCommentsForm .= '<input type="hidden" name="offset" value="'.$nextOffset.'">';
            $moreCommentsForm .= '<button type="submit" class="bottoni-rossi">Carica più recensioni</button></form>';
        }

        if($commentNumber>4) {
            $moreCommentsForm .= '<a href="#return-comment" id="pin-comment" class="bottone-link">Torna al primo commento</a>';
        }

        $pagina = str_replace("[COMMENTS BUTTONS]",$moreCommentsForm,$pagina);
    }


}else{
    header("Location: prodotti.php");
    exit();
}

$isUserLogged = $db->IsUserLog();

if(is_bool($isUserLogged) && $isUserLogged == false){//Se l'utente non è loggato
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    $pagina = str_replace("<form method=\"post\" id=\"modifica-preferiti\">
                            <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                            <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                            [PREFERITO]
                        </form>","",$pagina);
    $pagina = str_replace("<dl class=\"singleproduct-rating\">
                    <dt>Data</dt>
                    <dd>[Data Valutazione]</dd>
                    <dt>Valutazione</dt>
                    <dd class=\"rating-stars\">
                        <span aria-hidden=\"true\"></span>
                    </dd>
                    <dt>Commento</dt>
                    <dd>[Commento]</dd>
                </dl>","",$pagina);
    $pagina = str_replace("<form method=\"post\" id=\"elimina-valutazione\">
                    <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                    <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                    <div class=\"button-container\">
                        <button type=\"submit\" aria-label=\"Elimina Valutazione\" class=\"bottoni-rossi\" name=\"delete-review\">Elimina Valutazione</button>
                    </div>
                </form>","",$pagina);           
    $pagina = str_replace("[COMMENT]","<p id=\"comment-log\">Vuoi dire la tua su questo prodotto? <a href=\"login.php?reference-product=".urldecode($product)."\">ACCEDI</a> o <a href=\"register.php?reference-product=".urldecode($product)."\"> REGISTRATI</a> e lascia subito un commento!</p>",$pagina);
    $pagina = str_replace("[OLD_COMMENT]","",$pagina);
    $pagina = str_replace("<p id=\"prodotto-nondisponibile\">NON DISPONIBILE</p>","",$pagina);
    $pagina = str_replace("[RESERVATION]","<p id=\"reservation-log\">Non lasciarti sfuggire questa bont&agrave;! <a href=\"login.php?reference-product=".urldecode($product)."\">ACCEDI</a> o <a href=\"register.php?reference-product=".urldecode($product)."\"> REGISTRATI</a> per prenotarla!</p>",$pagina);
}else{//L'utente è loggato
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);

    //GESTIONE PRODOTTO PREFERITO

    $isProductFavorite = $db->ThisIsAlreadyFavoriteProduct($productInfo["id"],$isUserLogged);

    if(is_string($isProductFavorite) && ($isProductFavorite == "Connection error" || $isProductFavorite == "Execution error")){
        header("Location: 500.php");
        exit();
    }
    if(!$isProductFavorite){
        $pagina = str_replace("[PREFERITO]","<button type=\"submit\" id=\"singleproduct-stella\" title=\"Clicca per aggiungere ai preferiti\" aria-label=\"Clicca per aggiungere ai preferiti\" name=\"favorite\">
                                <img src=\"assets/img/icone/star.svg\" alt=\"Prodotto non aggiunto ai preferiti\">
                            </button>",$pagina);
    }else{
        $pagina = str_replace("[PREFERITO]","<button type=\"submit\" id=\"singleproduct-stella\" title=\"Clicca per rimuovere dai preferiti\" aria-label=\"Clicca per rimuovere dai preferiti\" name=\"remove-favorite\">
                                <img src=\"assets/img/icone/star-checked.svg\" alt=\"Prodotto aggiunto ai preferiti\">
                            </button>",$pagina);
    }

    if(isset($_POST["favorite"])){//Se l'utente ha cliccato il bottone per aggiungere il prodotto ai preferiti
        $result = $db->AddFavoriteProduct($isUserLogged,$productInfo["id"]);

        if(is_string($result) && ($result == "Execution error" || $result == "Connection error")){
            header("Location: 500.php");
        }

        $pagina = str_replace("<img src=\"assets/img/icone/star.svg\" alt=\"Prodotto non aggiunto ai preferiti\">",
        "<img src=\"assets/img/icone/star-checked.svg\" alt=\"Prodotto aggiunto ai preferiti\">",
        $pagina);

        $pagina = str_replace("title=\"Clicca per aggiungere ai preferiti\" aria-label=\"Clicca per aggiungere ai preferiti\" name=\"favorite\">",
        "title=\"Togli dai preferiti\" aria-label=\"Togli dai preferiti\" name=\"remove-favorite\">",
        $pagina);

        header("Location: prodotto.php?prodotto=" . $productInfo["id"]);
        exit();
    }

    if(isset($_POST["remove-favorite"])){//Se l'utente ha cliccato il bottone per rimuovere il prodotto dai preferiti
        $result = $db->DeleteOneFavoriteProduct($productInfo["id"]);
        if(is_string($result) && ($result == "Execution error" || $result == "Connection error")){
            header("Location: 500.php");
        }
        $pagina = str_replace("<img src=\"assets/img/icone/star-checked.svg\" alt=\"Prodotto aggiunto ai preferiti\">",
        "<img src=\"assets/img/icone/star.svg\" alt=\"Prodotto non aggiunto ai preferiti\">",
        $pagina);

        $pagina = str_replace("title=\"Clicca per rimuovere dai preferiti\" aria-label=\"Clicca per rimuovere dai preferiti\" name=\"remove-favorite\">",
        "title=\"Aggiungi ai preferiti\" aria-label=\"Aggiungi ai preferiti\" name=\"favorite\">",
        $pagina);

        header("Location: prodotto.php?prodotto=" . $productInfo["id"]);
        exit();
    }

    //CONTROLLO PRODOTTO DISPONIBILE PER PRENOTAZIONE

    if($productInfo["isDisponibile"]){//SE IL PRODOTTO E' DISPONIBILE
        $pagina = str_replace("<p id=\"prodotto-nondisponibile\">NON DISPONIBILE</p>","",$pagina);
        $now = new DateTime();
        $prox = (clone $now)->modify('+1 day');
        $next = (clone $prox)->modify('+14 day');
        $pagina = str_replace("[RESERVATION]",
    "<form method=\"post\" id=\"prenotazione\" class=\"form-bianco\">
                        <fieldset>
                            <legend>Prenotazione Prodotto</legend>

                            <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                            <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                            <label for=\"quantita\" class=\"form-label\">Quantità da prenotare</label>
                            <div id=\"quantita-unita\">
                                <input type=\"number\" id=\"quantita\" name=\"quantita\" min=\"1\" max=\"".(int)$productInfo["max_prenotabile"]."\" required>
                                <span class=\"unita\">[Unita]</span>
                                
                            </div>
                            <small class=\"descrizione-quantita\">Puoi prenotare da [min_prenotabile] a [max_prenotabile] unit&agrave;.</small>
                            [quantity-error]

                            <div>
                                <label for=\"data-ritiro\" class=\"form-label\" id=\"order-label\">Data di ritiro</label>
                                <input type=\"date\" id=\"data-ritiro\" name=\"data_ritiro\" min=\"".$prox->format("Y-m-d")."\" max=\"".$next->format("Y-m-d")."\" required>
                                [date-error]
                            </div>

                            <div class=\"button-container\">
                                <button type=\"submit\" aria-label=\"Prenota Prodotto\" class=\"bottoni-rossi\" id=\"submit-order\" name=\"submit-reservation\">Prenota</button>
                            </div>
                        </fieldset>
                    </form>",
    $pagina);
    
    $pagina = str_replace("[Unita]",($productInfo["unita"]=="kg"? '<abbr title="chilogrammo">kg</abbr>':$productInfo["unita"]),$pagina);
    $pagina = str_replace("[min_prenotabile]",$productInfo["min_prenotabile"],$pagina);
    $pagina = str_replace("[max_prenotabile]",$productInfo["max_prenotabile"],$pagina);
    }else{//SE IL PRODOTTO NON E' DISPONIBILE
        $pagina = str_replace("[RESERVATION]","",$pagina);
    }

    //GESTIONE COMMENTO SE GIA' ESISTENTE O MENO
    $isUserCommented = $db->GetReview($isUserLogged,$productInfo["id"]);

    if(is_bool($isUserCommented) && !$isUserCommented){
        header("Location: 500.php");
        exit();
    }elseif (is_string($isUserCommented) && $isUserCommented == "No reviews found for this product"){//Se non c'è ancora una recensione dell'utente
        $pagina = str_replace("[COMMENT]",
                "<form method=\"post\" id=\"valutazione\" class=\"form-bianco\">
                    <fieldset>
                        <legend>La tua valutazione del prodotto</legend>
                        <h5 class=\"form-label\">Valutazione</h5>
                        <div class=\"rating\" role=\"radiogroup\" aria-label=\"Valutazione da 1 a 5 stelle\">
                            <input type=\"radio\" name=\"rating\" id=\"star5\" value=\"5\">
                            <label for=\"star5\" title=\"5 stelle\" id=\"cinque-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star4\" value=\"4\">
                            <label for=\"star4\" title=\"4 stelle\" id=\"quattro-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star3\" value=\"3\">
                            <label for=\"star3\" title=\"3 stelle\" id=\"tre-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star2\" value=\"2\">
                            <label for=\"star2\" title=\"2 stelle\" id=\"due-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star1\" value=\"1\">
                            <label for=\"star1\" title=\"1 stella\" id=\"una-stella\">&#9733;</label>
                        </div>
                        <small class=\"descrizione-quantita\">Esprimi la tua valutazione del prodotto selezionando da 1 a 5 stelle.</small>

                        <label for=\"user-comment\" class=\"form-label\">Scrivi qui il tuo commento</label>
                        <details open>
                            <summary id=\"hints-comment\" class=\"hints\" role=\"button\" aria-expanded=\"false\" aria-controls=\"hint-list-comment\">
                                Suggerimenti Commento
                            </summary>
                            <ul class=\"suggestions-list\" id=\"hint-list-comment\">
                                <li id=\"min-char-comment\">Minimo 30 caratteri</li>
                                <li id=\"max-char-comment\">Massimo 300 caratteri</li>
                            </ul>
                        </details>
                        <textarea name=\"commento\" placeholder=\"Scrivi qui il tuo commento sul prodotto.\" id=\"user-comment\" required minlength=\"30\" maxlength=\"300\"></textarea>
                        [comment-error]
                    </fieldset>
                    <div class=\"button-container\">
                        <button type=\"reset\" class=\"bottoni-neri\" id=\"reset-comment\">Annulla</button>
                        <button type=\"submit\" class=\"bottoni-rossi\" id=\"submit-comment\" name=\"submit-user-comment\">Conferma</button>
                    </div>
                </form>",
    $pagina);
    

    $pagina = str_replace("
                <dl class=\"singleproduct-rating\">
                    <dt>Data</dt>
                    <dd>[Data Valutazione]</dd>
                    <dt>Valutazione</dt>
                    <dd class=\"rating-stars\">
                        <span aria-hidden=\"true\"></span>
                    </dd>
                    <dt>Commento</dt>
                    <dd>[Commento]</dd>
                </dl>","",$pagina);

    $pagina = str_replace("<form method=\"post\" id=\"elimina-valutazione\">
                    <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                    <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                    <div class=\"button-container\">
                        <button type=\"submit\" aria-label=\"Elimina Valutazione\" class=\"bottoni-rossi\" name=\"delete-review\">Elimina Valutazione</button>
                    </div>
                </form>","",$pagina);

    $pagina = str_replace("[OLD_COMMENT]","",$pagina);

    }else{//Se l'utente ha già lasciato una recensione

        $data = new DateTime($isUserCommented["data"]);
        $pagina = str_replace("[OLD_COMMENT]", "
                <h5 id=\"tua-rating\">La tua valutazione</h5>
                <dl class=\"singleproduct-rating\">
                    <dt>Data</dt>
                    <dd>[Data Valutazione]</dd>
                    <dt>Valutazione</dt>
                    <dd class=\"rating-stars\">
                        [Valutazione]
                    </dd>
                    <dt>Commento</dt>
                    <dd>[Commento]</dd>
                </dl>

                <form method=\"post\" id=\"elimina-valutazione\">
                    <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                    <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                    <div class=\"button-container\">
                        <button type=\"submit\" aria-label=\"Elimina Valutazione\" class=\"bottoni-rossi\" name=\"delete-review\">Elimina Valutazione</button>
                    </div>
                </form>", $pagina);
        $pagina = str_replace("<h4 class=\"no-print left\">Inserisci Valutazione e Commento</h4>[COMMENT]","",$pagina);
        $pagina = str_replace("[Data Valutazione]",'<time datetime="'.$data->format("Y-m-d"). '">'.$data->format("d/m/Y").'</time>',$pagina);
        $pagina = str_replace("[Commento]",htmlspecialchars($isUserCommented["commento"]),$pagina);
        $pagina = str_replace("[voto]",$isUserCommented["voto"],$pagina);
        $pagina = str_replace("<h4 class=\"no-print left\">Inserisci Valutazione e Commento</h4>","",$pagina);
        $pagina = str_replace("[COMMENT]","",$pagina);

        $templateValutazione = '<span aria-hidden="true">';

        for ($i=0; $i < $isUserCommented["voto"]; $i++) { 
            $templateValutazione .= '★';
        }

        for ($i=$isUserCommented["voto"]; $i < 5; $i++) { 
            $templateValutazione .= '☆';
        }

        $templateValutazione.= '</span> ('.$isUserCommented["voto"].' su 5)';

        $pagina = str_replace("[Valutazione]",$templateValutazione,$pagina);
    }

    //FORM PRENOTAZIONE
    if(isset($_POST["submit-reservation"])){
        $errorFound = false;

        $quantity = (int)$_POST["quantita"];
        if($quantity < $productInfo["min_prenotabile"]){
            $errorFound = true; 
            $pagina = str_replace("[quantity-error]","<p class=\"error\" id=\"quantity-error\">La quantità di prodotto che puoi prenotare deve essere superiore o uguale a ".$productInfo["min_prenotabile"]." ".$productInfo["unita"]."</p>",$pagina);
        }elseif($quantity > $productInfo["max_prenotabile"]){
            $errorFound = true; 
            $pagina = str_replace("[quantity-error]","<p class=\"error\" id=\"quantity-error\">La quantità di prodotto che puoi prenotare deve essere inferiore o uguale a ".$productInfo["max_prenotabile"]." ".$productInfo["unita"]."</p>",$pagina);
        }else{
            $pagina = str_replace("[quantity-error]","",$pagina);
        }


        $date_reservation = $_POST["data_ritiro"];
        $today = new DateTime();
        $today1 = new DateTime();
        $tomorrow = (clone $today)->modify('+1 day');
        $limit_date = (clone $today)->modify('+14 day');
        $reservationDate = new DateTime($date_reservation);
        if($reservationDate <= $today){
            $errorFound = true; 
            $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">L'ordine può essere ritirato solo nei giorni successivi ad oggi: ". $today1->format("d-m-Y")."</p>", $pagina);
        }else if($reservationDate > $limit_date){
            $errorFound = true; 
            $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">L'ordine può essere ritirato solo dal ". $tomorrow->format("d-m-Y")." al ".$limit_date->format("d-m-Y")."</p>", $pagina);         
        }

        if($errorFound == true){//ci sono stati errori
            $pagina = str_replace("min=\"1\"","min=\"1\" value=\"".$quantity."\"", $pagina);
            $pagina = str_replace("name=\"data_ritiro\"","name=\"data_ritiro\" value=\"".$date_reservation."\"", $pagina); 
            header('Location: prodotto.php?prodotto='. $productInfo["id"] . '#prenotazione');
        }else{
            $addReservation = $db->AddReservation($productInfo["id"], $quantity, $date_reservation);
            if(is_bool($addReservation) && $addReservation == true) {//controllo se la modifica delle informazioni è andata a buon fine
                $pagina = str_replace("[date-error]","", $pagina);
                header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');
                exit();
            }elseif(is_string($addReservation) && $addReservation == "User already has a reservation for this product on the selected date"){
                $pagina = str_replace("[date-error]","<p class=\"error\" id=\"date-error\">È già presente una prenotazione per questo prodotto nella data selezionata: ".$date_reservation."</p>",$pagina);
            } else { 
                header('Location: 500.php');
                exit();
            }
        }

    }

    //FORM COMMENTO
    if(isset($_POST["submit-user-comment"])){
        $errorFound = false;
        $grade = isset($_POST["rating"]) ? (Sanitizer::IntFilter($_POST["rating"])) : 0;// voto minimo
        $comment = isset($_POST["commento"]) ? (Sanitizer::SanitizeText($_POST["commento"])) :'';
        unset($_POST["commento"]);

        if(mb_strlen($comment)<30) {//controlla la lunghezza minima del commento
            $errorFound = true;
            $pagina =str_replace("[comment-error]","<p role=\"alert\" class=\"error\" id=\"comment-error\">La lunghezza minima del commento non deve essere inferiore ai 30 caratteri</p>",$pagina);
        }else if(mb_strlen($comment)>300){//controlla la lunghezza massima del messaggio
            $errorFound = true;
            $pagina = str_replace("[comment-error]","<p role=\"alert\" class=\"error\" id=\"comment-error\">La lunghezza massima del commento non deve superare i 300 caratteri</p>",$pagina);
        }else{
            $pagina = str_replace("[comment-error]","",$pagina);
        }

        if($errorFound == false) {//non ci sono errori
            $addReview = $db->AddReview(Sanitizer::SanitizeText($comment), Sanitizer::IntFilter($grade), $productInfo['id']);
            if(is_bool($addReview) && $addReview == true) {//controllo se l'aggiunta della recensione è andata a buon fine
                header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');//reindirizza alla pagina del prodotto
                exit();
            } else if(is_string($addReview) && ($addReview == "Execution error" || $addReview == "Connection error")){ 
                header('Location: 500.php');
                exit();
            }else if(is_string($addReview) && $addReview == "User has already reviewed this product"){
                header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');//reindirizza alla pagina del prodotto
                exit();
            }
            header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');
            exit();
        }else{//ci sono errori
            $pagina = str_replace("Scrivi qui il tuo commento sul prodotto.",$comment,$pagina);
        }
    }

    if(isset($_POST["delete-review"])){//Se l'utente intende eliminare la propria recensione
        $deleteReview = $db->DeleteOneReview($productInfo["id"]);
        if(is_bool($deleteReview) && $deleteReview == true) {
            header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');//reindirizza alla pagina del prodotto
            exit();
        }elseif(is_string($deleteReview) && ($deleteReview == "Connection error"|| $deleteReview == "Execution error")){
            header('Location: 500.php');
            exit();
        }elseif(is_string($deleteReview) && $deleteReview == "User is not logged in"){
            header('Location: login.php');
            exit();
        }
        header('Location: prodotto.php?prodotto='. $productInfo["id"] . '');
        exit();
    }

    if(!isset($_POST['submit-user-comment']) || !isset($_POST['submit-reservation'])){
        $pagina = str_replace("[comment-error]","",$pagina);
        $pagina = str_replace("[quantity-error]","",$pagina);
        $pagina = str_replace("[date-error]","",$pagina);
    }
}

    echo $pagina;

?>