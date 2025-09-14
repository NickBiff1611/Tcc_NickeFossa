<?php
include "config.php";

// Buscar todos os feedbacks
$sql = "SELECT nome, comentario, avaliacao, data FROM feedbacks ORDER BY data DESC";
$result = $conn->query($sql);

$feedbacks = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedbacks[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($feedbacks);

$conn->close();
?>