<?php
/*
    * Classe per la gestione del database
    * Contiene le funzioni per la registrazione, login, logout, eliminazione utente, ottenimento informazioni utente, prodotti e degustazioni
    * 
    * Alcuni controlli ulteriori potrebbero essere controllo maggiore su degustazioni(es data fine data inizio e data scelta dall'utente), vedere se un prodotto è disponibile, ecc...
    * @version 1.0
*/

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

        mysqli_report(MYSQLI_REPORT_STRICT);//abilita il report degli errori per mysqli
        try{
            //connessione al database
            $this->connection = mysqli_connect(self::DB_NAME, self::USERNAME, self::PASSWORD, self::HOST);

        } catch(\mysqli_sql_exception $error){//forse e al posto di error
            //se c'è un errore nella connessione, ritorna false
            return false;
        }
        //se la connessione è avvenuta con successo, ritorna true
        return true;

    }

    private function CloseConnectionDB(): void{//chiude la connessione al database

        mysqli_close($this->connection);//

    }

    public function IsUserLog(): bool | string{//controlla se l'utente è loggato

        if(isset($_SESSION["logged_user"]) && $_SESSION != null) return $_SESSION["logged_user"];
        else return false;

    }

    private function UserUsername(): bool | string{//controlla se l'utente è loggato e ritorna il suo username

        if(isset($_SESSION["logged_username"]) && $_SESSION != null) return $_SESSION["logged_username"];
        else return false;

    }
    
    //string per gli errori
    //AZIONI GENERALI

    public function RegisterNewUser($username, $name, $surname, $date, $email, $password): bool | string{//registrazione nuovo utente (da aggiungere nel corpo della funzione la data d'iscrizione)

        $encriptedPassword = hash('sha256', $password);//crittografia della password
        $subscribe_date=date("Y-m-d H:i:s");
        $newConnection = $this->OpenConnectionDB();

        if($newConnection){
            $userInfo = $this->connection->prepare("INSERT INTO utenti(email, username, password, nome, cognome, data_di_nascita, data_iscrizione) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $userInfo->bind_param("sssssss", $email, $username, $encriptedPassword, $name, $surname, $date, $subscribe_date);

            try{
                $userInfo->execute();
            }catch(\mysqli_sql_exception $error){

                $this->CloseConnectionDB();
                $userInfo->close();
                return false; //errore nell'esecuzione della query

            }

            //!!! CONTROLLARE
            if(mysqli_affected_rows($this->connection) == 1){
                //se la query ha inserito una riga, allora l'utente è stato registrato con successo
                $this->CloseConnectionDB();
                $userInfo->close();
                $id = $this->connection->insert_id; //ottiene l'id dell'utente appena registrato(insert_id è una proprietà di mysqli che restituisce l'id dell'ultima riga inserita)
                $_SESSION["logged_user"] = $id; //impostazione della sessione per l'utente loggato, SERVE QUERY PER OTTENERE L'ID
                return true; //registrazione avvenuta con successo

            } else {
                
                $this->CloseConnectionDB();
                $userInfo->close();
                return false; //nessuna riga inserita, errore nella registrazione

            }
        }else {

            return "Connection error"; //errore nella connessione al database

        }
    }

    public function LoginUser($username, $password): bool | string{//login utente

        if($this->IsUserLog() == false) {
            //se l'utente non è loggato, procedi con il login
            $encriptedPassword = hash('sha256', $password);
            $newConnection = $this->OpenConnectionDB();

            if($newConnection){
                //preparazione della query per verificare l'esistenza dell'utente
                $isUserExist = $this->connection->prepare("SELECT id,username FROM utenti WHERE username = ? AND password = ?");
                $isUserExist->bind_param("ss", $username, $encriptedPassword);
                try{
                    $isUserExist->execute();
                }catch(\mysqli_sql_exception $error){

                    $this->CloseConnectionDB();
                    $isUserExist->close();
                    return false; //errore nell'esecuzione della query
                }

                $info = $isUserExist->get_result();
                $this->CloseConnectionDB();
                $isUserExist->close();

                //Il server tecweb non controlla il case-sensitive per il database, quindi controllo che il risultato trovato corrisponda esattamente allo username inserito
                if ($info->num_rows==1 && strcmp(mysqli_fetch_assoc($info)["username"],$username)==0) {
                    //se l'utente esiste, procedi con il login
                    $id = $info->fetch_assoc()["id"];//ottiene l'id dell'utente
                    $username = $info->fetch_assoc()["username"];//ottiene lo username dell'utente
                    $_SESSION["logged_user"] = $id;//METTERE QUERY
                    $_SESSION["logged_username"] = $username;//impostazione della sessione per l'utente loggato
                    $info->free();
                    return true;

                } else {
                    //se l'utente non esiste, ritorna false
                    $info->free();
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

        if($isUserLogged == false){
            //se l'utente è loggato, procedi con il logout
            unset($_SESSION["logged_user"]); //rimuove la sessione dell'utente loggato
            return true; //logout avvenuto con successo

        }else{
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato

        }

    }

    public function DeleteUser(): bool | string{//eliminazione utente
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per eliminare l'utente
                $deleteUser = $this->connection->prepare("DELETE FROM utenti WHERE id = ?");
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
                $deleteUser->close();

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

    function GetUserInfo(): array | string{//ottenere informazioni su un utente
        $id = $this->IsUserLog(); 
        if($id == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User Is not logged"; //utente non loggato
        }
        $UserExist = $this->ThisIsUserExists($id);
        if(!$UserExist){
            //se l'utente non esiste, ritorna un messaggio di errore
            return "User not found"; //utente non trovato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per ottenere le informazioni su un utente
                $userInfo = $this->connection->prepare("SELECT username, nome, cognome, data_iscrizione, url_immagine FROM utenti WHERE id = ?");
                $userInfo->bind_param("i", $id);
                try{
                    //esecuzione della query per ottenere le informazioni su un utente
                    $userInfo->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $userInfo->close();
                    return "Execution error"; //errore nell'esecuzione della query
                }
                //ottiene il risultato della query
                $result = $userInfo->get_result();
                $this->CloseConnectionDB();
                $userInfo->close();
                if($result->num_rows == 1){
                    //se l'utente esiste, ritorna le informazioni dell'utente
                    $info = mysqli_fetch_assoc($result);
                    $result->free();
                    return $info; //ritorna le informazioni dell'utente come array associativo
                }else{
                    //se l'utente non esiste, ritorna un messaggio di errore
                    $result->free();
                    return "User not found"; //utente non trovato
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    function ThisIsUserExists($id): bool | string{//verifica se un utente esiste
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per verificare l'esistenza di un utente
            $userExists = $this->connection->prepare("SELECT id FROM utenti WHERE id = ?");
            $userExists->bind_param("i", $id);
            try{
                //esecuzione della query per verificare l'esistenza di un utente
                $userExists->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $userExists->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $userExists->get_result();
            $this->CloseConnectionDB();
            $userExists->close();
            if($result->num_rows == 1){
                //se l'utente esiste, ritorna true
                $result->free();
                return true; //utente esistente
            }else{
                //se l'utente non esiste, ritorna false
                $result->free();
                return false; //utente non trovato
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

        public function ThisUsernameExists($username, $checkCaseSensitive = true): bool | string {//controlla se un username esiste nel database
        $newConnection = $this->OpenConnectionDB();
        if ($newConnection) {
            $checkUsername=$this->connection->prepare("SELECT username FROM utenti WHERE username = ?");
            $checkUsername->bind_param("s",$username);
            try {
                $checkUsername->execute();
            } catch (\mysqli_sql_exception $e) {
                $this->CloseConnectionDB();
                $checkUsername->close();
                return "Execution error"; //errore nell'esecuzione della query
            }

            $result=$checkUsername->get_result();
            $this->CloseConnectionDB();
            $checkUsername->close();

            if ($result->num_rows==1) {
                if($checkCaseSensitive) {
                    if(strcmp(mysqli_fetch_assoc($result)["username"],$username)==0) {//se l'username esiste e corrisponde esattamente a quello inserito
                        $result->free();
                        return true;
                    } else {
                        $result->free();
                        return false;
                    }
                } else {
                    $result->free();
                    return true;
                }
            } else {//se l'username non esiste
                $result->free();
                return false;
            }
        } else {
            return "Connection error"; //errore nella connessione al database
        }
    }

    public function ThisEmailExist($email){
        $newConnection = $this->OpenConnectionDB();
        if ($newConnection) {
            $checkEmail = $this->connection->prepare("SELECT email FROM utenti WHERE email = ?");
            $checkEmail->bind_param("s", $email);
            try {
                $checkEmail->execute();
            } catch (\mysqli_sql_exception $e) {
                $this->CloseConnectionDB();
                $checkEmail->close();
                return "Execution error"; //errore nell'esecuzione della query
            }

            $result = $checkEmail->get_result();
            $this->CloseConnectionDB();
            $checkEmail->close();

            if ($result->num_rows == 1) {
                if (strcmp(mysqli_fetch_assoc($result)["email"],$email)==0) {//se l'email esiste e corrisponde esattamente a quella inserita
                    $result->free();
                    return true;
                } else {
                    $result->free();
                    return false;
                }
            } else {//se l'email non esiste
                $result->free();
                return false;
            }
        } else {
            return "Connection error"; //errore nella connessione al database
        }
    }//Controlla se un'email esiste nel database

    //OTTENERE INFO PRODOTTO E DEGUSTAZIONE
    
    public function GetProductInfo($product): array | string{//ottenere informazioni su un prodotto
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per ottenere le informazioni su un prodotto
            $productInfo = $this->connection->prepare("SELECT * FROM prodotti WHERE nome = ?");
            $productInfo->bind_param("s", $product);
            try{
                //esecuzione della query per ottenere le informazioni su un prodotto
                $productInfo->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $productInfo->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $productInfo->get_result();
            $this->CloseConnectionDB();
            $productInfo->close();
            if($result->num_rows == 1){
                //se il prodotto esiste, ritorna le informazioni del prodotto
                $info = mysqli_fetch_assoc($result);
                $result->free();
                return $info; //ritorna le informazioni del prodotto come array associativo
            }else{
                //se il prodotto non esiste, ritorna un messaggio di errore
                return "Product not found"; //prodotto non trovato
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }
    
    public function GetProductIngredients($product): array | string{//ottenere ingredienti di un prodotto
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            $ingredients = $this->connection->prepare("SELECT * FROM prodotto_ingredienti WHERE prodotto = ?");
            $ingredients->bind_param("s", $product);
            try{
                //esecuzione della query per ottenere gli ingredienti di un prodotto
                $ingredients->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $ingredients->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $ingredients->get_result();
            $this->CloseConnectionDB();
            $ingredients->close();
            if($result->num_rows > 0){
                //se ci sono ingredienti, li aggiunge all'array
                $ingredientsArray = array();
                while($row = mysqli_fetch_assoc($result)){
                    array_push($ingredientsArray, $row);
                }
                //libera la memoria occupata dal risultato della query
                $result->free();
                return $ingredientsArray; //ritorna l'array degli ingredienti del prodotto
            }else{
                //se non ci sono ingredienti, ritorna un messaggio di errore
                return "No ingredients found for this product"; //nessun ingrediente trovato per questo prodotto
            }

        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }
    
    public function IsProductVeganVegetarianCeliac($product): bool | string{//verifica se un prodotto è senza glutine, vegano e/o vegetariano
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per verificare se un prodotto è vegano, vegetariano o senza glutine
            $productType = $this->connection->prepare("SELECT MIN(ingredienti.isVegano) AS vegano, MIN(ingredienti.isVegetariano) AS vegetariano, MIN(ingredienti.isCeliaco) AS celiaco 
            FROM prodotto_ingredienti 
            JOIN ingredienti ON ingredienti.nome = prodotto_ingredienti.ingrediente
            WHERE prodotto_ingredienti.prodotto = ?");
            $productType->bind_param("s", $product);
            try{
                //esecuzione della query per verificare se un prodotto è vegano, vegetariano o senza glutine
                $productType->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $productType->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $productType->get_result();
            $this->CloseConnectionDB();
            $productType->close();
            if($result->num_rows == 1){
                //se il prodotto esiste, ritorna le informazioni del prodotto
                $info = mysqli_fetch_assoc($result);
                $result->free();
                return $info; //ritorna le informazioni del prodotto come array associativo
            }else{
                //se il prodotto non esiste, ritorna un messaggio di errore
                return "Product not found"; //prodotto non trovato
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
        
    }

    public function GetTastingInfo($tasting): array | string{//ottenere informazioni su una degustazione
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per ottenere le informazioni su una degustazione
            $tastingInfo = $this->connection->prepare("SELECT nome_prodotto, descrizione, disponibilita_persone, data_inizio, data_fine, prezzo FROM degustazioni WHERE id_degustazione = ?");
            $tastingInfo->bind_param("i", $tasting);
            try{
                //esecuzione della query per ottenere le informazioni su una degustazione
                $tastingInfo->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $tastingInfo->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $tastingInfo->get_result();
            $this->CloseConnectionDB();
            $tastingInfo->close();
            if($result->num_rows == 1){
                //se la degustazione esiste, ritorna le informazioni della degustazione
                $info = mysqli_fetch_assoc($result);
                $result->free();
                return $info; //ritorna le informazioni della degustazione come array associativo
            }else{
                //se la degustazione non esiste, ritorna un messaggio di errore
                return "Tasting not found"; //degustazione non trovata
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    } 

    //OTTENERE INFO UTENTE

    public function GetUserFavoritesProducts($id,int $quantity = -1): array | string{//ottenere prodotti preferiti
        $newConnection = $this->OpenConnectionDB();
        $favorites = array();
        if($newConnection){
            //preparazione della query per ottenere i prodotti preferiti dell'utente
            $userFavorites = $this->connection->prepare( "SELECT prodotti.nome, prodotti.categoria, prodotto.prezzo, prodotti.url_immagine FROM prodotti, preferiti WHERE preferiti.nome_prodotto = prodotti.nome and preferiti.id_utente = ?");
            $userFavorites->bind_param("i", $id);
            try{
                //esecuzione della query per ottenere i prodotti preferiti dell'utente
                $userFavorites->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $userFavorites->close();
                return false;
            }
            //ottiene il risultato della query
            $result = $userFavorites->get_result();
            $this->CloseConnectionDB();
            $userFavorites->close();
            if($result->num_rows > 0){
                if($quantity == -1){
                    //se la quantità è -1, allora ritorna tutti i prodotti preferiti
                    //se ci sono prodotti preferiti, li aggiunge all'array
                    while($row = mysqli_fetch_assoc($result)){
                        array_push($favorites, $row);
                    }
                }else{
                    //se la quantità è diversa da -1, allora ritorna solo i primi $quantity prodotti preferiti
                    while($quantity > 0 && $row = mysqli_fetch_assoc($result)){
                        array_push($favorites, $row);
                        $quantity--; //decrementa la quantità di prodotti preferiti da aggiungere
                    }
                }
                //libera la memoria occupata dal risultato della query
                $result->free();
                return $favorites; //ritorna l'array dei prodotti preferiti
            }else{
                //se non ci sono prodotti preferiti
                return "No favorite products found"; //nessun prodotto preferito trovato
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    public function GetUserReservation($id): array | string{//ottenere prenotazione prodotti
        $newConnection = $this->OpenConnectionDB();
        $reservations = array();
        if($newConnection){
            //preparazione della query per ottenere le prenotazioni dei prodotti dell'utente
            $userReservations = $this->connection->prepare("SELECT prodotti.nome, prodotti.categoria, prenotazioni.data_prenotazione, prenotazioni.quantita 
            FROM prenotazioni 
            JOIN prodotti ON prenotazioni.id_prodotto = prodotti.id
            WHERE prenotazioni.id_utente = ?");
            $userReservations->bind_param("i", $id);
            try{
                //esecuzione della query per ottenere le prenotazioni dei prodotti dell'utente
                $userReservations->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $userReservations->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $userReservations->get_result();
            $this->CloseConnectionDB();
            $userReservations->close();
            if($result->num_rows > 0){
                //se ci sono prenotazioni, le aggiunge all'array
                while($row = mysqli_fetch_assoc($result)){
                    array_push($reservations, $row);
                }
                //libera la memoria occupata dal risultato della query
                $result->free();
                return $reservations; //ritorna l'array delle prenotazioni dei prodotti dell'utente
            }else{
                //se non ci sono prenotazioni
                $result->free();
                return "No reservations found"; //nessuna prenotazione trovata
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }

    }

    public function GetUserTastings($id): array | string{//ottenere prenotazione degustazioni
        $newConnection = $this->OpenConnectionDB();
        $tastings = array();
        if($newConnection){
            //preparazione della query per ottenere le degustazioni prenotate dall'utente
            $userTastings = $this->connection->prepare("SELECT degustazioni.nome_prodotto, degustazioni.data_inizio, degustazioni.data_fine, degustazioni.prezzo 
            FROM degustazioni 
            JOIN utenti ON degustazioni.id_utente = utenti.id
            WHERE utenti.id = ?");
            $userTastings->bind_param("i", $id);
            try{
                //esecuzione della query per ottenere le degustazioni prenotate dall'utente
                $userTastings->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $userTastings->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $userTastings->get_result();
            $this->CloseConnectionDB();
            $userTastings->close();
            if($result->num_rows > 0){
                //se ci sono degustazioni, le aggiunge all'array
                while($row = mysqli_fetch_assoc($result)){
                    array_push($tastings, $row);
                }
                //libera la memoria occupata dal risultato della query
                $result->free();
                return $tastings; //ritorna l'array delle degustazioni prenotate dall'utente
            }else{
                //se non ci sono degustazioni prenotate dall'utente
                $result->free();
                return "No tastings found"; //nessuna degustazione trovata
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }
    
    public function GetUserReviewProduct($id, $product): array | string{//ottenere recensione scritta su un prodotto singolo
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            $userComments = $this->connection->prepare("SELECT nome_prodotto, voto, commento FROM valutazioni WHERE id_utente = ? AND nome_prodotto = ?");
            $userComments->bind_param("is", $id, $product);
            try{
                //esecuzione della query per ottenere le recensioni scritte dall'utente su un prodotto singolo
                $userComments->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $userComments->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $userComments->get_result();
            $this->CloseConnectionDB();
            $userComments->close();
            if($result->num_rows == 1){
                //se l'utente ha scritto una recensione su un prodotto singolo, la aggiunge all'array
                $review = mysqli_fetch_assoc($result);
                $result->free();
                return $review; //ritorna la recensione scritta dall'utente su un prodotto singolo
            } else {
                //se non ci sono recensioni scritte dall'utente su un prodotto singolo
                $result->free();
                return "No reviews found for this product"; //nessuna recensione trovata per questo prodotto
            }

        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    //FUNZIONI CHE NON SERVONO MA POSSONO SERVIRE IN FUTURO
    //public function GetUserAdvices($id): array | string{}//ottenere suggerimenti scritti dall'utente(se serve)
    //public function GetUserReviews($id): array | string{}//ottenere tutte le recensioni scritte dall'utente(vediamo se servirà)

    //ELIMINAZIONI DA PARTE DELL'UTENTE

    public function DeleteAllFavoritesProducts(): bool | string {//cancellare tutti i prodotti preferiti
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                $deleteFavorites = $this->connection->prepare("DELETE FROM preferiti WHERE id_utente = ?");
                $deleteFavorites->bind_param("i", $isUserLogged);
                try{
                    //esecuzione della query per cancellare tutti i prodotti preferiti dell'utente
                    $deleteFavorites->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteFavorites->close();
                    return false; //errore nell'esecuzione della query
                }
                $this->CloseConnectionDB();
                $deleteFavorites->close();
                return true; //cancellazione avvenuta con successo(volendo si può controllare se mysqli_affected_rows(this->connection) == 0 per vedere se non c'erano prodotti preferiti da cancellare)

            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteOneFavoriteProduct($product): bool | string{//cancellare un singolo prodotto preferito --> da vedere cos'è product
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                $deleteFavorite = $this->connection->prepare("DELETE FROM preferiti WHERE id_utente = ? AND nome_prodotto = ?"); 
                $deleteFavorite->bind_param("is", $isUserLogged, $product);
                try{
                    //esecuzione della query per cancellare un singolo prodotto preferito dell'utente
                    $deleteFavorite->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteFavorite->close();
                    return false; //errore nell'esecuzione della query
                }
                $result = $deleteFavorite->affected_rows;
                $this->CloseConnectionDB();
                $deleteFavorite->close();
                if($result == 1){
                    //se la query ha cancellato una riga, allora il prodotto preferito è stato cancellato con successo
                    return true; //cancellazione avvenuta con successo
                }else{
                    //se la query non ha cancellato nessuna riga, allora il prodotto preferito non esiste o c'è stato un errore
                    return "Product not found or already deleted"; //nessuna riga cancellata, errore nella cancellazione del prodotto preferito
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteAllReservations(): bool | string {//cancellare tutte le prenotazioni dei prodotti
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cancellare tutte le prenotazioni dei prodotti dell'utente
                $deleteReservations = $this->connection->prepare("DELETE FROM prenotazioni WHERE id_utente = ?");
                $deleteReservations->bind_param("i", $isUserLogged);
                try{
                    //esecuzione della query per cancellare tutte le prenotazioni dei prodotti dell'utente
                    $deleteReservations->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteReservations->close();
                    return false; //errore nell'esecuzione della query
                }
                $this->CloseConnectionDB();
                $deleteReservations->close();
                return true; //cancellazione avvenuta con successo (volendo si può controllare se mysqli_affected_rows(this->connection) == 0 per vedere se non c'erano prenotazioni da cancellare) 
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteOneReservation($reservation): bool | string{//cancellare una singola prenotazione di un prodotto --> da vedere cos'è reservation o se si può ottenere l'id della prenotazione
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cancellare una singola prenotazione di un prodotto dell'utente
                $deleteReservation = $this->connection->prepare("DELETE FROM prenotazioni WHERE id_utente = ? AND id_prodotto = ?");
                $deleteReservation->bind_param("ii", $isUserLogged, $reservation);
                try{
                    //esecuzione della query per cancellare una singola prenotazione di un prodotto dell'utente
                    $deleteReservation->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteReservation->close();
                    return false; //errore nell'esecuzione della query
                }
                $result = $deleteReservation->affected_rows;
                $this->CloseConnectionDB();
                $deleteReservation->close();
                if($result == 1){
                    //se la query ha cancellato una riga, allora la prenotazione del prodotto è stata cancellata con successo
                    return true; //cancellazione avvenuta con successo
                }else{
                    //se la query non ha cancellato nessuna riga, allora la prenotazione del prodotto non esiste o c'è stato un errore
                    return "Reservation not found or already deleted"; //nessuna riga cancellata, errore nella cancellazione della prenotazione del prodotto
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteAllTastings(): bool | string {//cancellare tutte le degustazioni prenotate
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cancellare tutte le degustazioni prenotate dell'utente
                $deleteTastings = $this->connection->prepare("DELETE FROM degustazioni WHERE id_utente = ?");
                $deleteTastings->bind_param("i", $isUserLogged);
                try{
                    //esecuzione della query per cancellare tutte le degustazioni prenotate dell'utente
                    $deleteTastings->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteTastings->close();
                    return false; //errore nell'esecuzione della query
                }
                $this->CloseConnectionDB();
                $deleteTastings->close();
                return true; //cancellazione avvenuta con successo (volendo si può controllare se mysqli_affected_rows(this->connection) == 0 per vedere se non c'erano degustazioni da cancellare)
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteOneTasting($tasting): bool | string{//cancellare una singola degustazione --> da vedere cos'è tasting
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cancellare una singola degustazione dell'utente
                $deleteTasting = $this->connection->prepare("DELETE FROM degustazioni WHERE id_utente = ? AND id_degustazione = ?");
                $deleteTasting->bind_param("ii", $isUserLogged, $tasting);
                try{
                    //esecuzione della query per cancellare una singola degustazione dell'utente
                    $deleteTasting->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteTasting->close();
                    return false; //errore nell'esecuzione della query
                }
                $result = $deleteTasting->affected_rows;
                $this->CloseConnectionDB();
                $deleteTasting->close();
                if($result == 1){
                    //se la query ha cancellato una riga, allora la degustazione è stata cancellata con successo
                    return true; //cancellazione avvenuta con successo
                }else{
                    //se la query non ha cancellato nessuna riga, allora la degustazione non esiste o c'è stato un errore
                    return "Tasting not found or already deleted"; //nessuna riga cancellata, errore nella cancellazione della degustazione
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteAllReviews(): bool | string {//cancellare tutte le recensioni
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cancellare tutte le recensioni dell'utente
                $deleteReviews = $this->connection->prepare("DELETE FROM valutazioni WHERE id_utente = ?");
                $deleteReviews->bind_param("i", $isUserLogged);
                try{
                    //esecuzione della query per cancellare tutte le recensioni dell'utente
                    $deleteReviews->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteReviews->close();
                    return false; //errore nell'esecuzione della query
                }
                $this->CloseConnectionDB();
                $deleteReviews->close();
                return true; //cancellazione avvenuta con successo (volendo si può controllare se mysqli_affected_rows(this->connection) == 0 per vedere se non c'erano recensioni da cancellare)
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function DeleteOneReview($product): bool | string{//cancellare una singola recensione  --> da vedere cos'è product
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cancellare una singola recensione dell'utente
                $deleteReview = $this->connection->prepare("DELETE FROM valutazioni WHERE id_utente = ? AND nome_prodotto = ?");
                $deleteReview->bind_param("is", $isUserLogged, $product);
                try{
                    //esecuzione della query per cancellare una singola recensione dell'utente
                    $deleteReview->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $deleteReview->close();
                    return false; //errore nell'esecuzione della query
                }
                $result = $deleteReview->affected_rows;
                $this->CloseConnectionDB();
                $deleteReview->close();
                if($result == 1){
                    //se la query ha cancellato una riga, allora la recensione è stata cancellata con successo
                    return true; //cancellazione avvenuta con successo
                }else{
                    //se la query non ha cancellato nessuna riga, allora la recensione non esiste o c'è stato un errore
                    return "Review not found or already deleted"; //nessuna riga cancellata, errore nella cancellazione della recensione
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    //AGGIUNTE DA PARTE DELL'UTENTE
    //Al posto di username -> logged_user da isUserLog() (da scegliere)

    public function AddAdvice($advice, $id =null): bool | string {//Aggiunta da parte dell'utente di un suggerimento
        $date = date("Y-m-d H:i:s"); //ottiene la data e l'ora attuale
        $newConnection = $this->OpenConnectionDB();
        $isUserLogged = $this->IsUserLog(); //!!! CONTROLLARE

        if($newConnection){
            //preparazione della query per aggiungere un suggerimento
            $addAdvice = $this->connection->prepare("INSERT INTO suggerimenti (id_utente, data, suggerimento) VALUES (?, ?, ?)");
            $addAdvice->bind_param("iss", $isUserLogged, $date, $advice);

            try{
                //esecuzione della query per aggiungere un suggerimento
                $addAdvice->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $addAdvice->close();
                return false; //errore nell'esecuzione della query
            }

            if(mysqli_affected_rows($this->connection) == 1){
                //se la query ha inserito una riga, allora il suggerimento è stato aggiunto con successo
                $this->CloseConnectionDB();
                $addAdvice->close();
                return true; //aggiunta avvenuta con successo
            }else{
                //se la query non ha inserito nessuna riga, allora c'è stato un errore
                $this->CloseConnectionDB();
                $addAdvice->close();
                return false; //nessuna riga inserita, errore nell'aggiunta del suggerimento
            } 
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    //!!! CONTROLLARE
    public function AddReview($id, $comment, $grade, $product): bool | string{//Aggiunta da parte dell'utente di un commento e valutazione di un prodotto(vedere se usare string o bool e basta per gli errori)
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            $date = date("Y-m-d H:i:s"); //ottiene la data e l'ora attuale
            if($newConnection){
                $reviewExists = $this->GetUserReviewProduct($isUserLogged, $product);
                if(is_array($reviewExists)){
                    //se l'utente ha già scritto una recensione su questo prodotto, ritorna un messaggio di errore
                    return "User has already reviewed this product"; //l'utente ha già recensito questo prodotto
                }else if($reviewExists == "No reviews found for this product"){
                    //se l'utente non ha ancora scritto una recensione su questo prodotto, allora può procedere con l'aggiunta della recensione
                    //preparazione della query per aggiungere una recensione
                    $addReview = $this->connection->prepare("INSERT INTO valutazioni (id_utente, nome_prodotto, data, voto, commento) VALUES (?, ?, ?, ?, ?)");
                    $addReview->bind_param("issis", $isUserLogged, $product, $date, $grade, $comment);
                    try{
                        //esecuzione della query per aggiungere una recensione
                        $addReview->execute();
                    }catch(\mysqli_sql_exception $error){
                        //se c'è un errore nell'esecuzione della query, ritorna false
                        $this->CloseConnectionDB();
                        $addReview->close();  
                        return false; //errore nell'esecuzione della query
                    }
                    if(mysqli_affected_rows($this->connection) == 1){
                        //se la query ha inserito una riga, allora la recensione è stata aggiunta con successo
                        $this->CloseConnectionDB();
                        $addReview->close();
                        return true; //aggiunta avvenuta con successo
                    }else{
                        //se la query non ha inserito nessuna riga, allora c'è stato un errore
                        $this->CloseConnectionDB();
                        $addReview->close();
                        return "Review addition failed"; //nessuna riga inserita, errore nell'aggiunta della recensione
                    }
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
            return "Connection error"; //!!! CONTROLLARE
        }
    }

    public function AddFavoriteProduct($id,$product): bool | string{//Aggiunta di un prodotto preferito da parte dell'utente
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per aggiungere un prodotto preferito
                $addFavorite = $this->connection->prepare("INSERT INTO preferiti (id_utente, nome_prodotto) VALUES (?, ?)");
                $addFavorite->bind_param("is", $isUserLogged, $product);
                try{
                    //esecuzione della query per aggiungere un prodotto preferito
                    $addFavorite->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $addFavorite->close();
                    return false; //errore nell'esecuzione della query
                }
                if(mysqli_affected_rows($this->connection) == 1){
                    //se la query ha inserito una riga, allora il prodotto preferito è stato aggiunto con successo
                    $this->CloseConnectionDB();
                    $addFavorite->close();
                    return true; //aggiunta avvenuta con successo
                }else{
                    //se la query non ha inserito nessuna riga, allora il prodotto preferito esiste già o c'è stato un errore
                    $this->CloseConnectionDB();
                    $addFavorite->close();
                    return "Product already exists in favorites"; //prodotto già presente nei preferiti
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function AddReservation($id, $product, $quantity, $date): bool | string{//Aggiunta da parte dell'utente di una prenotazione rispetto ad un prodotto indicando quantità dello stesso e quando (data) ritirarlo
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per aggiungere una prenotazione
                $addReservation = $this->connection->prepare("INSERT INTO prenotazioni (id_utente, id_prodotto, quantita, data_ritiro) VALUES (?, ?, ?, ?)");
                $addReservation->bind_param("iiis", $isUserLogged, $product, $quantity, $date);
                try{
                    //esecuzione della query per aggiungere una prenotazione
                    $addReservation->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $addReservation->close();
                    return false; //errore nell'esecuzione della query
                }
                if(mysqli_affected_rows($this->connection) == 1){
                    //se la query ha inserito una riga, allora la prenotazione è stata aggiunta con successo
                    $this->CloseConnectionDB();
                    $addReservation->close();
                    return true; //aggiunta avvenuta con successo
                }else{
                    //se la query non ha inserito nessuna riga, allora c'è stato un errore
                    $this->CloseConnectionDB();
                    $addReservation->close();
                    return "Reservation addition failed"; //nessuna riga inserita, errore nell'aggiunta della prenotazione
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    //DA CONTROLLARE LE PROSSIME DUE FUNZIONI :AddTasting e CheckTastingAvailability -> MOLTO IMPORTANTE

    public function AddTasting($id, $tasting, $people_number, $date): bool | string{//Aggiunta di una prenotazione per una degustazione da parte dell'utente per un tot di persone e in una certa data
        $isUserLogged = $this->IsUserLog();
        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            $date2 = date("Y-m-d H:i:s"); //ottiene la data e l'ora attuale
            if($newConnection){
                //preparazione della query per aggiungere una prenotazione per una degustazione
                if(!$this->CheckTastingAvailability($tasting, $people_number)){
                    //se la degustazione non è disponibile per il numero di persone richiesto, ritorna un messaggio di errore
                    return "Tasting not available for the requested number of people"; //degustazione non disponibile per il numero di persone richiesto
                }
                //preparazione della query per aggiungere una prenotazione per una degustazione
                //inserisce una prenotazione per una degustazione con l'id della degustazione, l'id dell'utente, la data della prenotazione e la data della scelta
                $addTasting = $this->connection->prepare("INSERT INTO prenotazioni_degustazioni (id_degustazione, id_cliente, data_prenotazione, data_scelta) VALUES (?, ?, ?, ?)");
                $addTasting->bind_param("iiss", $tasting, $isUserLogged, $date2, $date);

                //preparazione della query per aggiornare la disponibilità delle persone per la degustazione
                //decrementa la disponibilità delle persone per la degustazione di un certo numero di persone
                $changePeopleNumber = $this->connection->prepare("UPDATE degustazioni SET disponibilita_persone = disponibilita_persone - ? WHERE id_degustazione = ?");
                $changePeopleNumber->bind_param("ii", $people_number, $tasting);
                
                try{
                    //esecuzione della query per aggiungere una prenotazione per una degustazione
                    $addTasting->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $addTasting->close();
                    return false; //errore nell'esecuzione della query
                }

                try{
                    //esecuzione della query per aggiornare la disponibilità delle persone per la degustazione
                    $changePeopleNumber->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $changePeopleNumber->close();
                    return false; //errore nell'esecuzione della query
                }

                if(mysqli_affected_rows($this->connection) == 1){
                    //se la query ha inserito una riga, allora la prenotazione per la degustazione è stata aggiunta con successo
                    $this->CloseConnectionDB();
                    $addTasting->close();
                    return true; //aggiunta avvenuta con successo
                }else{
                    //se la query non ha inserito nessuna riga, allora c'è stato un errore
                    $this->CloseConnectionDB();
                    $addTasting->close();
                    return "Tasting addition failed"; //nessuna riga inserita, errore nell'aggiunta della prenotazione per la degustazione
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function CheckTastingAvailability($tasting, $people_number): bool | string{//Controlla se una degustazione è disponibile per un certo numero di persone
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per controllare la disponibilità di una degustazione
            $checkAvailability = $this->connection->prepare("SELECT disponibilita_persone FROM degustazioni WHERE id_degustazione = ?");
            $checkAvailability->bind_param("i", $tasting);
            try{
                //esecuzione della query per controllare la disponibilità di una degustazione
                $checkAvailability->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $checkAvailability->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $checkAvailability->get_result();
            $this->CloseConnectionDB();
            $checkAvailability->close();
            if($result->num_rows == 1){
                //se la degustazione esiste, allora controlla la disponibilità
                $row = mysqli_fetch_assoc($result);
                if($row['disponibilita_persone'] >= $people_number){
                    return true; //degustazione disponibile per il numero di persone richiesto
                }else{
                    return "Tasting not available for the requested number of people"; //degustazione non disponibile per il numero di persone richiesto
                }
            }else{
                return "Tasting not found"; //degustazione non trovata
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    //CAMBIO DELLE IMPOSTAZIONI

    public function ChangePassword($oldPassword, $newPassword): bool | string{//Cambio password da parte dell'utente
        $encriptedOldPassword = hash('sha256', $oldPassword);
        $encriptedNewPassword = hash('sha256', $newPassword);
        $isUserLogged = $this->IsUserLog();

        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cambiare la password dell'utente
                $changePassword = $this->connection->prepare("UPDATE utenti SET password = ? WHERE id = ? AND password = ?");
                $changePassword->bind_param("sis", $encriptedNewPassword, $isUserLogged, $encriptedOldPassword);

                try{
                    //esecuzione della query per cambiare la password dell'utente
                    $changePassword->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $changePassword->close();
                    return false; //errore nell'esecuzione della query
                }

                if(mysqli_affected_rows($this->connection) == 1){
                    //se la query ha cambiato una riga, allora la password è stata cambiata con successo
                    $this->CloseConnectionDB();
                    $changePassword->close();
                    return true; //cambio password avvenuto con successo
                }else{
                    //se la query non ha cambiato nessuna riga, allora l'utente non esiste o c'è stato un errore
                    $this->CloseConnectionDB();
                    $changePassword->close();
                    return "Password change failed"; //nessuna riga cambiata, errore nel cambio della password
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    public function ChangeMainInfo($username, $name, $cognome, $date, $logo): bool | string{//Cambio informazioni personali da parte dell'utente
        $isUserLogged = $this->IsUserLog();

        if($isUserLogged == false){
            //se l'utente non è loggato, ritorna un messaggio di errore
            return "User is not logged in"; //l'utente non è loggato
        }else{
            $newConnection = $this->OpenConnectionDB();
            if($newConnection){
                //preparazione della query per cambiare le informazioni personali dell'utente
                $changeInfo = $this->connection->prepare("UPDATE utenti SET username = ?, nome = ?, cognome = ?, data_di_nascita = ?, logo = ? WHERE id = ?");
                $changeInfo->bind_param("sssssi", $username, $name, $cognome, $date, $logo, $isUserLogged);

                try{
                    //esecuzione della query per cambiare le informazioni personali dell'utente
                    $changeInfo->execute();
                }catch(\mysqli_sql_exception $error){
                    //se c'è un errore nell'esecuzione della query, ritorna false
                    $this->CloseConnectionDB();
                    $changeInfo->close();
                    return false; //errore nell'esecuzione della query
                }

                if(mysqli_affected_rows($this->connection) == 1){
                    //se la query ha cambiato una riga, allora le informazioni sono state cambiate con successo
                    $this->CloseConnectionDB();
                    $changeInfo->close();
                    $_SESSION["logged_user_name"] = $username; //impostazione della sessione per l'utente loggato
                    return true; //cambio informazioni avvenuto con successo
                }else{
                    //se la query non ha cambiato nessuna riga, allora l'utente non esiste o c'è stato un errore
                    $this->CloseConnectionDB();
                    $changeInfo->close();
                    return "Information change failed"; //nessuna riga cambiata, errore nel cambio delle informazioni
                }
            }else{
                return "Connection error"; //errore nella connessione al database
            }
        }
    }

    //ALTRO
    public function AverageGradeProduct($product): int | string{//Restituisce il voto medio del prodotto
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per ottenere il voto medio del prodotto
            $averageGrade = $this->connection->prepare("SELECT CAST(AVG(voto) AS DECIMAL (2,0)) AS media FROM valutazioni WHERE nome_prodotto = ?");
            $averageGrade->bind_param("s", $product);
            try{
                //esecuzione della query per ottenere il voto medio del prodotto
                $averageGrade->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $averageGrade->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $averageGrade->get_result();
            $this->CloseConnectionDB();
            $averageGrade->close();
            if($result->num_rows == 1){
                //se il prodotto ha almeno una recensione, ritorna il voto medio
                $row = mysqli_fetch_assoc($result);
                return $row["media"]; //ritorna il voto medio come intero
            }else{
                //se il prodotto non ha recensioni, ritorna 0
                return "*"; //nessuna recensione trovata
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    public function ThisProductExists($product): bool | string{//Controlla se un prodotto esiste
        $newConnection = $this->OpenConnectionDB();
        $product = array();
        if($newConnection){
            $isProductExist = $this->connection->prepare("SELECT nome FROM prodotti WHERE nome = ?");
            $isProductExist->bind_param("s", $product);
            try{
                //esecuzione della query per verificare l'esistenza del prodotto
                $isProductExist->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $isProductExist->close();
                return false; //errore nell'esecuzione della query
            }
            $product = $isProductExist->get_result();
            $this->CloseConnectionDB();
            $isProductExist->close();
            if($product->num_rows == 1){
                //se il prodotto esiste, ritorna true
                $product->free();
                return true; //prodotto esistente
            }else{
                //se il prodotto non esiste, ritorna false
                $product->free();
                return false; //prodotto non esistente
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    public function ThisTastingExists($tasting): bool | string{//Controlla se una degustazione esiste
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            //preparazione della query per verificare l'esistenza della degustazione
            $isTastingExist = $this->connection->prepare("SELECT id_degustazione FROM degustazioni WHERE id_degustazione = ?");//vedere se usare id o nome per la degustazione(anche come primary key)
            $isTastingExist->bind_param("i", $tasting);
            try{
                //esecuzione della query per verificare l'esistenza della degustazione
                $isTastingExist->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $isTastingExist->close();
                return false; //errore nell'esecuzione della query
            }
            $result = $isTastingExist->get_result();
            $this->CloseConnectionDB();
            $isTastingExist->close();
            if($result->num_rows == 1){
                //se la degustazione esiste, ritorna true
                $result->free();
                return true; //degustazione esistente
            }else{
                //se la degustazione non esiste, ritorna false
                $result->free();
                return false; //degustazione non esistente
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    public function GetProductComments($product, int $check = -1): array | string{//Restituisce i commenti di un prodotto
        $newConnection = $this->OpenConnectionDB();
        $id = -1; //inizializza l'id a -1 per evitare errori se non viene trovato l'utente loggato
        if($this->IsUserLog() != false){
            $id = $this->IsUserLog(); //ottiene l'id dell'utente loggato
            $username = $this->UserUsername(); //ottiene lo username dell'utente loggato
        }
        if($newConnection){
            //preparazione della query per ottenere i commenti del prodotto
            $productComments = $this->connection->prepare("SELECT utenti.username, valutazioni.data, valutazioni.voto, valutazioni.commento FROM valutazioni JOIN utenti ON valutazioni.id_utente = utenti.id WHERE valutazioni.nome_prodotto = ? AND valutazioni.id_utente <>?");
            $productComments->bind_param("si", $product, $id);
            try{
                //esecuzione della query per ottenere i commenti del prodotto
                $productComments->execute();
            }catch(\mysqli_sql_exception $error){
                //se c'è un errore nell'esecuzione della query, ritorna false
                $this->CloseConnectionDB();
                $productComments->close();
                return false; //errore nell'esecuzione della query
            }
            //ottiene il risultato della query
            $result = $productComments->get_result();
            $this->CloseConnectionDB();
            $productComments->close();
            if($result->num_rows > 0){
                //se ci sono commenti, li aggiunge all'array
                if($check == -1){
                    //se il check è -1, allora ritorna tutti i commenti del prodotto
                    while($row = mysqli_fetch_assoc($result)){
                        array_push($comments, $row);
                    }
                }else{
                    while($check > 0 && $row = mysqli_fetch_assoc($result)){
                        //se il check è diverso da -1, allora ritorna solo i primi $check commenti del prodotto
                        array_push($comments, $row);
                        $check--; //decrementa il check per limitare il numero di commenti da aggiungere
                    }
                }
                //libera la memoria occupata dal risultato della query
                $result->free();
                return $comments; //ritorna l'array dei commenti del prodotto
            }else{
                //se non ci sono commenti
                return "No comments found"; //nessun commento trovato
            }
        }else{
            return "Connection error"; //errore nella connessione al database
        }
    }

    public function GetProducts($query, $params): array | null{
        $newConnection = $this->OpenConnectionDB();
        if($newConnection){
            try {
            $registerUserStatement=$this->connection->prepare($query);
            $registerUserStatement->execute($params);
            $result = $registerUserStatement->get_result();
            $this->CloseConnectionDB();
            $registerUserStatement->close();
            if ($result->num_rows > 0) {
                $list = array();
                while ($row = $result->fetch_assoc()) { // prende solo una riga
                    $list[] = $row;
                }
                $result->free_result();
                return $list;
            } else {
                $result->free_result();
                return null;
            } 
        } catch(\mysqli_sql_exception $e) {
            $this->CloseConnectionDB();
            $registerUserStatement->close();
            echo $e->getMessage();
            return null;
        }
        }else{
            header('Location: 500.php'); //errore nella connessione al database, reindirizza alla pagina di errore
            exit();

        }
    }
}
?>
