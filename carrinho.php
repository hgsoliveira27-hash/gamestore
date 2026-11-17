<?php
session_start();
require_once 'config/conexao.php';

// Atualizar ou remover um produto do carrinho
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['remover'])) {
        $id = filter_var($_POST['remover'], FILTER_VALIDATE_INT);

        if ($id && isset($_SESSION['carrinho'][$id])) {
            unset($_SESSION['carrinho'][$id]);
            $_SESSION['mensagem'] = 'Produto removido do carrinho.';
        }

        header('Location: carrinho.php');
        exit;
    }

    if (isset($_POST['atualizar_id'])) {
        $id = filter_var($_POST['atualizar_id'], FILTER_VALIDATE_INT);
        $quantidade = filter_var($_POST['quantidade'], FILTER_VALIDATE_INT);

        if ($id && $quantidade && $quantidade > 0) {
            $sql = "SELECT estoque FROM produtos WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($produto && $produto['estoque'] > 0) {
                if ($quantidade > $produto['estoque']) {
                    $quantidade = (int)$produto['estoque'];
                    $_SESSION['mensagem'] = 'A quantidade foi ajustada ao estoque disponível.';
                } else {
                    $_SESSION['mensagem'] = 'Quantidade atualizada!';
                }

                $_SESSION['carrinho'][$id] = $quantidade;
            } else {
                unset($_SESSION['carrinho'][$id]);
                $_SESSION['mensagem'] = 'Produto sem estoque e removido do carrinho.';
            }
        } else {
            $_SESSION['mensagem'] = 'Digite uma quantidade válida.';
        }

        header('Location: carrinho.php');
        exit;
    }
}

$carrinho = $_SESSION['carrinho'] ?? [];
$total = 0;
$produtos = [];

foreach ($carrinho as $id => $quantidade) {
    $sql = "SELECT * FROM produtos WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([(int)$id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($produto) {
        $produto['quantidade'] = (int)$quantidade;
        $produtos[] = $produto;
        $total += $produto['preco'] * $quantidade;
    } else {
        unset($_SESSION['carrinho'][$id]);
    }
}

$mensagem = $_SESSION['mensagem'] ?? '';
unset($_SESSION['mensagem']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="container">
        <h1>Meu Carrinho 🛒</h1>
        <nav><a href="index.php">Continuar comprando</a></nav>
    </div>
</header>

<main class="container">
    <h2>Produtos adicionados</h2>

    <?php if ($mensagem != '') { ?>
        <p class="mensagem"><?php echo htmlspecialchars($mensagem); ?></p>
    <?php } ?>

    <?php if (count($produtos) > 0) { ?>
        <?php foreach ($produtos as $produto) { ?>
            <div class="produto">
                <img
                    src="imagens/<?php echo htmlspecialchars($produto['imagem'] ?? ''); ?>"
                    alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                >

                <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>

                <p>
                    Preço unitário:
                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                </p>

                <p>Estoque disponível: <?php echo (int)$produto['estoque']; ?></p>

                <p>
                    Subtotal:
                    R$ <?php echo number_format($produto['preco'] * $produto['quantidade'], 2, ',', '.'); ?>
                </p>

                <form method="POST" class="form-quantidade">
                    <label>Quantidade:</label>
                    <input
                        type="number"
                        name="quantidade"
                        min="1"
                        max="<?php echo (int)$produto['estoque']; ?>"
                        value="<?php echo (int)$produto['quantidade']; ?>"
                        required
                    >
                    <button type="submit" name="atualizar_id" value="<?php echo (int)$produto['id']; ?>">
                        Atualizar quantidade
                    </button>
                    <button type="submit" name="remover" value="<?php echo (int)$produto['id']; ?>" class="botao-remover">
                        Remover
                    </button>
                </form>
            </div>
        <?php } ?>

        <h2 class="preco">
            Total: R$ <?php echo number_format($total, 2, ',', '.'); ?>
        </h2>

        <?php if (isset($_SESSION['usuario_id'])) { ?>
            <h3>Forma de pagamento</h3>
            <form action="finalizar_pedido.php" method="POST">
                <select name="forma_pagamento" required>
                    <option value="">Selecione uma forma de pagamento</option>
                    <option value="Pix">PIX</option>
                    <option value="Boleto">Boleto</option>
                    <option value="Cartão">Cartão</option>
                </select>

                <p class="aviso">
                    Esta é uma simulação de pagamento para o projeto escolar.
                    Não será realizada uma cobrança real.
                </p>

                <button type="submit">Finalizar compra</button>
            </form>
        <?php } else { ?>
            <p>Faça login para finalizar sua compra.</p>
            <a href="login.php?voltar=carrinho" class="btn">Entrar na conta</a>
        <?php } ?>

    <?php } else { ?>
        <p>Seu carrinho está vazio!</p>
    <?php } ?>
</main>

</body>
</html>
