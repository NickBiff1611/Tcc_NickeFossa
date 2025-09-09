<?php
session_start();
include "config.php";

// Verificar se o usuário está autenticado
if (!isset($_SESSION["usuario_id"])) {
    echo "Erro: Usuário não autenticado";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $carro_id = $_POST["carro_id"];
    $data = $_POST["data"];
    $descricao = $_POST["descricao"];

    // Validação dos campos
    if (empty($carro_id) || empty($data) || empty($descricao)) {
        echo "Erro: Todos os campos são obrigatórios";
        exit;
    }
    
    // Verificar se o carro pertence ao usuário
    $check_carro = $conn->prepare("SELECT id FROM carros WHERE id = ? AND usuario_id = ?");
    $check_carro->bind_param("ii", $carro_id, $usuario_id);
    $check_carro->execute();
    $check_carro->store_result();
    
    if ($check_carro->num_rows === 0) {
        echo "Erro: Carro não encontrado ou não pertence ao usuário";
        exit;
    }

    // Verificar se ainda há vagas para esta data
    $check_vagas = $conn->prepare("SELECT COUNT(*) as total FROM agendamentos WHERE data = ? AND status != 'cancelado'");
    $check_vagas->bind_param("s", $data);
    $check_vagas->execute();
    $result = $check_vagas->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['total'] >= 5) { // Limite de 5 vagas por dia
        echo "Erro: Não há vagas disponíveis para esta data";
        exit;
    }

    // Inserir o agendamento
    $sql = "INSERT INTO agendamentos (usuario_id, carro_id, data, descricao, status) 
            VALUES (?, ?, ?, ?, 'agendado')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $usuario_id, $carro_id, $data, $descricao);

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