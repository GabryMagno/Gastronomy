
<?php

require_once "php/db.php";
require_once "php/sanitizer.php";

$username="";
$categoria="";
$mail="";
$psw="";
$db = new DB;

$isLogged=$db->isUserLog();
if ($isLogged!=false) {
    $_POST = null;
    header('Location: user-profile.php');
    exit();
}

$pagina=file_get_contents("html/register.html");

if(isset($_GET["ref"])) {
    $pagina=str_replace("{{ref-value}}",'?ref='.$_GET["ref"],$pagina);
} else {
    $pagina=str_replace("{{ref-value}}","",$pagina);
}

if (isset($_POST['submit-sign-up'])) {
    $error = false;
    $username = $_POST["username"];
    //CONTROLLO USERNAME
    if (mb_strlen($username) == 0) {
        $pagina = str_replace("{{username-error}}",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> è un campo obbligatorio</p>',$pagina);
        $error = true;
    } elseif(mb_strlen($username) < 4) {
        $pagina = str_replace("{{username-error}}",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> deve avere una lunghezza minima di 4 caratteri</p>',$pagina);
        $error = true;
    } elseif (mb_strlen($username) > 16) {
        $pagina = str_replace("{{username-error}}",'<p role="alert" class="error" id="username-error">Lo <span lang="en">username</span> non deve superare i 16 caratteri</p>',$pagina);
        $error = true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ0-9]+$/",$username) == 0) {
        $error = true;
        $pagina = str_replace("{{username-error}}",'<p role="alert" class="error" id="username-error"><span lang="en">Username</span> non valido, usa solo lettere o numeri.</p>',$pagina);
    } else {
        $username = Sanitizer::SanitizeUsername($username);
        $isUsernameAlreadyExists = $db->ThisUsernameExists($username,false);
        if (strcmp($isUsernameAlreadyExists,"Execution error") != 0 && strcmp($isUsernameAlreadyExists,"Connection error") != 0 && $isUsernameAlreadyExists == true) {
            $error = true;
            $pagina = str_replace("{{username-error}}",'<p role="alert" class="error">Lo <span lang="en">username</span> inserito non può essere utilizzato</p>',$pagina);
        } elseif (strcmp($isUsernameAlreadyExists,"ExceptionThrow") == 0 || strcmp($isUsernameAlreadyExists,"ConnectionFailed") == 0) {
            $_POST = null;
            header('Location: 500.php');
            exit();
        } else {
            $pagina = str_replace("{{username-error}}","",$pagina);
        }
    }

    //CONTROLLO NOME
    $name = $_POST['nome'];
    if (mb_strlen($name) == 0) {
        $pagina = str_replace("{{name-error}}",'<p role="alert" class="error" id="name-error">Il nome è un campo obbligatorio</p>',$pagina);
        $error = true;
    } elseif (mb_strlen($name) > 15) {
        $pagina = str_replace("{{name-error}}",'<p role="alert" class="error" id="name-error">Il nome non deve superare i 15 caratteri</p>',$pagina);
        $error = true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ0-9]+$/",$name) == 0) {
        $error = true;
        $pagina = str_replace("{{name-error}}",'<p role="alert" class="error" id="name-error">Nome non valido, usa solo lettere.</p>',$pagina);
    } else {
        $name = Sanitizer::SanitizeUsername($name);
        $pagina = str_replace("{{name-error}}","",$pagina);
    }

    //CONTROLLO COGNOME
    $surname = $_POST['cognome'];
    if (mb_strlen($surname) == 0) {
        $pagina = str_replace("{{surname-error}}",'<p role="alert" class="error" id="surname-error">Il cognome è un campo obbligatorio</p>',$pagina);
        $error=true;
    } elseif (mb_strlen($surname) > 15) {
        $pagina = str_replace("{{surname-error}}",'<p role="alert" class="error" id="surname-error">Il cognome non deve superare i 15 caratteri</p>',$pagina);
        $error=true;
    } elseif (preg_match("/^[a-zA-ZÀ-Ýß-ÿ]+$/",$surname)==0) {
        $error=true;
        $pagina = str_replace("{{surname-error}}",'<p role="alert" class="error" id="surname-error">Cognome non valido, usa solo lettere..</p>',$pagina);
    } else {
        $surname=Sanitizer::SanitizeUsername($surname);
        $pagina = str_replace("{{surname-error}}","",$pagina);
    }

    //CONTROLLO DATA DI NASCITA
    $birthdate = $_POST['data'];
    $date = DateTime::createFromFormat('Y-m-d', $birthdate);
    $isValidDate = $date && $date->format('Y-m-d') === $birthdate;
    $minYear = 1900;
    $year = (int)date('Y', strtotime($birthdate));

    if (!$isValidDate) {
        $error = true;
        $pagina = str_replace("{{date-error}}",'<p role="alert" class="error" id="date-error">La <span lang="en">data</span> di nascita non è valida</p>',$pagina);
    } else {
        $currentDate = new DateTime();
        $birthDateObj = DateTime::createFromFormat('Y-m-d', $birthdate);
        if ($birthDateObj > $currentDate) {
            $error = true;
            $pagina = str_replace("{{date-error}}",'<p role="alert" class="error" id="date-error">La <span lang="en">data</span> di nascita non può essere futura</p>',$pagina);
        } elseif($currentDate->diff($birthDateObj)->y < 18) {
            $error = true;
            $pagina = str_replace("{{date-error}}",'<p role="alert" class="error" id="date-error">Per registrarti devi avere almeno 18 anni</p>',$pagina);
        } elseif ($minYear > $year) {
            $error = true;
            $pagina = str_replace("{{date-error}}",'<p role="alert" class="error" id="date-error">Per registrarsi inserire un anno successivo al 1899 (almeno 1900)</p>',$pagina);
        }else{
            $pagina = str_replace("{{date-error}}","",$pagina);
        }
    }

    //CONTROLLO EMAIL
    $email=$_POST['email'];
    if (mb_strlen($email)==0) {
        $error=true;
        $pagina = str_replace("{{email-error}}",'<p role="alert" class="error" id="email-error">L\'<span lang="en">email</span> è un campo obbligatorio</p>',$pagina);
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error=true;
        $pagina = str_replace("{{email-error}}",'<p role="alert" class="err-msg" id="email-error">L\'<span lang="en">email</span> non è un indirizzo valido</p>',$pagina);
    } else {
        $isEmailExist = $db->ThisEmailExist($email);
        if (strcmp($isEmailExist,"ExceptionThrow")!=0 && strcmp($isEmailExist,"Connection error")!=0 && $isEmailExist==true) {
            $error=true;
            $pagina = str_replace("{{email-error}}",'<p role="alert" class="err-msg" id="email-error">Questa <span lang="en">email</span> non può essere utilizzata</p>',$pagina);
        } else if (strcmp($isEmailExist,"Execution error")==0 || strcmp($isEmailExist,"Connection error")==0) {
            $_POST = null;
            header('Location: 500.php');
            exit();
        } else {
            $pagina = str_replace("{{email-error}}","",$pagina);
        }
    }

    //CONTROLLO PASSWORD
    $password = $_POST['password'];
    if (mb_strlen($password)==0) {
        $error=true;
        $pagina = str_replace("{{repeat-password-error}}","",$pagina);
        $pagina = str_replace("{{password-error}}",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> è un campo obbligatorio</p>',$pagina);
    } elseif (mb_strlen($password)<8) {
        $error=true;
        $pagina = str_replace("{{repeat-password-error}}","",$pagina);
        $pagina = str_replace("{{password-error}}",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> deve essere lunga almeno 8 caratteri</p>',$pagina);
    } elseif (preg_match("/^(?=.*[a-zß-ÿ])(?=.*[A-ZÀ-Ý])(?=.*[\d])(?=.*[.,!?@+\-_€$%&^*<>=]).+$/",$password)==0) {
        $error=true;
        $pagina = str_replace("{{repeat-password-error}}","",$pagina);
        $pagina = str_replace("{{password-error}}",'<p role="alert" class="error" id="password-error">La <span lang="en">password</span> deve avere una lettera maiuscola, una lettera minuscola, un numero e un carattere speciale</p>',$pagina);
    } elseif (strcmp($password,$_POST['repeat-password'])!=0) {// controllo che le password siano uguali
        $pagina = str_replace("{{password-error}}","",$pagina);
        $error=true;
        $pagina = str_replace("{{repeat-password-error}}",'<p role="alert" class="error" id="repeat-password-error">Le <span lang="en">password</span> non coincidono</p>',$pagina);
    } else {// se la password è valida
        $pagina = str_replace("{{password-error}}","",$pagina);
        $pagina = str_replace("{{repeat-password-error}}","",$pagina);
    }
    
    //CONTROLLO ERRORI FINALE
    if ($error==false) {
        $result=$db->RegisterNewUser($username, $name, $surname, $date, $email, $password);
        if($result) {
            $_POST = null;
            if(isset($_GET["ref"])) {
                $link="prodotto.php?prodotto=".$_GET["ref"]."#eval-section";
                unset($_GET["ref"]);
                header("Location: ".$link);
                exit();
            }
            header('Location: user-profile.php');
            exit();
        } else {
            $_POST = null;
            header('Location: 500.php');
            exit();
        }
    } else {// se ci sono errori
        $pagina = str_replace('','',$pagina);
        $pagina = str_replace("id=register_username",'id="register_username" value="'.$username.'"',$pagina);
        $pagina = str_replace('id="register_nome"','id="register_nome" value="'.$name.'"',$pagina);
        $pagina = str_replace('id="register_cognome"','id="register_cognome" value="'.$surname.'"',$pagina);
        $pagina = str_replace('id="register_email"','id="register_email" value="'.$email.'"',$pagina);
        $pagina = str_replace('id="register_email','id="student-mail-up" value="'.$email.'"',$pagina);

        echo $pagina;
    }
} else {// se non ha fatto il submit del form
    $pagina = str_replace("{{username-error}}","",$pagina);
    $pagina = str_replace("{{name-error}}","",$pagina);
    $pagina = str_replace("{{surname-error}}","",$pagina);
    $pagina = str_replace("{{email-error}}","",$pagina);
    $pagina = str_replace("{{password-error}}","",$pagina);
    $pagina = str_replace("{{repeat-password-error}}","",$pagina);

    echo $pagina;
}

?>
