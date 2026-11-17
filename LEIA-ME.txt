ATUALIZACOES DA GAMESTORE

IMPORTANTE:
1. Faça uma cópia da pasta atual gamestore antes de substituir arquivos.
2. NÃO substitua nem apague o arquivo .env que já existe no projeto.
3. NÃO importe database/schema.sql no banco que já está funcionando. Esse arquivo é para criar a estrutura em uma instalação nova. Seu banco atual já tem tabelas e dados.
4. O arquivo gamestore.sql do seu backup original deve continuar guardado em local privado. Ele contém dados de contas cadastradas. Não publique esse backup no GitHub.

ARQUIVOS PARA SUBSTITUIR:
- index.php
- carrinho.php
- finalizar_pedido.php
- adicionar_produto.php
- editar_produto.php
- produtos_admin.php
- css/style.css

ARQUIVOS NOVOS:
- .env.example
- .gitignore
- database/schema.sql

COMO INSTALAR:
1. Extraia este ZIP.
2. Copie os arquivos e pastas para C:\xampp\htdocs\gamestore.
3. Quando o Windows perguntar se deseja substituir os arquivos, substitua somente os arquivos listados acima.
4. Mantenha o seu config/conexao.php e o seu .env atuais.
5. Inicie Apache e MySQL no XAMPP.
6. Abra http://localhost/gamestore.

MUDANCAS:
- Busca por nome e filtro por categoria na página inicial.
- Exibição da categoria de cada produto.
- Cadastro e edição de produtos com escolha de categoria.
- Alteração de quantidade e remoção no carrinho.
- Atualização dos subtotais e total.
- Opções simuladas de pagamento: PIX, Boleto e Cartão.
- Validação de estoque na finalização da compra.
- Arquivo schema.sql com a estrutura do banco.
- .env.example e .gitignore para preparar o GitHub.

PAGAMENTO:
As opções PIX, Boleto e Cartão são apenas uma simulação escolar. Não existe integração com banco, operadora de cartão ou emissão real de boleto.
