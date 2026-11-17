
<?php

session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] != 'admin') {
    header('Location: login.php');
    exit;
}

$sql = "SELECT pedidos.*, usuarios.nome
        FROM pedidos
        INNER JOIN usuarios
        ON pedidos.usuario_id = usuarios.id
        ORDER BY pedidos.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Pedidos - GameStore</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>GameStore 🎮</h1>
    <h2>Pedidos dos Clientes</h2>
</header>

<main class="container">

    <h2>Todos os pedidos</h2>

    <a href="admin.php">Voltar ao painel</a>

    <br><br>

    <?php if (count($pedidos) > 0) { ?>

        <?php foreach ($pedidos as $pedido) { ?>

            <div class="produto">

                <h3>Pedido #<?php echo $pedido['id']; ?></h3>

                <p>
                    Cliente:
                    <?php echo htmlspecialchars($pedido['nome']); ?>
                </p>

                <p>
                    Data:
                    <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                </p>

                <p>
                    Pagamento:
                    <?php echo htmlspecialchars($pedido['forma_pagamento']); ?>
                </p>

                <p>
                    Total:
                    R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
                </p>

                <h4>Produtos:</h4>

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

        <p>Nenhum pedido encontrado.</p>

    <?php } ?>

</main>

</body>
</html>
