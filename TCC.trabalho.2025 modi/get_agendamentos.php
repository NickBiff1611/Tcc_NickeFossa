<?php
// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir cabeçalho JSON primeiro
header('Content-Type: application/json; charset=utf-8');

// Verificar se o usuário está autenticado
if (!isset($_SESSION["usuario_id"])) {
    http_response_code(401);
    echo json_encode(["success" => false, "error" => "Usuário não autenticado"]);
    exit;
}

include "config.php";

$usuario_id = $_SESSION["usuario_id"];

try {
    // Consulta para buscar agendamentos com informações do carro (REMOVER mecanico)
    $sql = "SELECT a.id, a.data, a.descricao, a.status, 
                   c.marca, c.modelo, c.placa 
            FROM agendamentos a 
            INNER JOIN carros c ON a.carro_id = c.id 
            WHERE a.usuario_id = ? 
            ORDER BY a.data DESC, a.id DESC";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $conn->error);
    }
    
    if (!$stmt->bind_param("i", $usuario_id)) {
        throw new Exception("Erro ao vincular parâmetros: " . $stmt->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Erro na execução da consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $agendamentos = [];
    
    while ($row = $result->fetch_assoc()) {
        $agendamentos[] = $row;
    }
    
    echo json_encode([
        "success" => true, 
        "data" => $agendamentos
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro ao buscar agendamentos",
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>