<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    
    if (empty($titulo) || empty($descricao)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, preencha todos os campos.']);
        exit;
    }

    $sql = "INSERT INTO Checklist (Titulo, Descricao, Criador, Dt_Modificacao) VALUES (?, ?, ?, NOW())";
    
    $criador = $_POST['criador'];

    // Prepara e executa a consulta
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('ssi', $titulo, $descricao, $criador);
        
        if ($stmt->execute()) {
            // Retorna sucesso
            echo json_encode(['success' => true, 'message' => 'Checklist criado com sucesso!']);
        } else {
            // Retorna erro ao executar
            echo json_encode(['success' => false, 'message' => 'Erro ao criar checklist.']);
        }

        $stmt->close();
    } else {
        // Retorna erro ao preparar a consulta
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta.']);
    }
} else {
    // Retorna erro se a requisição não for POST
    echo json_encode(['success' => false, 'message' => 'Método não permitido.']);
}
?>
