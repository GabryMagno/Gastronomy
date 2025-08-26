<?php
require_once "php/db.php";
require_once "php/sanitizer.php";

$db = new DB;

$pagina = file_get_contents("html/index.html");

if($db->isUserLog()!=false) {
    $pagina = str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);
} else {
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina); 
}

$products = $db->GetBestProducts();// prendi i prodotti della pagina corrente

$d_products = "";// stringa che conterrà i prodotti da mostrare
if ($products != null) {// se ci sono prodotti, crea le brochure per ogni prodotto
    foreach ($products as $product) {// per ogni prodotto
        $d_products .= CreateBestProductBrochure($product["url_immagine"], $product["nome"], $product["prezzo"], $product["id"]);
    }
} else {// altrimenti mostra un messaggio di errore
    $d_products = "<p id=\"evidenza-nondisponibile\">Non abbiamo prodotti in evidenza</p>";
}

$pagina = str_replace("[BEST PRODUCTS]", $d_products, $pagina);

echo $pagina;

function CreateBestProductBrochure(string $img, string $title, float $cost, int $id): string{
    global $db;
    
    $TEMPLATE = '<li class="new">
                    <article>
                        <img loading="lazy" src="'. $img . '" alt=""/>
                        <h4 class="product-name">' . $title . '</h4>
                    
                        <p>Voto: '. $db->AverageGradeProduct($id) .' su 5</p>
                        <p>Prezzo: '. $cost . '&euro;</p>
                        
                        <a href="./prodotto.php?prodotto='. urlencode($id) . '" title="vai al prodotto ' . Sanitizer::SanitizeGenericInput($title) . '">Scheda del prodotto</a>
                    </article>
                </li>';
    return $TEMPLATE;
}
?>