<?php

session_start();

class DB {
    //Costanti per la connessione al database
    //DA MODIFICARE CON I DATI DEL PROPRIO DATABASE
    private const DB_NAME = "";
    private const USERNAME = "";
    private const PASSWORD = "";
    private const HOST = "";
    private $connection;

    private function OpenConnectionDB(): bool{

        mysqli_report(MYSQLI_REPORT_STRICT);
        try{
            //connessione al database
            this->connection = mysqli_connect(self::DB_NAME, self::USERNAME, self::PASSWORD, self::HOST);

        } catch(\mysqli_sql_exception $error){//forse e al posto di error
            //se c'è un errore nella connessione, ritorna false
            return false;
        }
        //se la connessione è avvenuta con successo, ritorna true
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

    public function RegisterNewUser($username, $name, $surname, $date, $email, $password): bool{//registrazione nuovo utente (da aggiungere nel corpo della funzione la data d'iscrizione)

        $encriptedPassword = hash('sha256', $password);
        $subscribe_date=date("Y-m-d h:m:s");
        $id //da creare un id unico per l'utente, ad esempio con un auto increment nella tabella utenti
        $newConnection = $this->OpenConnectionDB();

        if($newConnection){
            $userInfo = this->connection->prepare("INSERT INTO utenti(id, email, username, password, nome, cognome, data_di_nascita, data_iscrizione) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $userInfo->bind_params("isssssss", $id, $email, $username, $encriptedPassword, $name, $surname, $date, $subscribe_date,);

            try{
                $userInfo->execute();
            }catch(\mysqli_sql_exception $error){

                $this->CloseConnectionDB();
                $userInfo->close();
                return false; //errore nell'esecuzione della query

            }

            if(mysqli_affected_rows(this->connection) == 1){
                //se la query ha inserito una riga, allora l'utente è stato registrato con successo
                $this->CloseConnectionDB();
                $userInfo->close();
                $_SESSION["logged_user"] = $id; //impostazione della sessione per l'utente loggato
                return true; //registrazione avvenuta con successo

            } else {

                $this->CloseConnectionDB();
                $userInfo->close();
                return false; //nessuna riga inserita, errore nella registrazione

            }
        }else {

            return false; //errore nella connessione al database

        }
    }

    public function LoginUser($username, $password): bool | string{//login utente

        if(this->IsUserLog() == false) {
            //se l'utente non è loggato, procedi con il login
            $encriptedPassword = hash('sha256', $password);
            $newConnection = $this->OpenConnectionDB();

            if($newConnection){
                //preparazione della query per verificare l'esistenza dell'utente
                isUserExist = this->connection->prepare("SELECT username FROM utenti WHERE nome = ? AND password = ?");
                $isUserExist= bind_params("ss", username, encriptedPassword);
                try{
                    isUserExist->execute();
                }catch(\mysqli_sql_exception $error){

                    $this->CloseConnectionDB();
                    isUserExist->close();
                    return false; //errore nell'esecuzione della query

                }

                $info = isUserExist->get_result();
                this->CloseConnectionDB();
                isUserExist->close();

                //Il server tecweb non controlla il case-sensitive per il database, quindi controllo che il risultato trovato corrisponda esattamente allo username inserito
                if ($result->num_rows==1 && strcmp(mysqli_fetch_assoc($result)["username"],$username)==0) {
                    //se l'utente esiste, procedi con il login
                    $_SESSION["logged_user"] = $id;
                    $result->free(); 
                    return true;

                } else {
                    //se l'utente non esiste, ritorna false
                    $result->free();
                    return false;
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }else{
            return "User already logged in"; //l'utente è già loggato
        }
    }

    public function LogoutUser(): bool | string{//logout utente

        $db = new DB();
        $isUserLogged = $db->IsUserLog();

        if($isUserLogged = false){
            //se l'utente è loggato, procedi con il logout
            unset($_SESSION["logged_user"]); //rimuove la sessione dell'utente loggato
            return true; //logout avvenuto con successo

        }else{
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato

        }

    }

    public function DeleteUser(): bool | string{//eliminazione utente
        $isUserLogged = this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per eliminare l'utente
                $deleteUser = this->connection->prepare("DELETE FROM utenti WHERE id = ?");
                $deleteUser->bind_param("i", $isUserLogged);

                try{
                    //esecuzione della query per eliminare l'utente
                    $deleteUser->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteUser->close();
                    return false; //errore nell'esecuzione della query
                }

                $result = $deleteUser->affected_rows;
                $this->CloseConnectionDB();
                deleteUser->close();

                if($result == 1){
                    //se la query ha eliminato una riga, allora l'utente è stato eliminato con successo
                    $logOut = DB::LogoutUser();//chiama la funzione di logout per rimuovere la sessione dell'utente
                    if(is_string($logOut)){
                        //se c'è un errore nel logout, ritorna l'errore
                        return "Error during logout";
                    }
                    return true; //eliminazione avvenuta con successo
                }else{
                    //se la query non ha eliminato nessuna riga, allora l'utente non esiste o c'è stato un errore
                    return false; //nessuna riga eliminata, errore nell'eliminazione dell'utente
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    //OTTENERE INFO PRODOTTO E DEGUSTAZIONE
    
    public function GetProductInfo($product): array | string{}//ottenere informazioni su un prodotto

    public function GetTastingInfo($tasting): array | string{}//ottenere informazioni su una degustazione
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
