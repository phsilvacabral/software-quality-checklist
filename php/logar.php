<?php 

include('connection.php');
session_start();

// Verifica se o usuário está logado
if (isset($_SESSION['Cod_Usuario'])) {
    header('Location: ../');
}

// Verifica se os dados estão preenchidos
if(isset($_POST['email']) && isset($_POST['senha'])) {
    // Verifica se o e-mail e a senha estão corretos
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $sql = "SELECT * FROM Usuario WHERE Email = '$email' AND Senha = '$senha'";
    $resultado = mysqli_query($conn, $sql);
    $resultadoArray = mysqli_fetch_assoc($resultado);

    if (mysqli_num_rows($resultado) > 0) {
        // Se o e-mail e a senha estão corretos, cria uma nova sessão
        $_SESSION['Cod_Usuario'] = $resultadoArray['Cod_Usuario'];
        echo "ok";
    } else {
        // Exibe uma mensagem de erro se o e-mail e a senha estão incorretos
        echo "E-mail ou senha incorretos";
    }
} else {
    // Exibe uma mensagem de erro se os dados não estão preenchidos
    echo "Por favor, preencha todos os campos.";
}

?>