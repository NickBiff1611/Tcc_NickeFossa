<?php
session_start();
include "config.php";

if (!isset($_SESSION["usuario_id"])) {
    echo "<script>alert('⚠ Você precisa estar logado para cadastrar um carro.'); window.location.href='login.html';</script>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $marca = trim($_POST["marca"]);
    $modelo = trim($_POST["modelo"]);
    $ano = intval($_POST["ano"]);
    $placa = strtoupper(trim($_POST["placa"])); 
    $cor = trim($_POST["cor"]);
    $combustivel = trim($_POST["combustivel"]);
    $km = intval($_POST["km"]);

 
    $check = $conn->prepare("SELECT id FROM carros WHERE placa = ?");
    $check->bind_param("s", $placa);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows > 0) {
        echo "<script>alert('⚠ Essa placa já está cadastrada!'); window.location.href='perfil.html';</script>";
        exit;
    }

    $sql = "INSERT INTO carros (usuario_id, marca, modelo, ano, placa, cor, combustivel, km)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ississsi", $usuario_id, $marca, $modelo, $ano, $placa, $cor, $combustivel, $km);

    if ($stmt->execute()) {
        echo "<script>
                alert('✅ Carro cadastrado com sucesso!');
                window.location.href = 'perfil.html';
              </script>";
    } else {
        echo "<script>alert('❌ Erro ao cadastrar carro: " . addslashes($conn->error) . "'); window.location.href='perfil.html';</script>";
    }
}
?>