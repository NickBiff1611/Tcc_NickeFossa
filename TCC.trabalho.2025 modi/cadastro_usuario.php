<?php 
include "config.php"; 

if ($_SERVER["REQUEST_METHOD"] === "POST") { 
    $nome = $_POST["nome"] ?? ''; 
    $email = $_POST["email"]; 
    $telefone = $_POST["telefone"]; 
    $cpf = $_POST["cpf"]; 
    $senha = password_hash($_POST["senha"], PASSWORD_DEFAULT); 
    
    
    $check = $conn->prepare("SELECT id FROM usuarios WHERE email=? OR cpf=?"); 
    $check->bind_param("ss", $email, $cpf); 
    $check->execute(); 
    $check->store_result(); 
    
    if ($check->num_rows > 0) { 
        echo "⚠ Já existe uma conta com esse e-mail ou CPF!"; 
        exit; 
    } 
    
    $sql = "INSERT INTO usuarios (nome, email, telefone, cpf, senha) VALUES (?, ?, ?, ?, ?)"; 
    $stmt = $conn->prepare($sql); 
    $stmt->bind_param("sssss", $nome, $email, $telefone, $cpf, $senha); 
    
    if ($stmt->execute()) { 
        
        header("Location: login.html"); 
        exit; 
    } else { 
        echo "Erro ao cadastrar: " . $conn->error; 
    } 
} 
?>