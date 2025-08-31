<?php

require_once("php/db.php");
require_once("php/sanitizer.php");


class ChangePageTastings{
    private int $current_page = 1; // Pagina corrente, inizializzata a 1
    private int $pages = 1; // Numero totale di pagine, inizializzato a 1
    private DB $db;

    private array $filter_list;

    private ?string $order; //come vengono ordinati i prodotti (per prezzo, per voto, etc.)

    private const ITEMS_PER_PAGE = 5;

    public function __construct(DB $db, ?string $order){// Inizializza la classe con i parametri di ricerca e filtro
        $this->db = $db;
        $this->filter_list = array();
        $this->order = $order;
    }

    public function GetCurrentPage(int $page): array{// Prende la pagina corrente di prodotti in base ai filtri e alla pagina richiesta,
    //  coalesce serve per evitare errori se non ci sono valutazioni, ceiling serve per arrotondare il voto medio
        $query = "SELECT d.id as id_degustazione, d.id_prodotto as id_prodotto, d.data_inizio as data_inizio, d.data_fine as data_fine, p.url_immagine as url_immagine, p.nome as nome, d.prezzo as prezzo, d.disponibilita_persone as disponibilita_persone, d.descrizione as descrizione
                FROM degustazioni as d LEFT JOIN prodotti as p ON d.id_prodotto = p.id
                WHERE 1=1 ";
        $params = [];
           
        if($this->order) {// Se l'utente ha scelto un ordine specifico 
            $this->filter_list["ordina"] = $this->order;
        }

        switch ($this->order) {
            case 'degustazioni_passate':
                $query .= "AND d.data_fine < NOW() ORDER BY d.data_inizio ASC, p.nome ASC";
            break;    

            case 'degustazioni_attuali':
                $query .= "AND d.data_fine >= NOW() ORDER BY d.data_inizio ASC, p.nome ASC";
            break;

            case 'degustazioni_future':
                $query .= "AND d.data_inizio > NOW() ORDER BY d.data_inizio ASC, p.nome ASC";
            break;
                

            case 'tutte_le_degustazioni':
                $query .= "ORDER BY CASE
                        WHEN d.data_fine < NOW() THEN 1
                        WHEN d.data_fine >= NOW() AND d.data_inizio <= NOW() THEN 2
                        WHEN d.data_inizio > NOW() THEN 3
                        ELSE 4
                        END, d.data_inizio ASC, p.nome ASC";
            break;

            default:
                $query .= "AND d.data_fine >= NOW() ORDER BY d.data_inizio ASC";
            break;
        }

        $results = $this->db->GetProducts($query, $params);// Esegue la query per ottenere i prodotti filtrati

        $this->pages = ceil(($results ? count($results) : 1) / ChangePageTastings::ITEMS_PER_PAGE);// Calcola il numero totale di pagine in base al numero di risultati e agli elementi per pagina
        $this->current_page = max(1,min($page, $this->pages));// Imposta la pagina corrente, assicurandosi che sia compresa tra 0 e il numero totale di pagine

        if ($results) {
            return array_slice($results, ChangePageTastings::ITEMS_PER_PAGE * ($page - 1), ChangePageTastings::ITEMS_PER_PAGE);// Restituisce i risultati della pagina corrente, limitando il numero di elementi per pagina
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