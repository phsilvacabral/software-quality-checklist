<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codChecklist = $_POST['codChecklist'];
    $nomeChecklist = $_POST['nomeChecklist'];
    $escalonamento = $_POST['escalonamento'];
    $complexidade = $_POST['complexidade'];
    $responsavel = $_POST['responsavel'];

    if (!empty($nomeChecklist) && !empty($complexidade) && !empty($responsavel)) {
        $sql = "INSERT INTO ItemChecklist (Nome, Escalonamento, Complexidade, Responsavel, fk_Cod_Checklist)
                VALUES ('$nomeChecklist', '$escalonamento', '$complexidade', '$responsavel', '$codChecklist')";

        if (mysqli_query($conn, $sql)) {
            echo 'success';
        } else {
            echo 'Erro: ' . mysqli_error($conn);
        }
    } else {
        echo 'Preencha todos os campos obrigatórios.';
    }
}
?>
