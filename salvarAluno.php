<?php 
//1. Incluir Arquivo de Conexão
include "conexao.php";
//O código começa incluindo o arquivo conexao.php, que provavelmente contém a função ou classe responsável por conectar ao banco de dados.
//2. Estabelecer Conexão
$conn = Conexao::getConn();
//Aqui, uma conexão com o banco de dados é estabelecida através do método getConn() da classe Conexao, que vem do arquivo conexao.php.
//3. Verificar o Método de Requisição (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
//Esta linha verifica se o método da requisição HTTP é POST. A ação de cadastro só será executada se a requisição for do tipo POST, o que significa que os dados foram enviados via um formulário HTML.
//4. Receber Dados do Formulário
$nome = $_POST['nome'];
$email = $_POST['email'];
//As variáveis $nome e $email recebem os dados que foram enviados pelo formulário via POST. Esses dados são inseridos pelos usuários no campo "nome" e "email".
//5. Bloco Try-Catch para Tratar Exceções
//O código usa o bloco try-catch para tratar possíveis erros que possam ocorrer ao longo do processo de cadastro.
try {
//Inicia o bloco de tratamento de exceções. Se algum erro ocorrer, ele será capturado nas seções catch.
//6. Verificação se o Aluno Já Existe no Banco de Dados
$stmt = $conn->prepare("SELECT id FROM alunos WHERE nome = ? OR email = ?");
//Prepara uma consulta SQL para verificar se já existe algum aluno no banco de dados com o mesmo nome ou e-mail.
$stmt->bind_param("ss", $nome, $email);
//Esta linha "liga" os parâmetros da consulta SQL (nome e email) aos valores das variáveis $nome e $email, prevenindo ataques de SQL Injection.
$stmt->execute();
$stmt->store_result();
//Executa a consulta e armazena o resultado na variável $stmt.
if ($stmt->num_rows > 0) {
    echo "<div class='message error'>Já existe um aluno cadastrado com o nome ou email informado. Verifique os dados e tente novamente.</div>";
    $stmt->close();
    exit();
}
//Se a consulta retornar um ou mais registros (num_rows > 0), significa que já existe um aluno com o nome ou e-mail fornecido. Neste caso, exibe uma mensagem de erro e encerra a execução com exit().
//7. Inserção dos Dados no Banco de Dados
//Se o aluno ainda não existe, o código segue para a parte de inserção:

$stmt->close();
$sql = "INSERT INTO alunos (nome, email) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
//Fecha o statement anterior ($stmt->close()) e prepara uma nova consulta SQL para inserir os dados do aluno na tabela alunos.
$stmt->bind_param("ss", $nome, $email);
//Novamente, os valores de $nome e $email são ligados aos parâmetros da consulta de inserção, evitando ataques de SQL Injection.

if (!$stmt->execute()) {
    throw new mysqli_sql_exception($stmt->error, $stmt->errno);
}
//Aqui, o código executa a consulta de inserção. Se a execução falhar (!$stmt->execute()), uma exceção específica para mysqli (mysqli_sql_exception) é lançada com uma mensagem de erro.
//8. Exibição de Mensagem de Sucesso
echo "<div class='message success'>Cadastro realizado com sucesso!</div>";
//Se a inserção no banco de dados for bem-sucedida, uma mensagem de sucesso é exibida ao usuário.
//9. Tratamento de Erros (Catch)
//Se alguma exceção for lançada dentro do bloco try, ela será capturada em um dos blocos catch.

} catch (mysqli_sql_exception $e) {
    echo "<div class='message error'>Erro ao realizar o cadastro: " . $e->getMessage() . "</div>";
} catch (Exception $e) {
    echo "<div class='message error'>Erro ao processar o cadastro: " . $e->getMessage() . "</div>";
}
//Caso haja um erro na execução da query, uma mensagem específica com o erro é exibida. O código captura tanto as exceções de SQL (mysqli_sql_exception) quanto exceções genéricas (Exception).
//10. Fechar Statement e Conexão
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
//Aqui, o código fecha o statement ($stmt) e a conexão com o banco de dados ($conn) para liberar os recursos utilizados.
//11. Caso o Método Não Seja POST
} else {
    echo "Método de requisição inválido.";
}
//Se a requisição não for do tipo POST (por exemplo, GET), exibe uma mensagem de erro, indicando que o método é inválido.
?>;