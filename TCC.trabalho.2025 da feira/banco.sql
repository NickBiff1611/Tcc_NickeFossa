CREATE DATABASE oficina;
USE oficina;


CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  telefone VARCHAR(20) NOT NULL,
  cpf VARCHAR(14) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB;


CREATE TABLE carros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  marca VARCHAR(50) NOT NULL,
  modelo VARCHAR(50) NOT NULL,
  ano INT NOT NULL,
  placa VARCHAR(8) UNIQUE NOT NULL,
  cor VARCHAR(30),
  combustivel VARCHAR(20) NOT NULL,
  km INT NOT NULL,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS agendamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    carro_id INT NOT NULL,
    mecanico VARCHAR(100) NOT NULL,
    horario VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado',
    data_agendamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (carro_id) REFERENCES carros(id) ON DELETE CASCADE
);


ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;




ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


CREATE TABLE IF NOT EXISTS feedbacks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  nome VARCHAR(100),
  avaliacao INT NOT NULL,
  comentario TEXT,
  data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;


ALTER TABLE agendamentos ADD COLUMN status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado';

ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;






SELECT COUNT(*) FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'oficina' 
AND TABLE_NAME = 'usuarios' 
AND COLUMN_NAME = 'created_at';

ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;


CREATE TABLE IF NOT EXISTS feedbacks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT,
  nome VARCHAR(100),
  avaliacao INT NOT NULL,
  comentario TEXT,
  data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;


SELECT COUNT(*) FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'oficina' 
AND TABLE_NAME = 'agendamentos' 
AND COLUMN_NAME = 'status';


ALTER TABLE agendamentos ADD COLUMN status ENUM('agendado', 'concluido', 'cancelado') DEFAULT 'agendado';