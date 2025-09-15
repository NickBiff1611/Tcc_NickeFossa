<?php
session_start();
include "config.php";

// Verificar se o usuário está autenticado
if (!isset($_SESSION["usuario_id"])) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

// Ler dados JSON da requisição
$input = json_decode(file_get_contents('php://input'), true);
$feedback_id = $input['id'] ?? null;
$usuario_id = $_SESSION["usuario_id"];

if (!$feedback_id) {
    echo json_encode(["success" => false, "message" => "ID do feedback não fornecido"]);
    exit;
}

// Verificar se o feedback pertence ao usuário
$check_sql = "SELECT id FROM feedbacks WHERE id = ? AND usuario_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $feedback_id, $usuario_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Feedback não encontrado ou não pertence ao usuário"]);
    exit;
}

// Excluir o feedback
$delete_sql = "DELETE FROM feedbacks WHERE id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("i", $feedback_id);

if ($delete_stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Feedback excluído com sucesso"]);
} else {
    echo json_encode(["success" => false, "message" => "Erro ao excluir feedback: " . $conn->error]);
}

$delete_stmt->close();
$check_stmt->close();
$conn->close();
?>