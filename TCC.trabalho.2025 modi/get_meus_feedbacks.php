<?php
session_start();
include "config.php";

// Verificar se o usuário está autenticado
if (!isset($_SESSION["usuario_id"])) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Buscar feedbacks do usuário
$sql = "SELECT id, nome, email, telefone, comentario, avaliacao, data FROM feedbacks WHERE usuario_id = ? ORDER BY data DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$feedbacks = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($feedbacks);

$stmt->close();
$conn->close();
?>