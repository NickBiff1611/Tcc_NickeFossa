<?php
session_start();
include "config.php";

if (!isset($_SESSION["usuario_id"])) {
    echo "Erro: Usuário não autenticado";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_id = $_SESSION["usuario_id"];
    $nome = $_POST["nome"] ?? '';
    $telefone = $_POST["telefone"] ?? '';
    $senha = $_POST["senha"] ?? '';
    
    // Processar upload de foto
    $foto_perfil = null;
    if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] == UPLOAD_ERR_OK) {
        $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $tipo_arquivo = $_FILES["foto"]["type"];
        
        if (!in_array($tipo_arquivo, $tipos_permitidos)) {
            echo "Erro: Tipo de arquivo não permitido. Use apenas JPG, PNG, GIF ou WEBP.";
            exit;
        }
        
        if ($_FILES["foto"]["size"] > 3 * 1024 * 1024) {
            echo "Erro: O arquivo deve ter no máximo 3MB.";
            exit;
        }
        
        $diretorio = "uploads/";
        if (!file_exists($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
        
        $extensao = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $nome_arquivo = "perfil_" . $usuario_id . "_" . time() . "." . $extensao;
        $caminho_completo = $diretorio . $nome_arquivo;
        
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $caminho_completo)) {
            $foto_perfil = $caminho_completo;
            
            // Excluir foto anterior se existir
            $sql_foto_antiga = "SELECT foto_perfil FROM usuarios WHERE id = ?";
            $stmt_foto = $conn->prepare($sql_foto_antiga);
            $stmt_foto->bind_param("i", $usuario_id);
            $stmt_foto->execute();
            $stmt_foto->bind_result($foto_antiga);
            $stmt_foto->fetch();
            $stmt_foto->close();
            
            if ($foto_antiga && file_exists($foto_antiga) && $foto_antiga != "imagens/avatar-placeholder.png") {
                unlink($foto_antiga);
            }
        } else {
            echo "Erro: Não foi possível fazer upload da imagem.";
            exit;
        }
    }
    
    // Construir query de atualização
    $sql = "UPDATE usuarios SET nome = ?, telefone = ?";
    $params = [$nome, $telefone];
    $types = "ss";
    
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql .= ", senha = ?";
        $params[] = $senha_hash;
        $types .= "s";
    }
    
    if ($foto_perfil) {
        $sql .= ", foto_perfil = ?";
        $params[] = $foto_perfil;
        $types .= "s";
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $usuario_id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Atualizar dados na sessão
        $_SESSION["usuario_nome"] = $nome;
        $_SESSION["usuario_telefone"] = $telefone;
        if ($foto_perfil) {
            $_SESSION["usuario_foto"] = $foto_perfil;
        }
        
        echo "success";
    } else {
        echo "Erro ao atualizar perfil: " . $conn->error;
    }
} else {
    echo "Método não permitido";
}
?>