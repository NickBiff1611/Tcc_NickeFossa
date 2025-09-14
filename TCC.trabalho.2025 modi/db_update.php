<?php
include "config.php";

function atualizarBanco($conn) {
    // Verificar e adicionar coluna created_at na tabela usuarios se não existir
    $check_column = $conn->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'oficina_modi' 
        AND TABLE_NAME = 'usuarios' 
        AND COLUMN_NAME = 'created_at'");
    
    $result = $check_column->fetch_assoc();
    if ($result['total'] == 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Coluna created_at adicionada à tabela usuarios!<br>";
    }

    // Verificar e adicionar coluna foto_perfil na tabela usuarios se não existir
    $check_foto = $conn->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'oficina_modi' 
        AND TABLE_NAME = 'usuarios' 
        AND COLUMN_NAME = 'foto_perfil'");
    
    $result_foto = $check_foto->fetch_assoc();
    if ($result_foto['total'] == 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL");
        echo "Coluna foto_perfil adicionada à tabela usuarios!<br>";
    }

    // Verificar se a tabela feedbacks existe, se não, criar
    $check_table = $conn->query("SHOW TABLES LIKE 'feedbacks'");
    if ($check_table->num_rows == 0) {
        $conn->query("CREATE TABLE feedbacks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario_id INT,
            nome VARCHAR(100),
            avaliacao INT NOT NULL,
            comentario TEXT,
            data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        echo "Tabela feedbacks criada com sucesso!<br>";
    }

    // Verificar e adicionar coluna status na tabela agendamentos se não existir
    $check_status = $conn->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'oficina_modi' 
        AND TABLE_NAME = 'agendamentos' 
        AND COLUMN_NAME = 'status'");
    
    $result_status = $check_status->fetch_assoc();
    if ($result_status['total'] == 0) {
        $conn->query("ALTER TABLE agendamentos ADD COLUMN status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado'");
        echo "Coluna status adicionada à tabela agendamentos!<br>";
    }

    // Verificar e adicionar coluna created_at na tabela carros se não existir
    $check_carros = $conn->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'oficina_modi' 
        AND TABLE_NAME = 'carros' 
        AND COLUMN_NAME = 'created_at'");
    
    $result_carros = $check_carros->fetch_assoc();
    if ($result_carros['total'] == 0) {
        $conn->query("ALTER TABLE carros ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Coluna created_at adicionada à tabela carros!<br>";
    }

    echo "Banco de dados atualizado com sucesso!";
}

// Executar a atualização
atualizarBanco($conn);
?>