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
    }
    unset($_GET["prodotto"]);

    $ingredients = $db->GetProductIngredients($productInfo["id"]);
    $ingredientsHTML = "";
    if(is_string($ingredients) && ($ingredients == "Execution error" || $ingredients == "Connection error")) {
        header('Location: 500.php');
        exit();
    }
    if(is_string($ingredients) && $ingredients == "No ingredients found for this product") $ingredients = "";
    else{
        foreach($ingredients as $ingredient){
            $ingredientsHTML .= "<li>". $ingredient["quantita"] . (($ingredient["unita_misura"] == "num_el" || $ingredient["unita_misura"] == null)? "" : $ingredient["unita_misura"]. " di") ."  ". $ingredient["ingrediente"] ."</li>";
        }
    }

    $pagina = str_replace("[IMAGE]","<img src=". $productInfo["url_immagine"] ." alt=\"\">",$pagina);
    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[Categoria]",ucfirst($productInfo["categoria"]),$pagina);
    $pagina = str_replace("[Valutazione]",$db->AverageGradeProduct($productInfo["id"]),$pagina);
    $pagina = str_replace("[Prezzo]",$productInfo["prezzo"],$pagina);
    $pagina = str_replace("[Descrizione]",$productInfo["descrizione"],$pagina);
    $pagina = str_replace("[INGREDIENTI]",$ingredientsHTML,$pagina);

}else{
    header("Location: prodotti.php");
    exit();
}

$isUserLogged = $db->IsUserLog();

if(is_bool($isUserLogged) && $isUserLogged == false){
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
                    <dd class=\"rating-stars\" aria-label=\"Valutazione: [voto] su 5 stelle\">
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
    $pagina = str_replace("[COMMENT]","<p id=\"comment-log\">Se desideri commentare questo prodotto, cosa aspetti fai il <a href=\"login.php?reference-product=".urldecode($product)."\"><span lang=\"en\">LOGIN</span></a> oppure <a href=\"register.php?reference-product=".urldecode($product)."\"> REGISTRATI</a></p>",$pagina);
    $pagina = str_replace("[RESERVATION]","<p id=\"reservation-log\">Se desideri prenotare questo prodotto, cosa aspetti fai il <a href=\"login.php?reference-product=".urldecode($product)."\"><span lang=\"en\">LOGIN</span></a> oppure <a href=\"register.php?reference-product=".urldecode($product)."\"> REGISTRATI</a></p>",$pagina);
}else{
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
        $pagina = str_replace("[RESERVATION]",
    "<form method=\"post\" id=\"prenotazione\" class=\"form-bianco\">
                        <fieldset>
                            <legend>Prenotazione Prodotto</legend>

                            <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                            <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                            <label for=\"quantita\" class=\"form-label\">Quantità da prenotare</label>
                            <div id=\"quantita-unita\">
                                <input type=\"number\" id=\"quantita\" name=\"quantita\" min=\"1\" max=\"".(int)$productInfo["max_prenotabile"]."\" value=\"[quantita-ordine]\"required>
                                <span class=\"unita\">[Unita]</span>
                                
                            </div>
                            <small class=\"descrizione-quantita\">Puoi prenotare da [min_prenotabile] a [max_prenotabile] unit&agrave;.</small>
                            [quantity-error]

                            <div>
                                <label for=\"data-ritiro\" class=\"form-label\" id=\"order-label\">Data di ritiro</label>
                                <input type=\"date\" id=\"data-ritiro\" name=\"data_ritiro\" value=\"[data-ordine]\" required>
                                [date-error]
                            </div>

                            <div class=\"button-container\">
                                <button type=\"submit\" aria-label=\"Prenota Prodotto\" class=\"bottoni-rossi\" id=\"submit-order\" name=\"submit-reservation\">Prenota</button>
                            </div>
                        </fieldset>
                    </form>",
    $pagina);
    
    $pagina = str_replace("[Unita]",$productInfo["unita"],$pagina);
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
    }elseif (is_string($isUserCommented) && $isUserCommented == "No reviews found for this product"){
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
                    <dd class=\"rating-stars\" aria-label=\"Valutazione: [voto] su 5 stelle\">
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
    }else {
        $data = new DateTime($isUserCommented["data"]);
        $pagina = str_replace("<h4 class=\"no-print left\">Inserisci Valutazione e Commento</h4>[COMMENT]","",$pagina);
        $pagina = str_replace("[Data Valutazione]",$data->format("d/m/Y"),$pagina);
        $pagina = str_replace("[Commento]",$isUserCommented["commento"],$pagina);
        $pagina = str_replace("[voto]",$isUserCommented["voto"],$pagina);
        $pagina = str_replace("[Valutazione]",$isUserCommented["voto"],$pagina);
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
        $reservationDate = new DateTime($date_reservation);
        if($reservationDate <= $today){
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

    //FORM COMMENTO
    if(isset($_POST["submit-user-comment"])){
        $errorFound = false;
        $grade = isset($_POST["rating"]) ? Sanitizer::SanitizeGenericInput(Sanitizer::IntFilter($_POST["rating"])) : 0;// voto minimo
        $comment = isset($_POST["commento"]) ? Sanitizer::SanitizeGenericInput(Sanitizer::SanitizeText($_POST["commento"])) :'';
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
            $addReview = $db->AddReview($comment, $grade, $productInfo['id']);
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
    }
    //SEZIONE COMMENTI ALTRI UTENTI

    if(!isset($_POST['submit-user-comment']) || !isset($_POST['submit-reservation'])){
        $pagina = str_replace("[comment-error]","",$pagina);
        $pagina = str_replace("[quantity-error]","",$pagina);
        $pagina = str_replace("[date-error]","",$pagina);
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
                        <span class=\"diet-label\">Vegano</span>","",$pagina);
        }

        if($infoGenreProduct["vegetariano"] == 0){
            $pagina = str_replace(" <img src=\"assets/img/icone/vegetariano-verde.svg\" alt=\"icona verde con indicazione prodotto vegetariano\">
                        <span class=\"diet-label\">Vegetariano</span>","",$pagina);
        }

        if($infoGenreProduct["celiaco"] == 0){
            $pagina = str_replace("<img src=\"assets/img/icone/celiaco-verde.svg\" alt=\"icona verde con indicazione prodotto per persone celiache\">
                        <span class=\"diet-label\">Celiaco</span>","",$pagina);
        }
    }
}

    echo $pagina;

?>