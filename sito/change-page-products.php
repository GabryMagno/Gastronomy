<?php

require_once("php/db.php");
require_once("php/sanitizer.php");


class ChangePageProducts
{
    private int $current_page = 1; // Pagina corrente, inizializzata a 1
    private int $pages = 1; // Numero totale di pagine, inizializzato a 1
    private DB $db;

    private array $filter_list;

    private ?string $productName;
    private array $productCategory;
    private array $specialization;// Se il prodotto è vegano, vegetariano, senza glutine, etc.
    private ?int $grade;
    private ?int $cost;
    private ?string $order; //come vengono ordinati i prodotti (per prezzo, per voto, etc.)

    private const ITEMS_PER_PAGE = 6;

    public function __construct(DB $db, ?string $productName, array $productCategory, array $specialization, ?int $grade, ?int $cost, ?string $order){// Inizializza la classe con i parametri di ricerca e filtro
        $this->db = $db;
        $this->filter_list = array();

        $this->productName = $productName;
        $this->productCategory = $productCategory;

        $this->specialization = $specialization;

        $this->grade = $grade;
        $this->cost = $cost;
        $this->order = $order;
    }

    public function GetCurrentPage(int $page): array{// Prende la pagina corrente di prodotti in base ai filtri e alla pagina richiesta,
    //  coalesce serve per evitare errori se non ci sono valutazioni, ceiling serve per arrotondare il voto medio
        $query = "SELECT p.url_immagine,p.nome,CEILING(AVG(coalesce(v.voto,5))) as voto,p.categoria,p.prezzo
                FROM prodotti as p LEFT JOIN valutazioni as v ON p.nome = v.nome_prodotto
                WHERE 1=1 ";
        $params = [];

        if ($this->productName) {// Filtra per nome del prodotto
            $query .= " and REPLACE(p.nome, \" \", \"\") LIKE REPLACE(?, \" \", \"\") ";// query per evitare errori con gli spazi
            $params[] = "%" . $this->productName . "%";
            $this->filter_list["name"] = $this->productName;
        }

        if (!empty($this->productCategory)) {// Filtra per categoria del prodotto (bisogna farne uno per ogni categoria)
            $colMap = [
                "Antipasto" => "antipasto",
                "Primo" => "primo",
                "Secondo" => "secondo",
                "Contorno" => "contorno",
                "Dolce" => "dolce",
            ];

            $placeholders = array();
            $params_category = array();

            foreach ($this->productCategory as $filter) {
                if (isset($colMap[$filter])) {
                    $placeholders[] = "?"; // Aggiunge un placeholder per il filtro
                    $params_category[] = $colMap[$filter]; // Aggiunge il parametro per la categoria
                    $this->filter_list[$filter] = $colMap[$filter]; // Aggiunge il filtro alla lista dei filtri, traccia il filtro applicato
                }
            }

            if (!empty($placeholders)) {
                $query .= " AND p.categoria IN (" . implode(",", $placeholders) . ")"; // Aggiunge la condizione per il filtro
                $params = array_merge($params, $params_category); // Aggiunge i parametri per le categorie
            }
        }

        if(!empty($this->specialization)){
            //colMap è un array che mappa le specializzazioni ai campi della tabella ingredienti
            //in questo modo si può usare un'unica query per filtrare per specializzazione
            $colMap = [
                "Vegano" => "isVegano",
                "Vegetariano" => "isVegetariano",
                "Celiaco" => "isCeliaco"
            ];

            $havingPart = array();

            foreach ($this->specialization as $filter) {
                if(isset($colMap[$filter])) {
                    //$col = $colMap[$filter];
                    $havingPart[] = "MIN(i." . $colMap[$filter] . ") = 1";// Aggiunge la condizione per il filtro
                    $this->filter_list[$filter] = $filter; // Aggiunge il filtro alla lista dei filtri
                }
            }

            if(!empty($havingPart)) {
                $query .= " and p.nome IN (
                    SELECT pi.prodotto FROM prodotto_ingredienti as pi
                    JOIN ingredienti as i ON pi.ingrediente = i.nome
                    GROUP BY pi.prodotto
                    HAVING " . implode(" AND ", $havingPart) . "
                )";
            }
        }

        if ($this->cost) {// Filtra per prezzo del prodotto
            $query .= " and p.prezzo <= ? ";
            $params[] = $this->cost;
            $this->filter_list["price"] = $this->cost; 
        }

        $query .= "GROUP BY p.nome ";

        if ($this->grade) {// Filtra per voto del prodotto
            $query .= " HAVING AVG(coalesce(v.voto,3)) >= ? ";
            $params[] = $this->grade;
            $this->filter_list["rating"] = $this->grade; // //rating è il name degli input per il voto(da 1 a 5 stelle)
        }
        $order_query = "ORDER BY ";
        
        if ($this->productName) {
            $order_query .= "LOCATE(?, p.nome),";
            $params[] = $this->productName;
        }
        
        
        if($this->order) {// Se l'utente ha scelto un ordine specifico 
            $this->filter_list["ordina"] = $this->order;
        }

        switch ($this->order) {
            case 'prezzo_basso':
                $order_query .= "p.prezzo ASC";
            break;
                

            case 'prezzo_alto':
                $order_query .= "p.prezzo DESC";
            break;

            case 'voto_basso':
                $order_query .= "v.voto ASC";
            break;
                

            case 'voto_alto':
                $order_query .= "v.voto DESC";
            break;

            default:
                $order_query .= "v.voto DESC";
                        break;
        }


        $order_query.= ",p.nome ASC";// Aggiunge l'ordinamento per nome della ricetta come fallback

        $query .= $order_query;
        
        $results = $this->db->GetProducts($query, $params);// Esegue la query per ottenere i prodotti filtrati

        $this->pages = ceil(($results ? count($results) : 1) / ChangePageProducts::ITEMS_PER_PAGE);// Calcola il numero totale di pagine in base al numero di risultati e agli elementi per pagina
        $this->current_page = max(1,min($page, $this->pages));// Imposta la pagina corrente, assicurandosi che sia compresa tra 0 e il numero totale di pagine

        if ($results) {
            return array_slice($results, ChangePageProducts::ITEMS_PER_PAGE * ($page - 1), ChangePageProducts::ITEMS_PER_PAGE);// Restituisce i risultati della pagina corrente, limitando il numero di elementi per pagina
        }

        return $results ? : [];// Se non ci sono risultati, restituisce un array vuoto

    }

    public function GetParamList(): array // Restituisce la lista dei filtri applicati
    {
        return $this->filter_list;
    }

    public function CreateButtons(): string
    {
        return $this->CreatePageButtons($this->current_page, $this->pages, $this->filter_list);
    }

    private function CreatePageButtons(int $currentPage, int $totalPages, $filters_list): string
    {
        $previous_button = ($currentPage > 1) ? '<button id="prev" name="page" value="' . max(1,min($currentPage - 1, $totalPages)) . '">Pagina precedente</button>' : "";
        $next_button = ($currentPage < $totalPages) ? '<button id="next" name="page" value="' . max(1,min($currentPage + 1, $totalPages)). '">Pagina successiva</button>' : "";
        $TEMPLATE = $previous_button ."<p id=\"current-page\">" . $currentPage . ' su ' . $totalPages . " </p>" . $next_button;//<abbr title="su">/</abbr> altrimenti al posto di 'su'
        $HIDDEN = "";
        while ($value = current($filters_list)) {
            $HIDDEN .= "<input type=\"hidden\" name=" . key($filters_list) . " value=\"" . $value . "\"/>";// crea un input hidden per ogni filtro applicato
            next($filters_list);
        }
        return $TEMPLATE . $HIDDEN;
    }

}
?>