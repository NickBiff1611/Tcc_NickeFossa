<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = trim($_POST["nome"] ?? '');
    $email = trim($_POST["email"] ?? '');
    $telefone = trim($_POST["telefone"] ?? '');
    $mensagem = trim($_POST["mensagem"] ?? '');
    $avaliacao = intval($_POST["avaliacao"] ?? 5);
    $feedback_id = intval($_POST["feedback_id"] ?? 0);
    
    // Validação básica
    if (empty($nome) || empty($email) || empty($mensagem) || $avaliacao < 1 || $avaliacao > 5) {
        echo "Erro: Nome, e-mail, mensagem e avaliação são obrigatórios";
        exit;
    }
    
    // Verificar se o e-mail é válido
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Erro: E-mail inválido";
        exit;
    }
    
    // Se o usuário estiver logado, associar o feedback ao usuário
    $usuario_id = isset($_SESSION["usuario_id"]) ? $_SESSION["usuario_id"] : NULL;
    
    if ($feedback_id > 0) {
        // Modo edição - verificar se o feedback pertence ao usuário
        $check_sql = "SELECT id FROM feedbacks WHERE id = ? AND usuario_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $feedback_id, $usuario_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows === 0) {
            echo "Erro: Feedback não encontrado ou não pertence ao usuário";
            exit;
        }
        
        // Atualizar feedback
        $sql = "UPDATE feedbacks SET nome = ?, email = ?, telefone = ?, comentario = ?, avaliacao = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssii", $nome, $email, $telefone, $mensagem, $avaliacao, $feedback_id);
            
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Erro ao atualizar feedback: " . $conn->error;
            }
            
            $stmt->close();
        }
    } else {
        // Modo criação - inserir novo feedback
        $sql = "INSERT INTO feedbacks (usuario_id, nome, email, telefone, comentario, avaliacao) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("issssi", $usuario_id, $nome, $email, $telefone, $mensagem, $avaliacao);
            
            if ($stmt->execute()) {
                echo "success";
            } else {
                echo "Erro ao salvar feedback: " . $conn->error;
            }
            
            $stmt->close();
        }
    }
    
    $conn->close();
} else {
    echo "Método não permitido";
}
?>