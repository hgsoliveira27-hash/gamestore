
<?php

session_start();

// Apagar os dados da sessão
$_SESSION = [];

// Apagar o cookie da sessão
if (ini_get('session.use_cookies')) {

    $parametros = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $parametros['path'],
        $parametros['domain'],
        $parametros['secure'],
        $parametros['httponly']
    );

}

// Encerrar a sessão
session_destroy();

// Voltar para a loja
header('Location: index.php');
exit;

?>
