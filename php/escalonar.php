<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decodifica os dados JSON enviados via AJAX
    $itensNc = isset($_POST['itensNc']) ? json_decode($_POST['itensNc'], true) : [];
    $codChecklist = isset($_POST['codChecklist']) ? intval($_POST['codChecklist']) : 0;

    // Verifica se o array $itensNc está preenchido e se o codChecklist é válido
    if (!empty($itensNc) && $codChecklist > 0) {
        foreach ($itensNc as $itemId) {
            // Atualiza o escalonamento do item no banco de dados
            $sql = "UPDATE ItemChecklist SET Escalonamento = Escalonamento + 1 WHERE ID = ? AND fk_Cod_Checklist = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $itemId, $codChecklist);

            if (!mysqli_stmt_execute($stmt)) {
                echo "Erro ao escalonar item ID: $itemId";
            }
        }

        echo "Sucesso! Itens não conformes escalonados.";
    } else {
        echo "Nenhum item para escalonar ou código de checklist inválido.";
    }
} else {
    echo "Requisição inválida.";
}

?>
