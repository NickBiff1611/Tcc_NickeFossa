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
    echo json_encode(["error" => "Usuário não autenticado", "redirect" => "login.html"]);
    exit;
}

include "config.php";

$usuario_id = $_SESSION["usuario_id"];

try {
    $sql = "SELECT id, marca, modelo, ano, placa, cor, combustivel, km FROM carros WHERE usuario_id = ? ORDER BY id DESC";
    
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
    $carros = [];
    
    while ($row = $result->fetch_assoc()) {
        $carros[] = $row;
    }
    
    echo json_encode(["success" => true, "data" => $carros], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro ao buscar carros",
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