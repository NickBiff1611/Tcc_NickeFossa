<?php
session_start();
include "config.php";

// Verificar se o usuário está autenticado
if (!isset($_SESSION["usuario_id"])) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "error" => "Usuário não autenticado"]);
    exit;
}

// Ler dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);
$agendamento_id = $input['id'] ?? null;
$usuario_id = $_SESSION["usuario_id"];

if (!$agendamento_id) {
    echo json_encode(["success" => false, "error" => "ID do agendamento não fornecido"]);
    exit;
}

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

// Excluir o agendamento
$delete_sql = "DELETE FROM agendamentos WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $agendamento_id);

if ($delete_stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Agendamento excluído com sucesso"]);
} else {
    echo json_encode(["success" => false, "error" => "Erro ao excluir agendamento: " . $conn->error]);
}

$delete_stmt->close();
$check_stmt->close();
$conn->close();
?>