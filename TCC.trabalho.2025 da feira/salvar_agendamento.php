<?php
session_start();
include "config.php";


if (!isset($_SESSION["usuario_id"])) {
    echo "Erro: Usuário não autenticado";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $carro_id = $_POST["carro_id"];
    $mecanico = $_POST["mecanico"];
    $horario = $_POST["horario"];
    $descricao = $_POST["descricao"];

  
    if (empty($carro_id) || empty($mecanico) || empty($horario) || empty($descricao)) {
        echo "Erro: Todos os campos são obrigatórios";
        exit;
    }
    
    
    $check_carro = $conn->prepare("SELECT id FROM carros WHERE id = ? AND usuario_id = ?");
    $check_carro->bind_param("ii", $carro_id, $usuario_id);
    $check_carro->execute();
    $check_carro->store_result();
    
    if ($check_carro->num_rows === 0) {
        echo "Erro: Carro não encontrado ou não pertence ao usuário";
        exit;
    }

  
    $sql = "INSERT INTO agendamentos (usuario_id, carro_id, mecanico, horario, descricao, status) 
            VALUES (?, ?, ?, ?, ?, 'agendado')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $usuario_id, $carro_id, $mecanico, $horario, $descricao);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Erro ao salvar: " . $conn->error;
    }
    
    $stmt->close();
} else {
    echo "Método não permitido";
}

$conn->close();
?>