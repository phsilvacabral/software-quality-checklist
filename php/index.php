<?php
include('connection.php');
session_start();

if (isset($_SESSION['Cod_Usuario'])) {
    header('Location: ../');
}


?>