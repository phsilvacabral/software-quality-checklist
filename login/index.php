<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="../img/icon.ico" type="image/x-icon">
    <title>Login</title>
</head>

<body>
    <?php
    include('../php/connection.php');
    session_start();

    if (isset($_SESSION['Cod_Usuario'])) {
        header('Location: ../');
    }
    ?>

    <main>
        <div id="login-frame">
            <h1>Login</h1>

            <input type="text" id="email" name="email" placeholder="E-mail">
            <input type="password" id="senha" name="senha" placeholder="Senha">

            <p id="login-message">resp</p>

            <a href="../cadastro/" id="link-cadastro">Criar conta</a>

            <button id="loginButton">Login</button>
        </div>
    </main>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="script.js"></script>
</body>

</html>