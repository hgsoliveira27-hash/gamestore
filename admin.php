
<?php

session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['usuario_tipo']) || $_SESSION['usuario_tipo'] != 'admin') {
    echo "Acesso negado! Somente administradores podem entrar.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Painel Admin - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>GameStore 🎮</h1>
    <h2>Painel Administrativo</h2>
</header>

<main class="container">

    <h2>Bem-vindo, administrador!</h2>

    <p>Escolha uma opção:</p>

    <p><a href="index.php">Ver loja</a></p>

    <p><a href="produtos_admin.php">Gerenciar produtos</a></p>

    <p><a href="pedidos_admin.php">Ver pedidos</a></p>

    <p><a href="logout.php">Sair</a></p>

</main>

</body>
</html>
