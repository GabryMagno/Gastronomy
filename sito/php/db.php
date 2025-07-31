<?php

session_start();

class DB {
    private const DB_NAME = "";
    private const USERNAME = "";
    private const PASSWORD = "";
    private const HOST = "";
    private $connection;

    private function OpenConnectionDB(): bool{

        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            this->connection = mysqli_connect(self::DB_NAME, self::USERNAME, self::PASSWORD, self::HOST);
        } catch(\mysqli_sql_exception $error){//forse e al posto di error
            return false;
        }
        return true;

    }

    private function CloseConnectionDB(): void{

        mysqli_close(this->connection);

    }

    private function IsUserLog(): bool | string{

        if(isset($_SESSION["logged_user"]) && $_SESSION != null) return $_SESSION["logged_user"];
        else return false;

    }
    
    //string per gli errori
    //AZIONI GENERALI

    public function RegisterNewUser($username, $name, $surname, $date, $email, $password): bool{}//registrazione nuovo utente (da aggiungere nel corpo della funzione la data d'iscrizione)

    public function LoginUser($username, $password): bool | string{}//login utente

    public function LogoutUser(): bool | string{}//logout utente

    public function DeleteUser(): bool | string{}//eliminazione utente

    //OTTENERE INFO UTENTE

    public function GetUserFavoritesProducts(): array | string{}//ottenere prodotti preferiti

    public function GetUserReservation(): array | string{}//ottenere prenotazione prodotti

    public function GetUserTastings(): array | string{}//ottenere prenotazione degustazioni

    public function GetUserReviews(): array | string{}//ottenere recensioni scritte

    public function GetUserAdvices(): array | string{}//ottenere suggerimenti scritti

    //ELIMINAZIONI DA PARTE DELL'UTENTE

    public function DeleteAllFavoritesProducts(): bool | string {}//cancellare tutti i prodotti preferiti

    public function DeleteOneFavoriteProduct($product): bool | string{}//cancellare un singolo prodotto preferito --> da vedere cos'è product

    public function DeleteAllReservations(): bool | string {}//cancellare tutte le prenotazioni dei prodotti

    public function DeleteOneReservation($reservation): bool | string{}//cancellare una singola prenotazione di un prodotto --> da vedere cos'è reservation

    public function DeleteAllTastings(): bool | string {}//cancellare tutte le degustazioni prenotate

    public function DeleteOneTasting($tasting): bool | string{}//cancellare una singola degustazione --> da vedere cos'è tasting

    public function DeleteAllReviews(): bool | string {}//cancellare tutte le recensioni

    public function DeleteOneReview($review): bool | string{}//cancellare una singola recensione  --> da vedere cos'è review

    //AGGIUNTE DA PARTE DELL'UTENTE
    //Al posto di username->logged_user da isUserLog() (da scegliere)

    public function AddAdvice($username, $advice): bool | string{}//Aggiunta da parte dell'utente di un suggerimento

    public function AddReview($username, $comment, $grade, $product): bool | string{}//Aggiunta da parte dell'utente di un commento e valutazione di un prodotto

    public function AddFavoriteProduct($username,$product): bool | string{}//Aggiunta di un prodotto preferito da parte dell'utente

    public function AddReservation($username, $product, $quantity, $date): bool | string{}//Aggiunta da parte dell'utente di una prenotazione rispetto ad un prodotto indicando quantità dello stesso e quando (data) ritirarlo

    public function AddTasting($username, $tasting, $people_number, $date): bool | string{}//Aggiunta di una prenotazione per una degustazione da parte dell'utente per un tot di persone e in una certa data

    //CAMBIO DELLE IMPOSTAZIONI

    public function ChangePassword($oldPassword, $newPassword): bool | string{}//Cambio password da parte dell'utente

    public function ChangeMainInfo($username, $name, $cognome, $date, $logo): bool | string{}//Cambio informazioni personali da parte dell'utente

    //ALTRO
    public function AverageGradeProduct($product): int | string{}//Restituisce il voto medio del prodotto
}
?>
