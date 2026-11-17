
<?php

session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$sql = "SELECT * FROM pedidos
        WHERE usuario_id = ?
        ORDER BY data_pedido DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$_SESSION['usuario_id']]);

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <header>
        <div class="container">
            <h1>GameStore 🎮</h1>
            <p>Meu histórico de compras</p>
        </div>
    </header>

    <main class="container">

        <h2 class="mt-4">Minhas compras</h2>

        <?php if (count($pedidos) > 0) { ?>

            <?php foreach ($pedidos as $pedido) { ?>

                <div class="produto">

                    <h3>Pedido #<?php echo $pedido['id']; ?></h3>

                    <p>
                        Data:
                        <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                    </p>

                    <p>
                        Pagamento:
                        <?php echo htmlspecialchars($pedido['forma_pagamento']); ?>
                    </p>

                    <p class="preco">
                        Total:
                        R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
                    </p>

                    <h4>Produtos comprados:</h4>

                    <?php

                    $sqlItens = "SELECT itens_pedido.*, produtos.nome
                                 FROM itens_pedido
                                 INNER JOIN produtos
                                 ON itens_pedido.produto_id = produtos.id
                                 WHERE itens_pedido.pedido_id = ?";

                    $stmtItens = $pdo->prepare($sqlItens);
                    $stmtItens->execute([$pedido['id']]);

                    $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

                    ?>

                    <?php foreach ($itens as $item) { ?>

                        <p>
                            <?php echo htmlspecialchars($item['nome']); ?>
                            - Quantidade: <?php echo $item['quantidade']; ?>
                        </p>

                    <?php } ?>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>Você ainda não realizou nenhuma compra.</p>

        <?php } ?>

        <br>

        <a href="index.php">Voltar para a loja</a>

    </main>

</body>
</html>
