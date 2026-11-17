<?php

$env = parse_ini_file(__DIR__ . '/../.env');

$host = $env['DB_HOST'];
$banco = $env['DB_NAME'];
$usuario = $env['DB_USER'];
$senha = $env['DB_PASS'];

try {

    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8mb4",
        $usuario,
        $senha
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {

    error_log($e->getMessage());
die("Erro ao conectar ao banco de dados.");

}
?>