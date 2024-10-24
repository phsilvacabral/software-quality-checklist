<?php 
include('connection.php');

// Verifique se os dados foram recebidos corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codChecklist = isset($_POST['codChecklist']) ? intval($_POST['codChecklist']) : 0;

    if (!empty($codChecklist)) {
        // Excluir a checklist
        $sqlDeleteItem = "DELETE FROM ItemChecklist WHERE fk_Cod_Checklist = '$codChecklist'";
        $sqlDelete = "DELETE FROM Checklist WHERE Cod_Checklist = '$codChecklist'";
        if (mysqli_query($conn, $sqlDeleteItem) && mysqli_query($conn, $sqlDelete)) {
            echo "Checklist excluída com sucesso!";
        } else {
            echo "Erro ao excluir a checklist: " . mysqli_error($conn);
        }
    } else {
        echo "Dados inválidos recebidos.";
    }
}
?>