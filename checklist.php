<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php
    include('php/connection.php');
    session_start();

    if (!isset($_SESSION['Cod_Usuario'])) {
        header('Location: login/');
    }
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleChecklist.css">
    <link rel="shortcut icon" href="img/icon.ico" type="image/x-icon">
    <?php
    $checklistId = $_GET['id'];
    $sql = "SELECT * FROM Checklist WHERE Cod_Checklist = '" . $checklistId . "'";
    $querry1 = mysqli_query($conn, $sql);
    $resultado = mysqli_fetch_assoc($querry1);
    $titulo = $resultado['Titulo'];
    $descricao = $resultado['Descricao'];
    ?>
    <title><?php echo $titulo; ?></title>
</head>


<?php
// Select para pegar o tipo de usuário
$codUsuario = $_SESSION['Cod_Usuario'];
$codChecklist = $_GET['id'];
$sql = "SELECT Tipo FROM Usuario WHERE Cod_Usuario = '$codUsuario'";
$resultado = mysqli_query($conn, $sql);
$usuario = mysqli_fetch_assoc($resultado);
$tipoUsuario = $usuario['Tipo'];

// Consulta para pegar todas as checklists e o nome do criador
$sql = "SELECT c.*, u.Nome AS CriadorNome 
        FROM Checklist c 
        INNER JOIN Usuario u ON c.Criador = u.Cod_Usuario";
$resultado = mysqli_query($conn, $sql);

$querry = mysqli_fetch_assoc($resultado);

// Formatar a data de modificação
$dtModificacao = new DateTime($querry['Dt_Modificacao']);
$formattedDate = $dtModificacao->format('g:i a, d \d\e F \d\e Y');

// Consulta para calcular a aderência dos itens (percentual de itens 'Conforme')
$sqlItens = "SELECT COUNT(*) AS total, 
            SUM(CASE WHEN i.Conforme = 'CC' THEN 1 ELSE 0 END) AS conformes 
            FROM ItemChecklist i 
            WHERE i.fk_Cod_Checklist = " . $querry['Cod_Checklist'];
$resultadoItens = mysqli_query($conn, $sqlItens);
$dadosItens = mysqli_fetch_assoc($resultadoItens);

$totalItens = $dadosItens['total'];
$itensConformes = $dadosItens['conformes'];
$aderencia = $totalItens > 0 ? round(($itensConformes / $totalItens) * 100) : 0;


$versao = isset($_GET['versao']) ? $_GET['versao'] : 0;
if ($tipoUsuario == '2') {
    // Buscar o maior valor de escalonamento na tabela ItemChecklist
    $sqlMaxEscalonamento = "SELECT MAX(CAST(Escalonamento AS UNSIGNED)) AS max_escalonamento FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Conforme = 'NC'";
    $resultMax = mysqli_query($conn, $sqlMaxEscalonamento);
    $rowMax = mysqli_fetch_assoc($resultMax);
    $maxEscalonamento = $rowMax['max_escalonamento'];
}
?>


<body>
    <main>
        <nav id="nav">
            <section id="dados-checklist">
                <div>
                    <h1 id="titulo"><?php echo $titulo; ?></h1>
                    <div id="descricao"><?php echo $descricao; ?></div>
                    <div id="checklist-container" data-cod-checklist="<?php echo $codChecklist; ?>"></div>
                </div>

                <div id="dados-complementares">
                    <div>
                        <p id="criador">Criado por: <?php echo $querry['CriadorNome']; ?></p>
                        <p id="modificacao">Última modificação: <?php echo $formattedDate; ?></p>
                    </div>
                    <?php if ($tipoUsuario == '2') { ?><div id="botao-salvar-checklist">Salvar</div><?php }; ?>
                </div>
            </section>

            <section id="dashboards">
                <?php
                if ($tipoUsuario == '2') {
                    $abrir_check_nc = isset($_POST['abrir_check_nc']) ? true : false;
                    $_SESSION['abrir_check_nc'] = $abrir_check_nc;

                    // Verificar se há não conformidades na tabela ItemChecklist
                    $sqlCheckNc = "SELECT COUNT(*) AS total_nc FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Conforme = 'NC'";
                    $resultNc = mysqli_query($conn, $sqlCheckNc);
                    $rowNc = mysqli_fetch_assoc($resultNc);
                    $totalNc = $rowNc['total_nc'];
                }
                ?>

                <?php if ($versao == 0 && $tipoUsuario == '2') { ?>
                    <div id="checklist-nc" class="dashboards">
                        <h2>Checklist NC</h2>
                        <p>Não conformes: <?php echo $totalNc; ?></p>
                        <div id="abrir-nc" data-checklist="<?php echo $checklistId; ?>" data-versao="<?php echo $maxEscalonamento; ?> ">Abrir</div>
                    </div><?php }; ?>

                <?php if ($tipoUsuario == '1') { ?>
                    <div id="gerencia" class="dashboards">
                        <div id="botao-editar">Editar checklist</div>
                        <div id="botao-excluir">Excluir checklist</div>
                        <div id="botao-salvar">Salvar e voltar</div>
                    </div> <?php }; ?>


                <?php if ($tipoUsuario == '2' && $versao > 0) {
                    // Verifica se existe um valor máximo válido
                    if ($maxEscalonamento) { ?>
                        <div id="versao-nc" class="dashboards">
                            <h2>Versão de escalonamento</h2>
                            <select name="versao-escalonamento-nc" id="select-versao-escalonamento" data-checklist="<?php echo $checklistId; ?>">
                                <?php
                                // Loop para gerar as opções de 1 até o maior valor de escalonamento
                                for ($i = $maxEscalonamento; $i >= 1; $i--) {
                                    $selected = ($i == $versao) ? 'selected' : '';
                                    echo "<option value=\"$i\" $selected>Versão $i</option>";
                                }
                                ?>
                            </select>
                        </div>
                <?php }
                } ?>


                <?php
                // Consultas para contar as não conformidades
                $sqlAlta = "SELECT COUNT(*) AS total_alta FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Complexidade LIKE '%Alta%' AND Conforme = 'NC'";
                $sqlMedia = "SELECT COUNT(*) AS total_media FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Complexidade LIKE '%Média%' AND Conforme = 'NC'";
                $sqlBaixa = "SELECT COUNT(*) AS total_baixa FROM ItemChecklist WHERE fk_Cod_Checklist = $checklistId AND Complexidade LIKE '%Baixa%' AND Conforme = 'NC'";

                $resultAlta = mysqli_query($conn, $sqlAlta);
                $resultMedia = mysqli_query($conn, $sqlMedia);
                $resultBaixa = mysqli_query($conn, $sqlBaixa);

                $totalAlta = mysqli_fetch_assoc($resultAlta)['total_alta'];
                $totalMedia = mysqli_fetch_assoc($resultMedia)['total_media'];
                $totalBaixa = mysqli_fetch_assoc($resultBaixa)['total_baixa'];

                // Calcular o total de não conformidades
                $totalNc = $totalAlta + $totalMedia + $totalBaixa;
                ?>
                <div id="classificacao-nc" class="dashboards">
                    <div id="titulo-classificacao">
                        <h2>Nº NC</h2>
                    </div>
                    <div id="class-dias">
                        <p>Alta - 5 dias</p>
                        <p>Média - 3 dias</p>
                        <p>Baixa - 1 dia</p>
                        <p>Total</p>
                    </div>
                    <div id="class-qtd">
                        <p><?php echo $totalAlta; ?></p>
                        <p><?php echo $totalMedia; ?></p>
                        <p><?php echo $totalBaixa; ?></p>
                        <p><?php echo $totalNc; ?></p>
                    </div>
                </div>


                <?php
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
                <div id="resumo" class="dashboards">
                    <div id="titulo-resumo">
                        <h2>Resumo</h2>
                    </div>
                    <div id="resumo-itens">
                        <p>Total de itens</p>
                        <p>Conformes</p>
                        <p>Não conformes</p>
                        <p>Não aplicável</p>
                        <p>Aderência</p>
                    </div>
                    <div id="resumo-qtd">
                        <p><?php echo $totalItens; ?></p>
                        <p><?php echo $totalConformes; ?></p>
                        <p><?php echo $totalNaoConformes; ?></p>
                        <p><?php echo $totalNaoAplicaveis; ?></p>
                        <p><?php echo $aderencia; ?>%</p>
                    </div>
                </div>
            </section>
        </nav>

        <section id="tabela">
            <div id="header">
                <div id="id-h">ID</div>
                <div id="name-h">Nome</div>
                <div id="escalonamento-h">Escalonamento</div>
                <div id="complexidade-h">Complexidade</div>
                <div id="responsavel-h">Responsável</div>
                <?php if ($tipoUsuario != '1') { ?><div id="conformidade-h">Conformidade</div> <?php }; ?>
            </div>

            <div id="checklists">

                <!-- criar checklist como admin -->
                <?php
                if ($tipoUsuario == '1') {
                    // Buscar todos os usuários para preencher o select
                    $sqlUsuarios = "SELECT Cod_Usuario, Nome FROM Usuario";
                    $resultUsuarios = mysqli_query($conn, $sqlUsuarios);
                    $usuarios = [];
                    while ($usuario = mysqli_fetch_assoc($resultUsuarios)) {
                        $usuarios[] = $usuario;
                    }
                ?>

                    <div class="item-checklist" id="criar-checklist">
                        <input type="text" class="name-ck" id="name-checklist" placeholder="Nome do checklist">

                        <div class="escalonamento-ck" id=""></div>

                        <div class="complexidade-ck" id="">
                            <select name="complexidade" id="select-complexidade" class="selects-admin">
                                <option value="">Complexidade</option>
                                <option value="Alta">Alta - 5 dias</option>
                                <option value="Media">Média - 3 dias</option>
                                <option value="Baixa">Baixa - 1 dia</option>
                            </select>
                        </div>

                        <div class="responsavel-ck" id="">
                            <select name="responsaveis" id="select-responsavel" class="selects-admin">
                                <option value="">Responsável</option>
                                <?php foreach ($usuarios as $usuario) { ?>
                                    <option value="<?php echo $usuario['Cod_Usuario']; ?>"><?php echo $usuario['Nome']; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="conformidade-ck">
                            <input type="button" value="Publicar" id="botao-publicar-criar">
                        </div>
                    </div>
                <?php } ?>



                <!-- Checklist editavel para admin -->
                <?php
                if ($tipoUsuario == '1') {
                    // Buscar os itens do checklist
                    $sqlItens = "SELECT IC.ID, IC.Nome, IC.Escalonamento, IC.Complexidade, IC.Responsavel, U.Nome as NomeResponsavel 
                    FROM ItemChecklist IC
                    INNER JOIN Usuario U ON IC.Responsavel = U.Cod_Usuario
                    WHERE IC.fk_Cod_Checklist = $checklistId ORDER BY IC.ID";
                    $resultItens = mysqli_query($conn, $sqlItens);

                    // Buscar todos os usuários
                    $sqlUsuarios = "SELECT Cod_Usuario, Nome FROM Usuario";
                    $resultUsuarios = mysqli_query($conn, $sqlUsuarios);

                    // Armazenar os usuários em um array para fácil uso
                    $usuarios = [];
                    while ($usuario = mysqli_fetch_assoc($resultUsuarios)) {
                        $usuarios[] = $usuario;
                    }

                    $contador = 0;
                    while ($item = mysqli_fetch_assoc($resultItens)) {
                        $id = $item['ID'];
                        $nome = $item['Nome'];
                        $escalonamento = $item['Escalonamento'];
                        $complexidade = $item['Complexidade'];
                        $idResponsavel = $item['Responsavel'];
                        $responsavelAtual = $item['NomeResponsavel'];
                        $contador++;
                ?>

                        <div class="item-checklist">
                            <div class="id-ck" id="<?php echo $id; ?>"> <?php echo $contador; ?></div>

                            <input type="text" class="name-ck-editar" value="<?php echo $nome; ?>">

                            <div class="escalonamento-ck"><?php echo $escalonamento; ?></div>

                            <div class="complexidade-ck">
                                <select name="complexidade" class="selects-admin">
                                    <option value="Alta" <?php echo ($complexidade == 'Alta') ? 'selected' : ''; ?>>Alta - 5 dias</option>
                                    <option value="Media" <?php echo ($complexidade == 'Media') ? 'selected' : ''; ?>>Média - 3 dias</option>
                                    <option value="Baixa" <?php echo ($complexidade == 'Baixa') ? 'selected' : ''; ?>>Baixa - 1 dia</option>
                                </select>
                            </div>

                            <div class="responsavel-ck">
                                <select name="responsaveis" class="selects-admin">
                                    <?php foreach ($usuarios as $usuario) {
                                        $selected = ($usuario['Cod_Usuario'] == $idResponsavel) ? 'selected' : '';
                                    ?>
                                        <option value="<?php echo $usuario['Cod_Usuario']; ?>" <?php echo $selected; ?>>
                                            <?php echo $usuario['Nome']; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="conformidade-ck">
                                <input type="button" value="Excluir" class="botao-excluir-item" data-id="<?php echo $id; ?>">
                            </div>
                        </div>

                <?php
                    }
                }
                ?>



                <!-- Checklist normal para auditor quando versao = 0 -->
                <?php
                if ($tipoUsuario == '2' and $versao == 0) {
                    $sqlItens = "SELECT i.ID, i.Nome, i.Escalonamento, i.Complexidade, u.Nome as Responsavel, i.Conforme 
                    FROM ItemChecklist i 
                    INNER JOIN Usuario u ON i.Responsavel = u.Cod_Usuario 
                    WHERE i.fk_Cod_Checklist = '$codChecklist' ORDER BY i.ID";
                    $resultadoItens = mysqli_query($conn, $sqlItens);
                    $contador = 0;
                    while ($item = mysqli_fetch_assoc($resultadoItens)) { 
                        $contador++ ?>
                        <div class="item-checklist">
                            <div class="id-ck" id="<?php echo $item['ID']; ?>"> <?php echo $contador; ?></div>
                            <div class="name-ck"><?php echo $item['Nome']; ?></div>
                            <div class="escalonamento-ck"><?php echo $item['Escalonamento']; ?></div>
                            <div class="complexidade-ck"><?php echo $item['Complexidade']; ?></div>
                            <div class="responsavel-ck"><?php echo $item['Responsavel']; ?></div>
                            <div class="conformidade-ck">
                                <select name="conformidadade-ck" class="select-conformidade" data-id="<?php echo $item['ID']; ?>">
                                    <option value="CC" <?php echo ($item['Conforme'] == 'CC') ? 'selected' : ''; ?>>Conforme</option>
                                    <option value="NC" <?php echo ($item['Conforme'] == 'NC') ? 'selected' : ''; ?>>Não conforme</option>
                                    <option value="NA" <?php echo ($item['Conforme'] == 'NA') ? 'selected' : ''; ?>>Não aplicável</option>
                                </select>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>



                <!-- Checklist normal para auditor quando versao > 0 -->
                <?php if ($tipoUsuario == '2' and $versao > 0) {
                    $odenarEscalonamento = $maxEscalonamento - $versao;
                    $sqlItens = "SELECT i.ID, i.Nome, i.Escalonamento, i.Complexidade, u.Nome as Responsavel, i.Conforme 
                    FROM ItemChecklist i
                    INNER JOIN Usuario u ON i.Responsavel = u.Cod_Usuario 
                    WHERE i.fk_Cod_Checklist = '$codChecklist' AND Conforme = 'NC' AND Escalonamento > '$odenarEscalonamento'
                    ORDER BY i.ID";
                    $contador = 0;
                    $resultadoItens = mysqli_query($conn, $sqlItens);
                    while ($item = mysqli_fetch_assoc($resultadoItens)) { 
                        $contador++ ?>
                        <div class="item-checklist">
                            <div class="id-ck" id="<?php echo $item['ID']; ?>"> <?php echo $contador; ?></div>
                            <div class="name-ck"><?php echo $item['Nome']; ?></div>
                            <div class="escalonamento-ck"><?php echo $item['Escalonamento']; ?></div>
                            <div class="complexidade-ck"><?php echo $item['Complexidade']; ?></div>
                            <div class="responsavel-ck"><?php echo $item['Responsavel']; ?></div>
                            <div class="conformidade-ck">
                                <?php if ($versao == $maxEscalonamento) { ?>
                                <select name="conformidadade-ck" class="select-conformidade" data-id="<?php echo $item['ID']; ?>" >
                                    <option value="CC" <?php echo ($item['Conforme'] == 'CC') ? 'selected' : ''; ?>>Conforme</option>
                                    <option value="NC" <?php echo ($item['Conforme'] == 'NC') ? 'selected' : ''; ?>>Não conforme</option>
                                    <option value="NA" <?php echo ($item['Conforme'] == 'NA') ? 'selected' : ''; ?>>Não aplicável</option>
                                </select>
                                <?php } else { echo 'Não conforme'; } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </section>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="script.js"></script>
    </main>

</body>

</html>