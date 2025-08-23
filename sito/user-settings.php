<?php

require_once "php/db.php";
require_once "php/sanitizer.php";

$pagina = file_get_contents("html/user-settings.html");
$username = "";
$userInfo = array();

$db = new DB;

$isLogged_first = $db->isUserLog();//controllo se l'utente è loggato
$isLogged = $db->UserUsername();//recupero l'username dell'utente loggato

if ((is_bool($isLogged_first) && $isLogged_first == false)) {//controllo se l'utente non è loggato(sia con id che con username)
    header('Location: login.php');
    exit();
}

$userBasePath="./images/";//percorso base per le immagini profilo degli utenti
$userPath=$isLogged."/";//percorso per le immagini profilo dell'utente loggato
$image="";
$userInfo = $db->GetUserInfo();//recupero le informazioni dell'utente loggato
$isProfileImageChanged = false;//variabile che indica se l'immagine del profilo è stata cambiata
$hasProfileImageBeenDeleted = false;//variabile che indica se l'immagine del profilo è stata eliminata

if (is_string($userInfo) && (strcmp($userInfo,"Execution error") == 0 || strcmp($userInfo,"Connection error") == 0)) {
    header('Location: 500.php');
    exit();
} elseif (is_string($userInfo) && (strcmp($userInfo,"User is not logged") == 0 || strcmp($userInfo,"User not found") == 0)) {
    header('Location: login.php');
    exit();
}

if(!isset($_POST["submit-user-settings"]) && !isset($_POST["submit-password-settings"])) {//controllo se l'utente non ha inviato i form(per info utente e password)
    $pagina = str_replace("[old-password-error]","",$pagina);
    $pagina = str_replace("[new-password-error]","",$pagina);
    $pagina = str_replace("[repeat-password-error]","",$pagina);
    $pagina = str_replace("[data-error]","",$pagina);
    $pagina = str_replace("[logo-error]","",$pagina);
    $pagina = str_replace("[username-error]","",$pagina);
    $pagina = str_replace("[name-error]","",$pagina);
    $pagina = str_replace("[surname-error]","",$pagina);

    if ($userInfo["url_immagine"]) {//controllo se l'utente ha un'immagine profilo
        $pagina = str_replace("[profile-pic]",$userInfo["url_immagine"],$pagina);
    } else {
        $pagina = str_replace("[profile-pic]","./img/default.webp",$pagina);//immagine di default
    }

    if ($userInfo["username"]) {//controllo se lo username è presente
        $pagina = str_replace("[username]",$userInfo["username"],$pagina);//[username] presente nell'input username viene sostituito con lo username dell'utente
    } else {
        $pagina = str_replace("[username]","",$pagina);
    }
    $pagina = str_replace("[username-error]","",$pagina);

    if ($userInfo["nome"]) {//controllo se il nome è presente
        $pagina = str_replace("[nome]",$userInfo["nome"],$pagina);//[nome] presente nell'input nome viene sostituito con il nome dell'utente
    } else {
        $pagina = str_replace("[nome]","",$pagina);
    }
    $pagina = str_replace("[name-error]","",$pagina);

    if ($userInfo["cognome"]) {//controllo se il cognome è presente
        $pagina = str_replace("[cognome]",$userInfo["cognome"],$pagina);//[nome] presente nell'input nome viene sostituito con il nome dell'utente
    } else {
        $pagina = str_replace("[cognome]","",$pagina);
    }
    $pagina = str_replace("[surname-error]","",$pagina);

    if ($userInfo["data_nascita"]) {//controllo se la data di nascita è presente
        $pagina = str_replace("[data-nascita]",$userInfo["data_nascita"],$pagina);//[data-nascita] presente nell'input data viene sostituito con la data di nascita dell'utente
    } else {
        $pagina = str_replace("[data-nascita]","",$pagina);
    }

    echo $pagina;

} elseif(isset($_POST["submit-user-settings"])) {//controllo se l'utente ha inviato il form per le informazioni utente
    $errorFound = false;
    $pagina = str_replace("[old-password-error]","",$pagina);
    $pagina = str_replace("[new-password-error]","",$pagina);
    $pagina = str_replace("[repeat-password-error]","",$pagina);
    
    //Controlla se l'utente ha inserito un nuovo username
    $username = $_POST["new-username"];
    if (mb_strlen($username) < 4) {
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> deve avere una lunghezza minima di 4 caratteri</p>',$pagina);
        $errorFound=true;
    } elseif (mb_strlen($username) > 16) {
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> non deve superare i 16 caratteri</p>',$pagina);
        $errorFound=true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ0-9]+$/",$username) == 0) {
        $errorFound=true;
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error"><span lang="en">Username</span> non valido, usa solo lettere o numeri.',$pagina);
    } else {
        $username = Sanitizer::SanitizeUsername($username);
        $isUserPresent = $db->ThisUsernameExists($username);
        if (strcmp($isUserPresent,"Execution error") != 0 && strcmp($isUserPresent,"Connection error") != 0 && $isUserPresent == true && strcmp($username,$isLogged) != 0) {
            $errorFound = true;
            $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> inserito non può essere utilizzato</p>',$pagina);
        } else if (strcmp($isUserPresent,"Execution error") == 0 || strcmp($isUserPresent,"Connection error") == 0) {
            $_POST = null;
            header('Location: 500.php');
            exit();
        } else {
            $pagina = str_replace("[username-error]","",$pagina);
        }
    }
    //Controlla se l'utente ha inserito un nuovo nome
    $name = $_POST["new-name"];
    if(mb_strlen($name) == 0){
        $pagina = str_replace("[name-error]",'<p role="alert" class="error" id="name-error">Se vuoi modificare il nome devi inserire almeno un carattere</p>',$pagina);
        $errorFound=true;
    } elseif(mb_strlen($name) > 15) {
        $pagina = str_replace("[name-error]",'<p role="alert" class="error" id="name-error">Il nome non deve superare i 15 caratteri</p>',$pagina);
        $errorFound=true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ]+$/",$name) == 0) {
        $errorFound=true;
        $pagina = str_replace("[name-error]",'<p role="alert" class="error" id="name-error">Nome non valido, usa solo lettere.</p>',$pagina);
    } else {
        $name = Sanitizer::SanitizeUsername($name);
        $pagina = str_replace("[name-error]","",$pagina);
    }
    //controllo se l'utente ha inserito un nuovo cognome
    $surname = $_POST["new-surname"];
    if(mb_strlen($surname) == 0){
        $pagina = str_replace("[surname-error]",'<p role="alert" class="error" id="surname-error">Se vuoi modificare il cognome devi inserire almeno un carattere</p>',$pagina);
        $errorFound=true;
    } elseif(mb_strlen($surname) > 15) {
        $pagina = str_replace("[surname-error]",'<p role="alert" class="error" id="surname-error">Il cognome non deve superare i 15 caratteri</p>',$pagina);
        $errorFound=true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ]+$/",$surname) == 0) {
        $errorFound=true;
        $pagina = str_replace("[surname-error]",'<p role="alert" class="error" id="surname-error">Cognome non valido, usa solo lettere.</p>',$pagina);
    } else {
        $surname = Sanitizer::SanitizeUsername($surname);
        $pagina = str_replace("[surname-error]","",$pagina);
    }
    //controllo se l'utente ha inserito una nuova data di nascita
    $birthday = $_POST["new-date"];
    if(mb_strlen($birthday) == 0) {
        $pagina = str_replace("[data-error]",'<p role="alert" class="error" id="data-error">Inserisci una data di nascita valida</p>',$pagina);
        $errorFound=true;
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $birthday);
        if ($date && $date->format('Y-m-d') === $birthday) {
            $pagina = str_replace("[data-error]","",$pagina);
        } else {
            $pagina = str_replace("[data-error]",'<p role="alert" class="error" id="data-error">Inserisci una data di nascita valida</p>',$pagina);
            $errorFound=true;
        }
    }

    if($hasProfileImageBeenDeleted==false && isset($_FILES["new-logo"]) && $_FILES["new-logo"]["error"]==0) {//controlla se l'utente ha caricato un'immagine profilo
        $tmpFile = '/tmp//'.$_FILES["new-logo"]["name"];//crea un file temporaneo per l'immagine
        rename($_FILES["profile-img-edit"]["tmp_name"],'/tmp//'.$_FILES["new-logo"]["name"]);//sposta il file temporaneo nella cartella /tmp
        $info = new SplFileInfo($tmpFile);//crea un oggetto SplFileInfo per ottenere le informazioni sul file
        $extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);//ottiene l'estensione del file
        $extensionArray = array("jpg","jpeg","png");//array delle estensioni supportate

        if(mb_strlen($extension) == 0 || !in_array($extension,$extensionArray)) {//controllo se l'estensione del file è supportata
            $errorFound=true;
            $pagina = str_replace("[logo-error]",'<p role="alert" id="logo-error" class="error">L\'estensione dell\'immagine caricata non è corretta</p>',$pagina);
        } elseif($info->isExecutable()) {//controllo se il file è eseguibile
            $errorFound=true;
            $pagina = str_replace("[logo-error]",'<p role="alert" id="logo-error" class="error">Il <span lang="en">file</span> caricato non è supportato</p>',$pagina);
        } elseif($info->getSize() > 2097152) {//controllo se la dimensione del file è superiore a 2MB
            $errorFound=true;
            $pagina = str_replace("[logo-error]",'<p role="alert" id="logo-error" class="error">Sono accettati solo immagini di dimensione inferiore a 2<span lang="en" abbr="megabyte">MB</span></p>',$pagina);
        } elseif(strcmp(mime_content_type($tmpFile),"image/jpeg") != 0 && strcmp(mime_content_type($tmpFile),"image/png")!=0) {//
            $errorFound=true;
            $pagina = str_replace("[logo-error]",'<p role="alert" id="logo-error" class="error">Il <span lang="en">file</span> caricato è un formato non supportato</p>',$pagina);
        } else {
            if(!is_dir($userBasePath)) {//controllo se la cartella base per le immagini profilo esiste
                mkdir($userBasePath);//crea la cartella base se non esiste
            }
            if(!is_dir($userBasePath.$userPath)) {//controllo se la cartella per l'utente esiste
                mkdir($userBasePath.$userPath);//crea la cartella per l'utente se non esiste
            }

            $files = glob($userBasePath.$userPath.'*'); //cancello tutti i file già presenti
            foreach($files as $file){// rimuovo tutti i file presenti
                if(is_file($file)) {// controllo se il file è un file
                    unlink($file);// rimuovo il file
                }
            }

            switch (mime_content_type($tmpFile)) {//controllo il tipo di file
                case 'image/jpeg'://controllo se il file è un'immagine jpeg
                    $img = imagecreatefromjpeg($tmpFile);
                    break;
                
                case 'image/png'://controllo se il file è un'immagine png
                    $img = imagecreatefrompng($tmpFile);
                    break;
                
                default://controllo se il file è un tipo di file non supportato
                    header("Location : 500.php");
                    break;
            }

            imagewebp($img,$userBasePath.$userPath.$isLogged.".webp");//converte l'immagine in formato webp e la salva nella cartella dell'utente
            unset($_FILES["new-logo"]);//rimuove il file temporaneo
            $isProfileImageChanged = true;
            $pagina = str_replace("[logo-error]","",$pagina);            
        }
    } else {
        $pagina = str_replace("[logo-error]","",$pagina);
    }
        
    if($errorFound == true) {//nel caso ci siano errori
        $pagina = str_replace("[username]",$isLogged,$pagina);
        if ($userInfo["url_immagine"]) {
            $pagina = str_replace("[logo]",$userInfo["url_immagine"],$pagina);//logo sarà l'src dell'immagine profilo(in html)
        } else {
            $pagina=str_replace("[logo]","./asset/img/def-logo.webp",$pagina);
        }
        $pagina = str_replace("[nome]",$userInfo["nome"],$pagina);
        $pagina = str_replace("[cognome]",$userInfo["cognome"],$pagina);
        $pagina = str_replace("[data-nascita]",$userInfo["data_nascita"],$pagina);
        //[nome/cognome/username/data-nascita] sono i campi che verranno riempiti con i dati dell'utente(input in html)
        echo $pagina;
    } else {//nel caso non ci siano errori
        if(strcmp($username,$isLogged) != 0 || strcmp($name,$userInfo["nome"]) != 0 
        || strcmp($surname,$userInfo["cognome"]) != 0 || strcmp($birthday,$userInfo["data_nascita"]) != 0 
        || $isProfileImageChanged == true || $hasProfileImageBeenDeleted == true) {

            $changeResult = false;
            $imagedef = "";

            if($userInfo["url_immagine"] || $isProfileImageChanged==true) {//controllo se l'utente ha un'immagine profilo o se l'ha cambiata

            	$imagedef = scandir($userBasePath.$userPath);//recupero i file presenti nella cartella dell'utente
		        $info = new SplFileInfo($imagedef[2]);//creo un oggetto SplFileInfo per ottenere le informazioni sul file
		        $extension = pathinfo($info->getFilename(), PATHINFO_EXTENSION);//ottiene l'estensione del file
		        $imagedef = "./user_profiles/".$username.'/'.$username.".".$extension;//imagedef sarà il percorso dell'immagine profilo dell'utente

                if(is_dir($userBasePath.$userPath) && strcmp($username,$isLogged)!=0) {//controllo se la cartella dell'utente esiste e se l'username è diverso da quello attuale
                    rename($userBasePath.$userPath.$username.".".$extension,$userBasePath.$userPath.$username.".".$extension); //rinomina immagine con il nuovo username dell'utente
                    rename($userBasePath.$userPath,"./user_profiles/".$username.'/'); //rinomina la cartella dell'utente    
                }

                if($userInfo["url_immagine"] && $hasProfileImageBeenDeleted==true) {//controlla se l'utente ha eliminato l'immagine profilo
                    $files = glob($userBasePath.$userPath.'*'); //cancella tutti i file già presenti
                    foreach($files as $file){ //rimuove tutti i file presenti
                        if(is_file($file)) {
                            unlink($file);
                        }
                    }
                    $imagedef = NULL;
                }

                $changeResult = $db->ChangeMainInfo($username,$name,$surname,$birthday,$imagedef);
                if(is_bool($changeResult) && $changeResult == true) {//controllo se la modifica delle informazioni è andata a buon fine
                    header('Location: user-profile.php');//reindirizza alla pagina delle informazioni utente
                    exit();
                } else { 
                    header('Location: user-profile.php');
                    echo "2";
                    exit();
                }
            } else {
                $changeResult = $db->ChangeMainInfo($username,$name,$surname,$birthday,"assets/img/Cuochi.png");
                if(is_bool($changeResult) && $changeResult == true) {//controllo se la modifica delle informazioni è andata a buon fine
                    header('Location: user-profile.php');//reindirizza alla pagina delle informazioni utente
                    exit();
                } else { 
                    header('Location: user-profile.php');
                    echo "2";
                    exit();
                }
            }
        }
    }

} else {//controlla se l'utente ha inviato il form per la modifica della password

    //cancellazione dei messaggi di errore
    $pagina = str_replace("[username-error]","",$pagina);
    $pagina = str_replace("[name-error]","",$pagina);
    $pagina = str_replace("[surname-error]","",$pagina);
    $pagina = str_replace("[data-error]","",$pagina);
    $pagina = str_replace("[logo-error]","",$pagina);

    //inserimento i dati dell'utente nei campi del form
    $pagina = str_replace("[username]",$isLogged,$pagina);
    $pagina = str_replace("[nome]",$userInfo["nome"],$pagina);
    $pagina = str_replace("[cognome]",$userInfo["cognome"],$pagina);
    $pagina = str_replace("[data-nascita]",$userInfo["data_nascita"],$pagina);
    if ($userInfo["url_immagine"]) {
        $pagina = str_replace("[logo]",$userInfo["url_immagine"],$pagina);
    } else {
        $pagina = str_replace("[logo]","./asset/img/def-logo.webp",$pagina);
    }

    $errorFound = false;
    $oldPassword = "";
    $newPassword = "";
    $repeatNewPassword = "";

    if(isset($_POST["old-password"])) {
        $oldPassword = $_POST["old-password"];
        unset($_POST["old-password"]);
    }
    if(isset($_POST["new-password"])) {
        $newPassword = $_POST["new-password"];
        unset($_POST["new-password"]);
    }
    if(isset($_POST["confirm-new-password"])) {
        $repeatNewPassword = $_POST["confirm-new-password"];
        unset($_POST["confirm-new-password"]);
    }

    if(strcmp($oldPassword,"") == 0) {
        $pagina = str_replace("[old-password-error]",'<p role="alert" class="error" id="old-password-error">Inserisci la <span lang="en">password</span> attuale</p>',$pagina);
        $errorFound = true;
    }elseif(mb_strlen($oldPassword) < 8) {
        $pagina = str_replace("[old-password-error]",'<p role="alert" class="error" id="old-password-error">La <span lang="en">password</span> deve avere una lunghezza minima di 8 caratteri.</p>',$pagina);
        $errorFound = true;
    } elseif (preg_match("/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,=#!?@+\-_€$%&^*<>]).+$/",$oldPassword) == 0) {
        $errorFound = true;
        $pagina = str_replace("[old-password-error]",'<p role="alert" class="error" id="old-password-error">La <span lang="en">password</span> attuale deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.</p>',$pagina);
    } else {
        $pagina=str_replace("[old-password-error]","",$pagina);
    }
    if(mb_strlen($newPassword) < 8) {
        $pagina = str_replace("[password-error]",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> deve avere una lunghezza minima di 8 caratteri.</p>',$pagina);
        $errorFound = true;
    } elseif (preg_match("/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,=#!?@+\-_€$%&^*<>]).+$/",$newPassword) == 0) {
        $errorFound = true;
        $pagina = str_replace("[password-error]",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> deve contenere almeno una lettera minuscola, una lettera maiuscola, un numero e un carattere speciale.</p>',$pagina);
    } else {
        $pagina = str_replace("[password-error]","",$pagina);
    }
    if(strcmp($newPassword,$repeatNewPassword) != 0) {
        $pagina = str_replace("[repeat-password-error]",'<p role="alert" class="error" id="repeat-password-error">Le <span lang="en">password</span> non coincidono.</p>',$pagina);
        $errorFound = true;
    } else {
        $pagina = str_replace("[repeat-password-error]","",$pagina);
    }

    if($errorFound == true) {//nel caso ci siano errori
        $pagina = str_replace("[repeat-password-error]","",$pagina);
        echo $pagina;
        exit();
    }

    $result = $db->ChangePassword($oldPassword,$newPassword);//chiamata alla funzione per cambiare la password
    if(is_string($result) && strcmp($result,"Password change failed") == 0) {//controllo se la modifica della password è fallita
        $pagina = str_replace("[old-password-error]",'<p role="alert" class="error" id="old-password-error">La <span lang="en">password</span> attuale inserita non è corretta</p>',$pagina);
        echo $pagina;
        exit();
    } elseif (is_string($result)) {//controllo se la modifica della password ha generato un errore
        header('Location: 500.php');
        exit();
    } else {//controllo se la modifica della password è andata a buon fine
        header('Location: user-profile.php');
        exit();
    }
}

?>