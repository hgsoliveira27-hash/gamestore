
<?php

session_start();
require_once 'config/conexao.php';

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    if ($nome == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Preencha os dados corretamente.';

    } elseif (strlen($senha) < 6) {

        $mensagem = 'A senha precisa ter pelo menos 6 caracteres.';

    } else {

        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $mensagem = 'Este e-mail já está cadastrado.';

        } else {

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
                    VALUES (?, ?, ?, 'cliente')";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                $nome,
                $email,
                $senhaHash
            ]);

            $mensagem = 'Cadastro realizado! Agora você pode entrar.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>GameStore 🎮</h1>
    <h2>Criar conta</h2>
</header>

<main class="container">

    <h2>Cadastro de usuário</h2>

    <p><?php echo htmlspecialchars($mensagem); ?></p>

    <form method="POST">

        <input
            type="text"
            name="nome"
            placeholder="Seu nome"
            required
        >

        <br>

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
            minlength="6"
            required
        >

        <br><br>

        <button type="submit">Cadastrar</button>

    </form>

    <p>
        Já tem uma conta?
        <a href="login.php">Entrar</a>
    </p>

    <a href="index.php">Voltar para a loja</a>

</main>

</body>
</html>
