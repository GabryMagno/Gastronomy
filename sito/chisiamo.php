
<?php

require_once "php/db.php";
require_once "php/sanitizer.php";

$db = new DB;

$pagina = file_get_contents("html/chisiamo.html");
$isUserLogged=$db->isUserLog();

if(!isset($_POST["advice"])) {

    if ($isUserLogged!=false) {//se l'utente è loggato, mostra il suo profilo
        $pagina=str_replace("[to-profile]","<a href=\"user-profile.php\">Profilo</a>",$pagina);//se l'utente è loggato, mostra il link al suo profilo
        $userInfo=$db->getUserInfo();//ottiene le informazioni dell'utente loggato
        if (is_string($userInfo) && (strcmp($userInfo,"Execution error")==0 || strcmp($userInfo,"User not found")==0 || strcmp($userInfo,"Connection error")==0)) {//se c'è un errore nell'ottenere le informazioni dell'utente, reindirizza alla pagina di errore
            header('Location: 500-err.php');
            exit();
        } else if(is_string($userInfo) && strcmp($userInfo,"User Is not logged")==0) {
            header('Location: areariservata.php');
            exit();
        }
    } else {//se l'utente non è loggato, mostra il link per il login
        $pagina=str_replace("[to-profile]","<a href=\"login.php\"><span lang=\"en\">Login</span></a>",$pagina);
    }

    if(isset($_SESSION["adviceError"])) {//se c'è un errore nella sessione, lo mostra
        $pagina=str_replace("[comment-error]",$_SESSION["adviceError"],$pagina);//se ci sono errori, mostra il messaggio di errore
        //advice-error andrà messo come segnaposto in chisiamo.html per gli errori riguardanti il suggerimento
        unset($_SESSION["adviceError"]);//elimina il messaggio di errore dalla sessione
    } else {
        $pagina=str_replace("[comment-error]","",$pagina);//se non ci sono errori, rimuove il messaggio di errore
    }
    echo $pagina;
} else {
    unset($_POST["advice"]);//rimuove il campo "advice" da $_POST per evitare problemi
    $result="";

    $newAdvice = Sanitizer::SanitizeText($_POST["suggerimento"]);//sanitizza il testo del suggerimento
    unset($_POST["suggerimento"]);//rimuove il campo "suggerimento" da $_POST per evitare problemi

    if(mb_strlen($newAdvice)<30) {//controlla la lunghezza del messaggio
        $_SESSION["adviceError"]='<p role="alert" class="error" id="advice-error">La lunghezza minima del messaggio non deve essere inferiore ai 30 caratteri</p>';
        header('Location: chisiamo.php');
        exit();
    }else if(mb_strlen($newAdvice)>300){//controlla la lunghezza del messaggio
        $_SESSION["adviceError"]='<p role="alert" class="error" id="advice-error">La lunghezza massima del messaggio non deve superare i 300 caratteri</p>';
        header('Location: chisiamo.php');
        exit();
    }

    if($isUserLogged!=false) {//se l'utente è loggato, aggiunge il suggerimento con l'id dell'utente
        echo "Invio utente loggato";
        $result=$db->AddAdvice($newAdvice,$isUserLogged);
    } else {// se l'utente non è loggato, aggiunge il suggerimento senza l'id dell'utente
        echo "Invio senza utente loggato";
        $result=$db->AddAdvice($newAdvice);
    }
    
    if($result==false) {//se c'è un errore nell'aggiungere il suggerimento, reindirizza alla pagina di errore
        header('Location: 500.php');
        exit();
    } else {// se il suggerimento è stato aggiunto correttamente, reindirizza alla pagina di ringraziamento
        header('Location: chisiamo.php');//faremo una pagina di ringraziamento
        exit();
    }
}

?>
