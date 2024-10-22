<?php
include('connection.php');

// Verifique se os dados foram recebidos corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Exibe os dados recebidos
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';

    $itens = isset($_POST['itens']) ? $_POST['itens'] : null;
    $codChecklist = isset($_POST['codChecklist']) ? intval($_POST['codChecklist']) : 0;

    if (!empty($itens) && $codChecklist > 0) {
        // Atualiza cada item que foi modificado
        foreach ($itens as $itemId => $novoConforme) {
            $sqlUpdateItem = "UPDATE ItemChecklist SET Conforme = '$novoConforme' WHERE ID = '$itemId'";
            if (!mysqli_query($conn, $sqlUpdateItem)) {
                echo "Erro ao atualizar o item $itemId: " . mysqli_error($conn);
                exit;
            }
        }

        // Atualiza a data de modificação do checklist (usando a função date)
        date_default_timezone_set('America/Sao_Paulo');
        $novaData = date('Y-m-d H:i:s');
        $sqlUpdateChecklist = "UPDATE Checklist SET Dt_Modificacao = '$novaData' WHERE Cod_Checklist = '$codChecklist'";
        if (mysqli_query($conn, $sqlUpdateChecklist)) {
            echo "Checklist atualizado com sucesso!";
        } else {
            echo "Erro ao atualizar a data do checklist: " . mysqli_error($conn);
        }
    } else {
        echo "Dados inválidos recebidos.";
    }
}
