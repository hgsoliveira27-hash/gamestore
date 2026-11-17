
<?php

session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_SESSION['usuario_id'];
$mensagem = '';

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senhaAtual = $_POST['senha_atual'];
    $novaSenha = $_POST['nova_senha'];

    if ($nome == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = 'Preencha os dados corretamente.';

    } else {

        $sql = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$email, $id]);

        if ($stmt->fetch()) {

            $mensagem = 'Este e-mail já está cadastrado.';

        } elseif ($novaSenha != '' &&
                  (!password_verify($senhaAtual, $usuario['senha']) ||
                  strlen($novaSenha) < 6)) {

            $mensagem = 'Confira a senha atual e a nova senha (mínimo 6 caracteres).';

        } else {

            if ($novaSenha != '') {

                $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

                $sql = "UPDATE usuarios
                        SET nome = ?, email = ?, senha = ?
                        WHERE id = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $senhaHash, $id]);

            } else {

                $sql = "UPDATE usuarios
                        SET nome = ?, email = ?
                        WHERE id = ?";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nome, $email, $id]);

            }

            $_SESSION['usuario_nome'] = $nome;

            $mensagem = 'Perfil atualizado com sucesso!';

            $sql = "SELECT * FROM usuarios WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>GameStore 🎮</h1>
    <h2>Meu Perfil</h2>
</header>

<main class="container">

    <h2>Editar meus dados</h2>

    <p><?php echo htmlspecialchars($mensagem); ?></p>

    <form method="POST">

        <input type="text" name="nome"
        value="<?php echo htmlspecialchars($usuario['nome']); ?>"
        placeholder="Nome" required>

        <br>

        <input type="email" name="email"
        value="<?php echo htmlspecialchars($usuario['email']); ?>"
        placeholder="E-mail" required>

        <h3>Alterar senha (opcional)</h3>

        <input type="password" name="senha_atual"
        placeholder="Senha atual">

        <br>

        <input type="password" name="nova_senha"
        placeholder="Nova senha (mínimo 6 caracteres)"
        minlength="6">

        <br><br>

        <button type="submit">Salvar alterações</button>

    </form>

    <br>

    <a href="index.php">Voltar para a loja</a>

</main>

</body>
</html>
