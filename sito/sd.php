<?php
// Avvia la sessione (necessario se non è già stata avviata)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pulisci tutte le variabili di sessione
$_SESSION = array();

// Se vuoi anche distruggere il cookie della sessione
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000, // scadenza nel passato
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Distruggi la sessione
session_destroy();

// Eventualmente reindirizza l'utente
header("Location: login.php");
exit();
?>