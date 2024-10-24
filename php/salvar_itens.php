<?php
include('connection.php');

if (isset($_POST['itens']) && isset($_POST['checklistId'])) {
    $itens = json_decode($_POST['itens'], true); // Decodifica o JSON enviado
    $checklistId = $_POST['checklistId'];

    if (is_array($itens)) {
        foreach ($itens as $item) {
            // Extrai os dados do item
            $id = mysqli_real_escape_string($conn, $item['id']);
            $nome = mysqli_real_escape_string($conn, $item['nome']);
            $complexidade = mysqli_real_escape_string($conn, $item['complexidade']);
            $responsavel = mysqli_real_escape_string($conn, $item['responsavel']);

            // Certifique-se de que todos os campos estão preenchidos
            if (!empty($id) && !empty($nome) && !empty($complexidade) && !empty($responsavel)) {
                // Query para atualizar o item
                $sql = "UPDATE ItemChecklist 
                        SET Nome = '$nome', Complexidade = '$complexidade', Responsavel = '$responsavel' 
                        WHERE ID = $id AND fk_Cod_Checklist = $checklistId";

                if (mysqli_query($conn, $sql)) {
                    // Se a query foi bem-sucedida, continue
                    // Atualiza a data de modificação do checklist (usando a função date)
                    date_default_timezone_set('America/Sao_Paulo');
                    $novaData = date('Y-m-d H:i:s');
                    $sqlUpdateChecklist = "UPDATE Checklist SET Dt_Modificacao = '$novaData' WHERE Cod_Checklist = $checklistId";
                    mysqli_query($conn, $sqlUpdateChecklist);
                    continue;
                } else {
                    // Se falhar, retorne o erro do MySQL
                    echo 'error: ' . mysqli_error($conn);
                    exit;
                }
            } else {
                // Caso algum campo esteja vazio, retorna um erro
                echo 'error: invalid data';
                exit;
            }
        }
        echo 'success';
    } else {
        echo 'error: invalid items array';
    }
} else {
    echo 'error: missing data';
}
