<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $senha = $_POST["senha"];

    $sql = "SELECT id, nome, email, telefone, cpf, senha FROM usuarios WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc();

        if (password_verify($senha, $user["senha"])) {
            // Configurar sessão
            $_SESSION["usuario_id"] = $user["id"];
            $_SESSION["usuario_nome"] = $user["nome"];
            $_SESSION["usuario_email"] = $user["email"];
            $_SESSION["usuario_telefone"] = $user["telefone"];
            $_SESSION["usuario_cpf"] = $user["cpf"];
            
            // Configurar cookie de sessão para persistência
            session_regenerate_id(true);
            
            echo "<script>
                    localStorage.setItem('usuario_logado', JSON.stringify({
                        id: " . $user["id"] . ",
                        nome: '" . addslashes($user["nome"]) . "',
                        email: '" . addslashes($user["email"]) . "',
                        telefone: '" . addslashes($user["telefone"]) . "',
                        cpf: '" . addslashes($user["cpf"]) . "'
                    }));
                    window.location.href = 'index.html';
                  </script>";
            exit;
        } else {
            echo "<script>alert('❌ Senha incorreta!'); window.location.href='login.html';</script>";
        }
    } else {
        echo "<script>alert('❌ Usuário não encontrado!'); window.location.href='login.html';</script>";
    }
}
?>