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
if ($products != null && count($products) > 0) {// se ci sono prodotti, crea le brochure per ogni prodotto
    foreach ($products as $product) {// per ogni prodotto
        $d_products .= CreateBestProductBrochure($product["url_immagine"], $product["nome"], $product["prezzo"], $product["id"]);
    }
} else {// altrimenti mostra un messaggio di errore
    $d_products = "<p id=\"evidenza-nondisponibile\" class=\"error\">Non abbiamo prodotti in evidenza.</p>";
}

$comments = $db->GetBestComments();// prendi i prodotti della pagina corrente

$b_comments = "";// stringa che conterrà i prodotti da mostrare
if ($comments != null && count($comments) > 0) {// se ci sono prodotti, crea le brochure per ogni prodotto
    foreach ($comments as $comment) {// per ogni prodotto
        $b_comments .= createBestCommentTemplate($comment["username"], $comment["url_immagine"], $comment["voto"], $comment["commento"], $comment["nome_prodotto"], new DateTime($comment["data"]));
    }
} else {// altrimenti mostra un messaggio di errore
    $b_comments = "<p id=\"evidenza-nondisponibile\" class=\"error\">Al momento non sono presenti recensioni da mostrare.</p>";
}

$pagina = str_replace("[BEST PRODUCTS]", $d_products, $pagina);
$pagina = str_replace("[BEST COMMENTS]", $b_comments, $pagina);

echo $pagina;

function createBestCommentTemplate($username, $immagine, $voto, $commento, $prodotto, DateTime $data){
    if($immagine == null || $immagine == "") $immagine = "assets/img/users_logos/default.webp";
    
    $TEMPLATE = '
        <div class="recensione-card">
            <div class="recensione-foto">
                <img loading="lazy" src="' . $immagine . '" alt="Foto profilo di ' . $username . '"/>
            </div>
            <div class="recensione-contenuto">
                <h4 class="recensione-cliente">' . $username . '</h4>
                <p class="recensione-data">
                    <time datetime="' . $data->format("Y-m-d") . '">' . $data->format("d/m/Y") . '</time>
                </p>
                <p class="recensione-prodotto">'. $prodotto . '</p>
                <p class="recensione-testo">' . $commento . '</p>
                <p class="recensione-valutazione">Voto: <span aria-hidden="true">';

    for ($i=0; $i < $voto; $i++) { 
        $TEMPLATE .= '★';
    }

    for ($i=$voto; $i < 5; $i++) { 
        $TEMPLATE .= '☆';
    }

    $TEMPLATE .= '</span> ('.$voto.' su 5)';

    $TEMPLATE .= '</p></div></div>';
    return $TEMPLATE;
}

function CreateBestProductBrochure(string $img, string $title, float $cost, int $id): string{
    global $db;
    
    $TEMPLATE = '<li class="new">
                    <article>
                        <img loading="lazy" src="'. $img . '" alt=""/>
                        <h4 class="product-name">' . $title . '</h4>

                        <p>Voto: '. (number_format((float)$db->AverageGradeProduct($id),1) == 0 ? "X" : number_format((float)$db->AverageGradeProduct($id),1)) .' su 5</p>
                        <p>Prezzo: '. $cost . '&euro;</p>
                        
                        <a href="./prodotto.php?prodotto='. urlencode($id) . '" title="vai al prodotto ' . Sanitizer::SanitizeGenericInput($title) . '">Scheda del prodotto</a>
                    </article>
                </li>';
    return $TEMPLATE;
}
?>