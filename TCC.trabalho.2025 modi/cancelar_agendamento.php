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

// Ler dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);
$agendamento_id = $input['id'] ?? null;
$usuario_id = $_SESSION["usuario_id"];

if (!$agendamento_id) {
    echo json_encode(["success" => false, "error" => "ID do agendamento não fornecido"]);
    exit;
}

try {
    // Verificar se o agendamento pertence ao usuário
    $check_sql = "SELECT id FROM agendamentos WHERE id = ? AND usuario_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $agendamento_id, $usuario_id);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows === 0) {
        echo json_encode(["success" => false, "error" => "Agendamento não encontrado ou não pertence ao usuário"]);
        exit;
    }
    
    // Atualizar status para cancelado
    $update_sql = "UPDATE agendamentos SET status = 'cancelado' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $agendamento_id);
    
    if ($update_stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Agendamento cancelado com sucesso"]);
    } else {
        echo json_encode(["success" => false, "error" => "Erro ao cancelar agendamento: " . $conn->error]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Erro ao cancelar agendamento",
        "message" => $e->getMessage()
    ]);
} finally {
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($conn)) $conn->close();
}
?>