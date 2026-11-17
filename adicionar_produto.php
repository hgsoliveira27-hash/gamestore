<?php
session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] ?? '') != 'admin') {
    header('Location: login.php');
    exit;
}

$mensagem = '';

$sql = "SELECT * FROM categorias ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = filter_var($_POST['preco'] ?? '', FILTER_VALIDATE_FLOAT);
    $estoque = filter_var($_POST['estoque'] ?? '', FILTER_VALIDATE_INT);
    $imagem = trim($_POST['imagem'] ?? '');
    $categoriaId = filter_var($_POST['categoria_id'] ?? '', FILTER_VALIDATE_INT);

    if ($nome == '' || $preco === false || $preco < 0 ||
        $estoque === false || $estoque < 0 || !$categoriaId) {
        $mensagem = 'Preencha todos os campos corretamente.';
    } else {
        $sql = "SELECT id FROM categorias WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoriaId]);

        if (!$stmt->fetch()) {
            $mensagem = 'Selecione uma categoria válida.';
        } else {
            $sql = "INSERT INTO produtos
                    (nome, descricao, preco, estoque, imagem, categoria_id)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nome, $descricao, $preco, $estoque, $imagem, $categoriaId]);

            $mensagem = 'Produto cadastrado com sucesso!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Produto - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>GameStore 🎮</h1>
    <h2>Adicionar Produto</h2>
</header>

<main class="container">
    <p><?php echo htmlspecialchars($mensagem); ?></p>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome do produto" required>
        <br>
        <textarea name="descricao" placeholder="Descrição do produto"></textarea>
        <br>
        <input type="number" name="preco" placeholder="Preço" step="0.01" min="0" required>
        <br>
        <input type="number" name="estoque" placeholder="Quantidade em estoque" min="0" required>
        <br>
        <input type="text" name="imagem" placeholder="Nome da imagem (ex: fone.jpg)">
        <br>

        <select name="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            <?php foreach ($categorias as $categoria) { ?>
                <option value="<?php echo (int)$categoria['id']; ?>">
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <button type="submit">Cadastrar produto</button>
    </form>

    <br>
    <a href="produtos_admin.php">Voltar aos produtos</a>
</main>
</body>
</html>
