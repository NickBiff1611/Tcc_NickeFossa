<?php
include "config.php";

function atualizarBanco($conn) {
   
    $check_column = $conn->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'oficina' 
        AND TABLE_NAME = 'usuarios' 
        AND COLUMN_NAME = 'created_at'");
    
    $result = $check_column->fetch_assoc();
    if ($result['total'] == 0) {
        $conn->query("ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
        echo "Coluna created_at adicionada com sucesso!<br>";
    }

 
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
        ) ENGINE=InnoDB");
        echo "Tabela feedbacks criada com sucesso!<br>";
    }

   
    $check_status = $conn->query("SELECT COUNT(*) as total FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'oficina' 
        AND TABLE_NAME = 'agendamentos' 
        AND COLUMN_NAME = 'status'");
    
    $result_status = $check_status->fetch_assoc();
    if ($result_status['total'] == 0) {
        $conn->query("ALTER TABLE agendamentos ADD COLUMN status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado'");
        echo "Coluna status adicionada aos agendamentos!<br>";
    }

    echo "Banco de dados atualizado com sucesso!";
}


atualizarBanco($conn);
?>