<?php
session_start();
include "config.php";


if (!isset($_SESSION["usuario_id"])) {
    http_response_code(401);
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit;
}

$usuario_id = $_SESSION["usuario_id"];
$sql = "SELECT id, marca, modelo, ano, placa, cor, combustivel, km FROM carros WHERE usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$carros = [];
while ($row = $result->fetch_assoc()) {
    $carros[] = $row;
}


header('Content-Type: application/json');
echo json_encode($carros);
?>