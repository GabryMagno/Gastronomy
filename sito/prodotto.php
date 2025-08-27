<?php

require_once "php/db.php";
require_once "php/sanitizer.php";
require_once "change-page-products.php";

$db = new DB;

$comment = "";
$grade = null;
$quantity_reservation = null;
$date_reservation = null;

$pagina = file_get_contents("html/prodotto.html");
if(isset($_GET["prodotto"])){
    $productInfo = $db->GetProductInfo($_GET["prodotto"]);
    unset($_GET["prodotto"]);

    $pagina = str_replace("[IMAGE]","<img src=". $productInfo["url_immagine"] ." alt=\"\">",$pagina);
    $pagina = str_replace("[Nome Prodotto]",$productInfo["nome"],$pagina);
    $pagina = str_replace("[Categoria]",ucfirst($productInfo["categoria"]),$pagina);
    $pagina = str_replace("[Valutazione]",$db->AverageGradeProduct($productInfo["id"]),$pagina);
    $pagina = str_replace("[Prezzo]",$productInfo["prezzo"],$pagina);
    $pagina = str_replace("[Descrizione]",$productInfo["descrizione"],$pagina);

}else{
    header("Location: prodotti.php");
    exit();
}

$isUserLogged = $db->IsUserLog();

if(is_bool($isUserLogged) && $isUserLogged == false){
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    $pagina = str_replace("[COMMENT]","<p>Se desideri commentare questo prodotto, cosa aspetti fai il <a href=\"login.php\"><span lang=\"en\">LOGIN</span></a> oppure <a href=\"register.php\"> REGISTRATI</a></p>",$pagina);
    $pagina = str_replace("[RESERVATION]","<p>Se desideri prenotare questo prodotto, cosa aspetti fai il <a href=\"login.php\"><span lang=\"en\">LOGIN</span></a> oppure <a href=\"register.php\"> REGISTRATI</a></p>",$pagina);
}else{
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
    $pagina = str_replace("[COMMENT]",
                "<form method=\"post\" id=\"valutazione\" class=\"form-bianco\">
                    <fieldset>
                        <legend>La tua valutazione del prodotto</legend>
                        <h5 class=\"form-label\">Valutazione</h5>
                        <div class=\"rating\" role=\"radiogroup\" aria-label=\"Valutazione da 1 a 5 stelle\">
                            <input type=\"radio\" name=\"rating\" id=\"star1\" value=\"1\">
                            <label for=\"star1\" title=\"1 stella\" id=\"una-stella\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star2\" value=\"2\">
                            <label for=\"star2\" title=\"2 stelle\" id=\"due-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star3\" value=\"3\">
                            <label for=\"star3\" title=\"3 stelle\" id=\"tre-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star4\" value=\"4\">
                            <label for=\"star4\" title=\"4 stelle\" id=\"quattro-stelle\">&#9733;</label>

                            <input type=\"radio\" name=\"rating\" id=\"star5\" value=\"5\">
                            <label for=\"star5\" title=\"5 stelle\" id=\"cinque-stelle\">&#9733;</label>
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
                        
                    </fieldset>
                    <div class=\"button-container\">
                        <button type=\"reset\" class=\"bottoni-neri\" id=\"reset-comment\">Annulla</button>
                        <button type=\"submit\" class=\"bottoni-rossi\" id=\"submit-comment\">Conferma</button>
                    </div>
                </form>",
    $pagina);

    $pagina = str_replace("[RESERVATION]",
    "<form method=\"post\" id=\"prenotazione\" class=\"form-bianco\">
                        <fieldset>
                            <legend>Prenotazione Prodotto</legend>

                            <input type=\"hidden\" name=\"id_utente\" value=\"[id_utente]\">
                            <input type=\"hidden\" name=\"nome_prodotto\" value=\"[nome_prodotto]\">

                            <label for=\"quantita\" class=\"form-label\">Quantità da prenotare</label>
                            <div id=\"quantita-unita\">
                                <input type=\"number\" id=\"quantita\" name=\"quantita\" min=\"1\" max=\"10\" required>
                                <span class=\"unita\">[Unita]</span>
                            </div>
                            <small class=\"descrizione-quantita\">Puoi prenotare da [min_prenotabile] a [max_prenotabile] unit&agrave;.</small>

                            <div>
                                <label for=\"data-ritiro\" class=\"form-label\" id=\"order-label\">Data di ritiro</label>
                                <input type=\"date\" id=\"data-ritiro\" name=\"data_ritiro\" required>
                            </div>

                            <div class=\"button-container\">
                                <button type=\"submit\" aria-label=\"Prenota Prodotto\" class=\"bottoni-rossi\" id=\"submit-order\">Prenota</button>
                            </div>
                        </fieldset>
                    </form>",
    $pagina);
    
    $pagina = str_replace("[Unita]",$productInfo["unita"],$pagina);
    $pagina = str_replace("[min_prenotabile]",$productInfo["min_prenotabile"],$pagina);
    $pagina = str_replace("[max_prenotabile]",$productInfo["max_prenotabile"],$pagina);

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

    if(isset($_POST["remove-favorite"])){
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
}
echo $pagina;


?>