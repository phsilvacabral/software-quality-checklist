<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="shortcut icon" href="img/icon.ico" type="image/x-icon">
    <title>Home</title>
</head>

<body>
    <?php
    include('php/connection.php');
    session_start();

    if (!isset($_SESSION['Cod_Usuario'])) {
        header('Location: login/');
    }

    $sql = "SELECT * FROM Usuario WHERE Cod_Usuario = '" . $_SESSION['Cod_Usuario'] . "'";
    $resultado = mysqli_query($conn, $sql);
    $resultadoArray = mysqli_fetch_assoc($resultado);

    $nome = $resultadoArray['Nome'];
    $tipoUsuario = $resultadoArray['Tipo'];
    ?>

    <main>
        <div id="nav">
            <div id="conta">
                <p id="conta-name" data-id-logado="<?php echo $_SESSION['Cod_Usuario']; ?>"><?php echo $nome; ?></p>
                <div id="sair">Sair</div>
            </div>

            <?php if ($tipoUsuario == '1') { ?>
                <div id="new-checklist">
                    <p id="mais-icon">+</p>
                    <p id="novo-checklist">Novo checklist</p>
                </div> <?php }; ?>
        </div>

        <h1>Checklists criados</h1>

        <div id="checklists">
            <?php
            // Consulta para pegar todas as checklists e o nome do criador
            $sql = "SELECT c.*, u.Nome AS CriadorNome 
            FROM Checklist c 
            INNER JOIN Usuario u ON c.Criador = u.Cod_Usuario";
            $resultado = mysqli_query($conn, $sql);

            while ($querry = mysqli_fetch_assoc($resultado)) {
                // Formatar a data de modificação
                $dtModificacao = new DateTime($querry['Dt_Modificacao']);
                $formattedDate = $dtModificacao->format('g:i a, d \d\e F \d\e Y');
                $checklistId = $querry['Cod_Checklist'];

                // Consultas para contar as não conformidades
                $sqlTotal = "SELECT COUNT(*) AS total_itens FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId";
                $sqlConformes = "SELECT COUNT(*) AS total_conformes FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Conforme = 'CC'";
                $sqlNaoConformes = "SELECT COUNT(*) AS total_nao_conformes FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Conforme = 'NC'";
                $sqlNaoAplicaveis = "SELECT COUNT(*) AS total_nao_aplicaveis FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Conforme = 'NA'";

                $resultTotal = mysqli_query($conn, $sqlTotal);
                $resultConformes = mysqli_query($conn, $sqlConformes);
                $resultNaoConformes = mysqli_query($conn, $sqlNaoConformes);
                $resultNaoAplicaveis = mysqli_query($conn, $sqlNaoAplicaveis);

                $totalItens = mysqli_fetch_assoc($resultTotal)['total_itens'];
                $totalConformes = mysqli_fetch_assoc($resultConformes)['total_conformes'];
                $totalNaoConformes = mysqli_fetch_assoc($resultNaoConformes)['total_nao_conformes'];
                $totalNaoAplicaveis = mysqli_fetch_assoc($resultNaoAplicaveis)['total_nao_aplicaveis'];

                // Calcular a aderência
                $totalConsiderados = $totalItens - $totalNaoAplicaveis;
                $aderencia = $totalConsiderados > 0 ? ($totalConformes / $totalConsiderados) * 100 : 0;
                $aderencia = number_format($aderencia, 0);
            ?>

                <div id="" class="checklist" onclick="link(<?php echo $querry['Cod_Checklist']; ?>)">
                    <div id="name-description">
                        <h2><?php echo $querry['Titulo']; ?></h2>
                        <p><?php echo $querry['Descricao']; ?></p>
                    </div>

                    <div id="dt-modification">
                        <p>Criado por: <?php echo $querry['CriadorNome']; ?></p>
                        <p>Última modificação: <?php echo $formattedDate; ?></p>
                        <p>Aderência: <?php echo $aderencia; ?>%</p>
                    </div>
                </div>

            <?php } ?>

            <script>
                function link(id) {
                    window.location.href = "checklist.php?id=" + id;
                }


                document.getElementById("sair").addEventListener("click", function() {
                    window.location.href = "php/deslogar.php";
                });
            </script>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="script.js"></script>

        </div>
    </main>


    <div id="popup-new-checklist" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <h2>Criar novo checklist</h2>
            <input type="text" id="titulo-check" placeholder="Título do checklist">
            <input type="text" name="descricao-check" id="descricao-check" placeholder="Descrição do checklist">
            <div>
                <input type="button" value="Cancelar" id="botao-cancelar-popup">
                <input type="button" value="Criar checklist" id="botao-criar-popup">
            </div>
        </div>
    </div>
</body>


</html>