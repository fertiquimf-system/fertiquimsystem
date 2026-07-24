<?php
session_start();

// Remove todas as variáveis da sessão
$_SESSION = array();

// Se quiser matar também o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destrói a sessão
session_destroy();

// Redireciona para a tela de login
header("Location: ../pglogin/pglogin.php");
exit;
?>
