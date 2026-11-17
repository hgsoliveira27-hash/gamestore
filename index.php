<?php
session_start();
require_once 'config/conexao.php';

// Guardar o que foi digitado na busca
$busca = trim($_GET['busca'] ?? '');
$categoriaSelecionada = filter_var($_GET['categoria'] ?? '', FILTER_VALIDATE_INT);
if (!$categoriaSelecionada || $categoriaSelecionada < 1) {
    $categoriaSelecionada = 0;
}

// Buscar as categorias para o filtro
$sqlCategorias = "SELECT * FROM categorias ORDER BY nome";
$stmtCategorias = $pdo->prepare($sqlCategorias);
$stmtCategorias->execute();
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Montar a busca dos produtos
$sql = "SELECT produtos.*, categorias.nome AS categoria_nome
        FROM produtos
        LEFT JOIN categorias ON produtos.categoria_id = categorias.id
        WHERE 1 = 1";
$valores = [];

if ($busca != '') {
    $sql .= " AND produtos.nome LIKE ?";
    $valores[] = "%$busca%";
}

if ($categoriaSelecionada > 0) {
    $sql .= " AND produtos.categoria_id = ?";
    $valores[] = $categoriaSelecionada;
}

$sql .= " ORDER BY produtos.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($valores);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameStore - Loja Gamer</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="container">
        <h1>GameStore 🎮</h1>
        <nav>
            <a href="index.php">Início</a>

            <?php if (isset($_SESSION['usuario_id'])) { ?>
                <a href="perfil.php">Meu perfil</a>
                <a href="historico.php">Minhas compras</a>

                <?php if (($_SESSION['usuario_tipo'] ?? '') == 'admin') { ?>
                    <a href="admin.php">Painel Admin</a>
                <?php } ?>

                <a href="logout.php">Sair</a>
            <?php } else { ?>
                <a href="login.php">Entrar</a>
                <a href="cadastro.php">Criar conta</a>
            <?php } ?>

            <a href="carrinho.php">🛒 Carrinho</a>
        </nav>
    </div>
</header>

<main class="container">
    <section class="inicio">
        <h2>Bem-vindo à GameStore!</h2>
        <p>Os melhores produtos gamers você encontra aqui.</p>
    </section>

    <?php if ($mensagem != '') { ?>
        <p class="mensagem"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php } ?>

    <section>
        <h2>Nossos Produtos</h2>

        <form method="GET" action="index.php" class="filtro">
            <input
                type="text"
                name="busca"
                placeholder="Pesquisar produto pelo nome"
                value="<?php echo htmlspecialchars($busca); ?>"
            >

            <select name="categoria">
                <option value="0">Todas as categorias</option>
                <?php foreach ($categorias as $categoria) { ?>
                    <option
                        value="<?php echo $categoria['id']; ?>"
                        <?php if ($categoriaSelecionada == $categoria['id']) echo 'selected'; ?>
                    >
                        <?php echo htmlspecialchars($categoria['nome']); ?>
                    </option>
                <?php } ?>
            </select>

            <button type="submit">Pesquisar</button>
            <a class="btn" href="index.php">Limpar</a>
        </form>

        <div class="produtos">
            <?php if (count($produtos) > 0) { ?>
                <?php foreach ($produtos as $produto) { ?>
                    <div class="produto">
                        <img
                            src="imagens/<?php echo htmlspecialchars($produto['imagem'] ?? ''); ?>"
                            alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                        >

                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

                        <p><?php echo htmlspecialchars($produto['descricao'] ?? ''); ?></p>

                        <p>
                            Categoria:
                            <?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Sem categoria'); ?>
                        </p>

                        <p class="preco">
                            R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                        </p>

                        <p>Estoque: <?php echo (int)$produto['estoque']; ?></p>

                        <a href="detalhes.php?id=<?php echo (int)$produto['id']; ?>" class="btn">
                            Ver produto
                        </a>

                        <?php if ($produto['estoque'] > 0) { ?>
                            <form action="adicionar_carrinho.php" method="POST">
                                <input type="hidden" name="id" value="<?php echo (int)$produto['id']; ?>">
                                <button type="submit">Adicionar ao carrinho</button>
                            </form>
                        <?php } else { ?>
                            <p>Produto indisponível</p>
                        <?php } ?>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Nenhum produto encontrado.</p>
            <?php } ?>
        </div>
    </section>
</main>

<footer>
    <p>GameStore © 2026 - Todos os direitos reservados.</p>
</footer>

</body>
</html>
