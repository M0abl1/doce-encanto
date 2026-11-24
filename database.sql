-- Estrutura do banco da Loja de Bolos (MySQL / MariaDB).
-- Uso: mysql -u root -p < database.sql
-- Nenhum usuário é criado aqui: crie o primeiro administrador com  php criar_admin.php <usuario>

CREATE DATABASE IF NOT EXISTS loja_bolos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja_bolos;

CREATE TABLE IF NOT EXISTS funcionarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(50) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Limite de tentativas de login (por usuário e por IP, em janelas de 15 minutos).
CREATE TABLE IF NOT EXISTS tentativas_login (
    chave VARCHAR(120) PRIMARY KEY,
    tentativas INT UNSIGNED NOT NULL DEFAULT 0,
    janela_inicio DATETIME NOT NULL,
    INDEX (janela_inicio)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bolos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    imagem_url VARCHAR(500) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
