<?php
session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] ?? '') != 'admin') {
    header('Location: login.php');
    exit;
}

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);
if (!$id || $id <= 0) {
    header('Location: produtos_admin.php');
    exit;
}

$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    echo "Produto não encontrado!";
    exit;
}

$sql = "SELECT * FROM categorias ORDER BY nome";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = filter_var($_POST['preco'] ?? '', FILTER_VALIDATE_FLOAT);
    $estoque = filter_var($_POST['estoque'] ?? '', FILTER_VALIDATE_INT);
    $imagem = trim($_POST['imagem'] ?? '');
    $categoriaId = filter_var($_POST['categoria_id'] ?? '', FILTER_VALIDATE_INT);

    if ($nome == '' || $preco === false || $preco < 0 ||
        $estoque === false || $estoque < 0 || !$categoriaId) {
        $mensagem = 'Preencha os campos corretamente.';
    } else {
        $sql = "SELECT id FROM categorias WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$categoriaId]);

        if (!$stmt->fetch()) {
            $mensagem = 'Selecione uma categoria válida.';
        } else {
            $sql = "UPDATE produtos
                    SET nome = ?, descricao = ?, preco = ?, estoque = ?,
                        imagem = ?, categoria_id = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nome, $descricao, $preco, $estoque,
                $imagem, $categoriaId, $id
            ]);

            $mensagem = 'Produto atualizado com sucesso!';

            $sql = "SELECT * FROM produtos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>GameStore 🎮</h1>
    <h2>Editar Produto</h2>
</header>

<main class="container">
    <p><?php echo htmlspecialchars($mensagem); ?></p>

    <form method="POST">
        <input type="text" name="nome"
               value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
        <br>

        <textarea name="descricao"><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></textarea>
        <br>

        <input type="number" name="preco"
               value="<?php echo htmlspecialchars($produto['preco']); ?>"
               step="0.01" min="0" required>
        <br>

        <input type="number" name="estoque"
               value="<?php echo (int)$produto['estoque']; ?>"
               min="0" required>
        <br>

        <input type="text" name="imagem"
               value="<?php echo htmlspecialchars($produto['imagem'] ?? ''); ?>"
               placeholder="Nome da imagem">
        <br>

        <select name="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            <?php foreach ($categorias as $categoria) { ?>
                <option value="<?php echo (int)$categoria['id']; ?>"
                    <?php if ($produto['categoria_id'] == $categoria['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($categoria['nome']); ?>
                </option>
            <?php } ?>
        </select>
        <br><br>

        <button type="submit">Salvar alterações</button>
    </form>

    <br>
    <a href="produtos_admin.php">Voltar</a>
</main>
</body>
</html>
