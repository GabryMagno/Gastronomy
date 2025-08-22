
<?php

require_once "php/db.php";
require_once "php/sanitizer.php";

$pagina=file_get_contents("html/login.html");

$username="";
$psw="";
$error = false;
$db = new DB;

$isLogged = $db->IsUserLog();
if ($isLogged!=false) {
    $_POST = null;
    header('Location: user-profile.php');
    exit();
}

if(isset($_GET["ref"])) {
    $pagina=str_replace("{{ref-value}}",'?ref='.$_GET["ref"],$pagina);
} else {
    $pagina=str_replace("{{ref-value}}","",$pagina);
}

if (isset($_POST['submit-login'])) {
    
    //CONTROLLO USERNAME
    $username = $_POST["username"];
    if(mb_strlen($username) == 0) {
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> è un campo obbligatorio</p>',$pagina);
        $error = true;
    } elseif (mb_strlen($username) < 4) {
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> deve avere una lunghezza minima di 4 caratteri</p>',$pagina);
        $error = true;
    } elseif (mb_strlen($username) > 16) {
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> non deve superare i 16 caratteri</p>',$pagina);
        $error = true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ0-9]+$/",$username) == 0) {
        $error = true;
        $pagina = str_replace("[username-error]",'<p role="alert" class="error" id="username-error"><span lang="en">Username</span> non valido, usa solo lettere o numeri.</p>',$pagina);
    } else {
        $username = Sanitizer::SanitizeUsername($username);
    }

    //CONTROLLO PASSWORD
    $password = $_POST["password"];
    if($username == "user" && $password == "user") {//caso di test
        $pagina = str_replace("[username-error]","",$pagina);// rimuove l'errore se non c'è
        $pagina = str_replace("[password-error]","",$pagina);// rimuove l'errore se non c'è
        $_POST = null;
        if(isset($_GET["ref"])) {//se voglio commentare un prodotto, prima eseguo il login e poi torno alla pagina del prodotto
            $link = "prodotto.php?prodotto=".$_GET["ref"]."#login-needed";// crea il link per tornare alla pagina del prodotto
            unset($_GET["ref"]);//elimino il parametro ref dalla query string
            header("Location: ".$link);
            exit();
        }
        header("Location: user-settings.php");
        exit();
    }
    if (mb_strlen($password) == 0) {// controllo se la password è vuota
        $pagina = str_replace("[password-error]",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> è un campo obbligatorio</p>',$pagina);
        $error = true;
    } elseif (mb_strlen($password) < 8) {// controllo se la password è troppo corta
        $pagina = str_replace("[password-error]",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> deve avere una lunghezza minima di 8 caratteri</p>',$pagina);
        $error = true;
    }elseif (preg_match("/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>=]).+$/",$password)==0) {
        $error=true;
        $pagina = str_replace("[password-error]",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> deve avere una lettera maiuscola, una lettera minuscola, un numero e un carattere speciale</p>',$pagina);
    }

    if($error == false){
        $result = $db->LoginUser($username,$password);

        if ($result==true && is_bool($result)) {
            $_POST = null;
            if(isset($_GET["ref"])) {//se voglio commentare un prodotto, prima eseguo il login e poi torno alla pagina del prodotto
                $link = "prodotto.php?prodotto=".$_GET["ref"]."#login-needed";// crea il link per tornare alla pagina del prodotto
                unset($_GET["ref"]);//elimino il parametro ref dalla query string
                header("Location: ".$link);
                exit();
            }
            header("Location: user-settings.php");
            exit();
        } else if ($result == false) {
            $pagina = str_replace("[username-error]","",$pagina);
            $pagina = str_replace("[password-error]","",$pagina);
            echo str_replace("[error]",'<p role="alert" class="error" id="sign-error">Le credenziali inserite non sono corrette!</p>',$pagina);// se le credenziali non sono corrette
        } else if (strcmp($result,"User already logged in") == 0) {// se l'utente è già loggato
            $_POST = null;
            header('Location: user-settings.php');
            exit();
        } else {// in caso di errore nel database
            $_POST = null;
            header('Location: 500.php');
            exit();
        }
    }else{
        $pagina = str_replace('','',$pagina);
        $pagina = str_replace("id=login_username",'id="login_username" value="'.$username.'"',$pagina);
        echo $pagina;
    }
} else {//se non ha fatto il submit del form
    $pagina = str_replace("[username-error]","",$pagina);
    $pagina = str_replace("[password-error]","",$pagina);
    $pagina = str_replace("[error]","",$pagina);
    echo $pagina;
}

?>
