<?php
include('connection.php');

// Recebe os dados via POST
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$tipo = $_POST['tipo'];

// Verifica se os dados foram preenchidos
if (empty($nome) || empty($email) || empty($senha) || empty($tipo)) {
    echo 'Preencha todos os campos.';
    exit();
}

// Verifica se o e-mail já está cadastrado
$sqlCheck = "SELECT * FROM Usuario WHERE Email = '$email'";
$resultCheck = mysqli_query($conn, $sqlCheck);

if (mysqli_num_rows($resultCheck) > 0) {
    echo 'E-mail já cadastrado.';
    exit();
}


// Insere o novo usuário no banco de dados
$sqlInsert = "INSERT INTO Usuario (Nome, Email, Senha, Tipo) VALUES ('$nome', '$email', '$senha', '$tipo')";
if (mysqli_query($conn, $sqlInsert)) {
    echo 'ok';
} else {
    echo 'Erro ao cadastrar: ' . mysqli_error($conn);
}
?>