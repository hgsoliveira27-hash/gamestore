
<?php

session_start();
require_once 'config/conexao.php';

// Verificar se recebeu um ID
if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['id'])) {
    header('Location: index.php');
    exit;
}

$id = filter_var($_POST['id'], FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Buscar produto no banco
$sql = "SELECT * FROM produtos WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto || $produto['estoque'] <= 0) {
    $_SESSION['mensagem'] = 'Produto indisponível.';
    header('Location: index.php');
    exit;
}

// Criar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// Verificar quantidade
$quantidade = $_SESSION['carrinho'][$id] ?? 0;

if ($quantidade < $produto['estoque']) {

    $_SESSION['carrinho'][$id] = $quantidade + 1;

    $_SESSION['mensagem'] = 'Produto adicionado ao carrinho!';

} else {

    $_SESSION['mensagem'] = 'Quantidade máxima em estoque atingida.';

}

header('Location: carrinho.php');
exit;

?>
