<?php
session_start();
require_once 'config/conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php?voltar=carrinho');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_SESSION['carrinho'])) {
    header('Location: carrinho.php');
    exit;
}

$formaPagamento = $_POST['forma_pagamento'] ?? '';

if (!in_array($formaPagamento, ['Pix', 'Boleto', 'Cartão'], true)) {
    $_SESSION['mensagem'] = 'Selecione uma forma de pagamento válida.';
    header('Location: carrinho.php');
    exit;
}

try {
    $pdo->beginTransaction();

    $produtos = [];
    $total = 0;

    foreach ($_SESSION['carrinho'] as $id => $quantidade) {
        $id = (int)$id;
        $quantidade = (int)$quantidade;

        if ($id <= 0 || $quantidade <= 0) {
            throw new Exception('Existe um produto com quantidade inválida no carrinho.');
        }

        $sql = "SELECT * FROM produtos WHERE id = ? FOR UPDATE";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$produto || $produto['estoque'] < $quantidade) {
            throw new Exception('Estoque insuficiente para um dos produtos.');
        }

        $produtos[] = [
            'id' => $id,
            'quantidade' => $quantidade,
            'preco' => $produto['preco']
        ];

        $total += $produto['preco'] * $quantidade;
    }

    $sql = "INSERT INTO pedidos (usuario_id, data_pedido, forma_pagamento, total)
            VALUES (?, NOW(), ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$_SESSION['usuario_id'], $formaPagamento, $total]);

    $pedidoId = $pdo->lastInsertId();

    foreach ($produtos as $produto) {
        $sql = "INSERT INTO itens_pedido
                (pedido_id, produto_id, quantidade, preco_unitario)
                VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $pedidoId,
            $produto['id'],
            $produto['quantidade'],
            $produto['preco']
        ]);

        $sql = "UPDATE produtos SET estoque = estoque - ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$produto['quantidade'], $produto['id']]);
    }

    $pdo->commit();

    unset($_SESSION['carrinho']);
    header('Location: pedido_sucesso.php');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['mensagem'] = $e->getMessage();
    header('Location: carrinho.php');
    exit;
}
?>
