<?php
include('connection.php');

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Query para excluir o item
    $sql = "DELETE FROM ItemChecklist WHERE ID = $id";

    if (mysqli_query($conn, $sql)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
