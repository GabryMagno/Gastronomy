<?php

require_once "php/db.php";
require_once "php/sanitizer.php";
require_once "change-page-products.php";

$db = new DB;

$productName = isset($_GET['name']) ? Sanitizer::SanitizeGenericInput($_GET['name']) : null;
//Con productCategory array delle varie scelte per il tipo di prodotto (antipasto, primo, secondo, contorno, dolce)
$productCategory = [isset($_GET['Antipasto']) ? Sanitizer::SanitizeUserInput($_GET["Antipasto"]) : null,
                    isset($_GET['Primo']) ? Sanitizer::SanitizeUserInput($_GET["Primo"]) : null,
                    isset($_GET['Secondo']) ? Sanitizer::SanitizeUserInput($_GET["Secondo"]) : null,
                    isset($_GET['Contorno']) ? Sanitizer::SanitizeUserInput($_GET["Contorno"]) : null,
                    isset($_GET['Dolce']) ? Sanitizer::SanitizeUserInput($_GET["Dolce"]) : null
                    ];
// specialization array delle varie scelte per il tipo di prodotto (vegano, vegetariano, senza glutine)
$specialization = array_filter([isset($_GET['Vegano']) ? Sanitizer::SanitizeUserInput($_GET["Vegano"]) : null,
                    isset($_GET['Vegetariano']) ? Sanitizer::SanitizeUserInput($_GET["Vegetariano"]) : null,
                    isset($_GET['Celiaco']) ? Sanitizer::SanitizeUserInput($_GET["Celiaco"]) : null
                    ]);
$grade = isset($_GET['rating']) ? Sanitizer::SanitizeGenericInput(Sanitizer::IntFilter($_GET['rating'])) : 1;// voto minimo
$cost = isset($_GET['price']) ? Sanitizer::SanitizeGenericInput(Sanitizer::IntFilter($_GET['price'])) : 25;// costo massimo
$order = isset($_GET['ordina']) ? Sanitizer::SanitizeGenericInput($_GET['ordina']) : null;// ordine (per prezzo, per voto)


$pageSystem = new ChangePageProducts($db, $productName, $productCategory, $specialization, $grade, $cost, $order);// crea l'oggetto per gestire il cambio di pagina
$_SESSION["previous-page"]="<a href=\"./prodotti.php\">PRODOTTI</a>";// link alla pagina precedente

$pagina = file_get_contents("html/prodotti.html");

if($db->isUserLog() != false) {// Se l'utente è loggato, mostra il link al profilo, altrimenti mostra il link per il login
    $pagina=str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);//se l'utente è loggato, mostra il link al suo profilo
} else {//altrimenti mostra il link per il login
    $pagina = str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
}

$currentPage = isset($_GET['page']) ? Sanitizer::SanitizeGenericInput(Sanitizer::IntFilter($_GET['page'])) : 1;// pagina corrente

$products = $pageSystem->GetCurrentPage($currentPage);// prendi i prodotti della pagina corrente

$d_products = "";// stringa che conterrà i prodotti da mostrare
if ($products != null) {// se ci sono prodotti, crea le brochure per ogni prodotto
    foreach ($products as $product) {// per ogni prodotto
        $d_products .= CreateProductBrochure($product["url_immagine"], $product["nome"], $product["voto"], $product["prezzo"]);
    }
} else {// altrimenti mostra un messaggio di errore
    $d_products = "<p>Non abbiamo nessun prodotto che soddisfi i criteri di ricerca</p>";
}

foreach (['Antipasto', 'Primo', 'Secondo', 'Contorno', 'Dolce'] as $category) {
    if (in_array($category, $productCategory)) {
        // Aggiunge l'attributo checked
        $pagina = str_replace(
            'value="' . $category . '"',
            'value="' . $category . '" checked',
            $pagina
        );
    }
}
foreach (['Vegano', 'Vegetariano', 'Celiaco'] as $spec) {
    if (in_array($spec, $specialization)) {
        // Aggiunge l'attributo checked se il filtro è stato selezionato
        $pagina = str_replace(
            'value="' . $spec . '"',
            'value="' . $spec . '" checked',
            $pagina
        );
    }
}
for ($i = 1; $i <= $grade; $i++) {
    $pagina = str_replace(
        'id="star'.$i.'"',
        'id="star'.$i.'" checked',
        $pagina
    );
}
$pagina = str_replace("value=\"" . $order . "\"", "value=\"" . $order . "\" selected", $pagina);
$pagina = str_replace("id=\"searching-bar\"", "id=\"searching-bar\" value=\"" . $productName . "\"", $pagina);
$pagina = str_replace("id=\"price\"", "id=\"price\" value=\"" . $cost . "\"", $pagina);
$pagina = str_replace("id=\"select-for\" name=\"ordina\"", "id=\"select-for\" name=\"ordina\" onchange=\"this.form.submit()\"", $pagina);


$pagina = str_replace("[HIDDEN]", CreateOrderChanger($pageSystem->GetParamList()), $pagina);
$pagina = str_replace("[PRODUCTS]", $d_products, $pagina);
$pagina = str_replace("[BUTTONS]", $pageSystem->CreateButtons(), $pagina);
echo $pagina;

function CreateProductBrochure(string $img, string $title, int $grade, int $cost): string{
    global $db;
    
    $TEMPLATE = '<li class="product-brochure">
                    <img loading="lazy" src="'. $img . '" alt=""/>
                    <h4 class="product-name">' . $title . '</h4>
                   
                    <p>Valutazione:  '. $grade .'<abbr title="su">/</abbr> 5</p>
                    <p>Prezzo:  '. $cost . '&euro;</p>
                    
                    <a href="./prodotto.php?prodotto='. urlencode($title) . '" title="vai al prodotto ' . $title . '">Scheda del prodotto</a>
                </li>';
    return $TEMPLATE;
}

function CreateOrderChanger($filters_list)
{
    $HIDDEN = "";
    while ($value = current($filters_list)) {
        $HIDDEN .= "<input type=\"hidden\" name=" . key($filters_list) . " value=\"" . $value . "\"/>";//serve per
        next($filters_list);
    }
    return $HIDDEN;
}

function ParseCategory(string $category): string{
    return strtoupper(str_replace("_", " ", $category));
}
?>