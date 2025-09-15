<?php
session_start();
include "config.php";

// Verificar se o usuário está autenticado
if (!isset($_SESSION["usuario_id"])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Usuário não autenticado"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $mes = $_GET['mes'] ?? date('n');
    $ano = $_GET['ano'] ?? date('Y');
    
    // Calcular primeiro e último dia do mês
    $primeiroDia = 1;
    $ultimoDia = date('t', mktime(0, 0, 0, $mes, 1, $ano));
    
    $disponibilidade = [];
    
    for ($dia = $primeiroDia; $dia <= $ultimoDia; $dia++) {
        $data = sprintf('%04d-%02d-%02d', $ano, $mes, $dia);
        
        // Verificar se é uma data passada
        $dataAtual = date('Y-m-d');
        if ($data < $dataAtual) {
            $disponibilidade[$data] = -1; // Data passada
            continue;
        }
        
        // Consultar quantos agendamentos já existem para esta data
        $sql = "SELECT COUNT(*) as total FROM agendamentos WHERE data = ? AND status != 'cancelado'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $data);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $agendamentos = $row['total'];
        $vagasDisponiveis = 5 - $agendamentos; // Máximo de 5 vagas por dia
        
        $disponibilidade[$data] = max(0, $vagasDisponiveis); // Não pode ser negativo
    }
    
    header('Content-Type: application/json');
    echo json_encode($disponibilidade);
} else {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(["error" => "Método não permitido"]);
}
?>