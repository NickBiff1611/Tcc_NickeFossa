<?php
session_start();
include "config.php";

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["success" => false, "message" => "Usuário não autenticado"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    parse_str(file_get_contents("php://input"), $params);
    $carro_id = $params['id'] ?? null;
    $usuario_id = $_SESSION["usuario_id"];
    
    if (!$carro_id) {
        echo json_encode(["success" => false, "message" => "ID do carro não fornecido"]);
        exit;
    }
    
   
    $check = $conn->prepare("SELECT id FROM carros WHERE id = ? AND usuario_id = ?");
    $check->bind_param("ii", $carro_id, $usuario_id);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows === 0) {
        echo json_encode(["success" => false, "message" => "Carro não encontrado ou não pertence ao usuário"]);
        exit;
    }
    
   
    $sql = "DELETE FROM carros WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $carro_id);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Carro excluído com sucesso"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erro ao excluir carro: " . $conn->error]);
    }
}
?>