<?php 

include "conexao.php";
$conn = Conexao::getConn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {


$nome = $_POST['nome'];
$email = $_POST['email'];


try {


$stmt = $conn->prepare("SELECT id FROM alunos WHERE nome = ? OR email = ?");

$stmt->bind_param("ss", $nome, $email);

$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<div class='message error'>Já existe um aluno cadastrado com o nome ou email informado. Verifique os dados e tente novamente.</div>";
    $stmt->close();
    exit();
}


$stmt->close();
$sql = "INSERT INTO alunos (nome, email) VALUES (?, ?)";
$stmt = $conn->prepare($sql);

$stmt->bind_param("ss", $nome, $email);


if (!$stmt->execute()) {
    throw new mysqli_sql_exception($stmt->error, $stmt->errno);
}

echo "<div class='message success'>Cadastro realizado com sucesso!</div>";

} catch (mysqli_sql_exception $e) {
    echo "<div class='message error'>Erro ao realizar o cadastro: " . $e->getMessage() . "</div>";
} catch (Exception $e) {
    echo "<div class='message error'>Erro ao processar o cadastro: " . $e->getMessage() . "</div>";
}


if (isset($stmt)) {
    $stmt->close();
}
$conn->close();


} else {
    echo "Método de requisição inválido.";
}

