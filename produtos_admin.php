<?php
session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id']) || ($_SESSION['usuario_tipo'] ?? '') != 'admin') {
    header('Location: login.php');
    exit;
}

$sql = "SELECT produtos.*, categorias.nome AS categoria_nome
        FROM produtos
        LEFT JOIN categorias ON produtos.categoria_id = categorias.id
        ORDER BY produtos.id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Produtos - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1>GameStore 🎮</h1>
    <h2>Gerenciar Produtos</h2>
</header>

<main class="container">
    <h2>Produtos cadastrados</h2>
    <a href="admin.php">Voltar ao painel</a>
    <br><br>
    <a href="adicionar_produto.php" class="btn">Adicionar produto</a>
    <br><br>

    <?php if (count($produtos) > 0) { ?>
        <?php foreach ($produtos as $produto) { ?>
            <div class="produto">
                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                <p>Categoria: <?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?></p>
                <p>Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                <p>Estoque: <?php echo (int)$produto['estoque']; ?></p>

                <a href="editar_produto.php?id=<?php echo (int)$produto['id']; ?>" class="btn">
                    Editar
                </a>

                <form method="POST" action="excluir_produto.php"
                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                    <input type="hidden" name="id" value="<?php echo (int)$produto['id']; ?>">
                    <button type="submit" class="botao-remover">Excluir</button>
                </form>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>Nenhum produto cadastrado.</p>
    <?php } ?>
</main>
</body>
</html>
