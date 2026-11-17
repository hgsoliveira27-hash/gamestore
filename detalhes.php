
<?php

require_once 'config/conexao.php';

if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $sql = "SELECT * FROM produtos WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

} else {
    $produto = false;
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detalhes do Produto - GameStore</title>

    <link rel="stylesheet" href="css/style.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <header>
        <div class="container">
            <h1>GameStore 🎮</h1>
            <p>Detalhes do produto</p>
        </div>
    </header>

    <main class="container mt-5">

        <?php if ($produto) { ?>

            <div class="produto">

                <img src="imagens/<?php echo htmlspecialchars($produto['imagem']); ?>"
                     alt="<?php echo htmlspecialchars($produto['nome']); ?>">

                <h2>
                    <?php echo htmlspecialchars($produto['nome']); ?>
                </h2>

                <p>
                    <?php echo htmlspecialchars($produto['descricao']); ?>
                </p>

                <p class="preco">
                    R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
                </p>

                <p>
                    Estoque: <?php echo $produto['estoque']; ?>
                </p>

                <form action="adicionar_carrinho.php" method="POST">

<input type="hidden" name="id"
       value="<?php echo $produto['id']; ?>">

<button type="submit">
    Adicionar ao carrinho 🛒
</button>

</form>

<br>

<a href="carrinho.php">Ver meu carrinho</a>

<br><br>

                <a href="index.php" class="btn btn-success">
                    Voltar para a loja
                </a>

            </div>

        <?php } else { ?>

            <h2>Produto não encontrado!</h2>

            <a href="index.php">Voltar para a loja</a>

        <?php } ?>

    </main>

</body>
</html>
