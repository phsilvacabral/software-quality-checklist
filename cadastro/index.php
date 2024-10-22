<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="../img/icon.ico" type="image/x-icon">
    <title>Cadastro</title>
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
        <div id="cadastro-frame">
            <h1>Cadastro</h1>

            <input type="text" id="nome" name="nome" placeholder="Nome">
            <input type="text" id="email" name="email" placeholder="E-mail">
            <input type="password" id="senha" name="senha" placeholder="Senha">
            <input type="password" id="senha2" name="senha2" placeholder="Confirmar senha">

            <div id="div-tipo-usuario">
                <p>Tipo de usuário:</p>
                <label for="tipo"><input type="radio" class="tipo" name="tipo" value="1">
                Admin</label>
                <label for="tipo"><input type="radio" class="tipo" name="tipo" value="2">Auditor</label>
            </div>

            <p id="cadastro-message">resp</p>

            <a href="../login/" id="link-login">Login</a>

            <button id="cadastro-button">Criar conta</button>
        </div>
    </main>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="script.js"></script>
</body>

</html>