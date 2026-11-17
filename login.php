
<?php

session_start();
require_once 'config/conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Digite um e-mail válido.';

    } else {

        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_tipo'] = $usuario['tipo'];

            header('Location: index.php');
            exit;

        } else {

            $mensagem = 'E-mail ou senha incorretos.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>GameStore 🎮</h1>
    <h2>Entrar na conta</h2>
</header>

<main class="container">

    <h2>Login</h2>

    <p><?php echo htmlspecialchars($mensagem); ?></p>

    <form method="POST">

        <input
            type="email"
            name="email"
            placeholder="Seu e-mail"
            required
        >

        <br>

        <input
            type="password"
            name="senha"
            placeholder="Sua senha"
            required
        >

        <br><br>

        <button type="submit">Entrar</button>

    </form>

    <p>
        Não tem conta?
        <a href="cadastro.php">Cadastre-se</a>
    </p>

    <a href="index.php">Voltar para a loja</a>

</main>

</body>
</html>
