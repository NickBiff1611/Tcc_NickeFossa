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
    echo json_encode(["success" => false, "message" => "Usuário não autenticado", "redirect" => "login.html"]);
    exit;
}

include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $marca = trim($_POST["marca"] ?? '');
    $modelo = trim($_POST["modelo"] ?? '');
    $ano = intval($_POST["ano"] ?? 0);
    $placa = strtoupper(trim($_POST["placa"] ?? '')); 
    $cor = trim($_POST["cor"] ?? '');
    $combustivel = trim($_POST["combustivel"] ?? '');
    $km = intval($_POST["km"] ?? 0);

    // Validações
    if (empty($marca) || empty($modelo) || empty($placa) || empty($combustivel)) {
        echo json_encode(["success" => false, "message" => "Todos os campos obrigatórios devem ser preenchidos."]);
        exit;
    }

    if ($ano < 1900 || $ano > 2099) {
        echo json_encode(["success" => false, "message" => "Ano inválido."]);
        exit;
    }

    if ($km < 0) {
        echo json_encode(["success" => false, "message" => "Quilometragem inválida."]);
        exit;
    }
 
    // Verificar se a placa já existe
    $check = $conn->prepare("SELECT id FROM carros WHERE placa = ?");
    $check->bind_param("s", $placa);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Essa placa já está cadastrada!"]);
        exit;
    }

    // Inserir no banco de dados
    $sql = "INSERT INTO carros (usuario_id, marca, modelo, ano, placa, cor, combustivel, km)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ississsi", $usuario_id, $marca, $modelo, $ano, $placa, $cor, $combustivel, $km);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Carro cadastrado com sucesso!"]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro ao cadastrar carro: " . $conn->error]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Erro na preparação da consulta: " . $conn->error]);
    }
    
    $check->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Método não permitido"]);
}
?>