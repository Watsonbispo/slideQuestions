-- Schema do banco de dados para o sistema de questionários
-- CREATE DATABASE IF NOT EXISTS projectslides CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE projectslides;

-- Tabela de administradores
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de perguntas
CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title TEXT NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de opções de resposta
CREATE TABLE IF NOT EXISTS question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_id VARCHAR(10) NOT NULL, -- 'a', 'b', 'c', 'd'
    text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_question_option (question_id, option_id)
);

-- Tabela de configurações
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) UNIQUE NOT NULL,
    `value` TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir admin padrão (senha: 197508)
INSERT INTO admins (username, password_hash) VALUES 
('Davi Sena', '$2y$10$3XOaEDbACMqb5Bnw6TINnuokUgTsrLyC3uuJ6M/QHAYPzL8ZbXMki');

-- Inserir configurações padrão
INSERT INTO settings (`key`, `value`) VALUES 
('background_image_path', '/assets/imgs/590f67723c50604dd9ab22d6dd30c9ba.jpg');

-- Inserir perguntas padrão
INSERT INTO questions (title, sort_order) VALUES 
('Sobre sua saúde, há alguma condição relevante que devamos considerar?', 1),
('Como é sua rotina diária em termos de tempo disponível?', 2),
('Qual é o seu nível atual de atividade?', 3),
('Você prefere orientações mais diretas ou personalizadas?', 4),
('Quais resultados você espera alcançar nas próximas semanas?', 5);

-- Inserir opções para a primeira pergunta
INSERT INTO question_options (question_id, option_id, text) VALUES 
(1, 'a', 'Hipertensão controlada'),
(1, 'b', 'Diabetes controlado'),
(1, 'c', 'Lesões ou limitações físicas'),
(1, 'd', 'Nenhuma das opções acima');

-- Inserir opções para a segunda pergunta
INSERT INTO question_options (question_id, option_id, text) VALUES 
(2, 'a', 'Menos de 30 minutos por dia'),
(2, 'b', 'Entre 30 e 60 minutos por dia'),
(2, 'c', 'Mais de 60 minutos por dia');

-- Inserir opções para a terceira pergunta
INSERT INTO question_options (question_id, option_id, text) VALUES 
(3, 'a', 'Sedentário(a)'),
(3, 'b', 'Ativo(a) leve (1–2x por semana)'),
(3, 'c', 'Ativo(a) moderado/intenso (3+ vezes por semana)');

-- Inserir opções para a quarta pergunta
INSERT INTO question_options (question_id, option_id, text) VALUES 
(4, 'a', 'Diretas e objetivas (passo a passo simples)'),
(4, 'b', 'Personalizadas (ajustes conforme meu perfil)'),
(4, 'c', 'Tanto faz, desde que funcione');

-- Inserir opções para a quinta pergunta
INSERT INTO question_options (question_id, option_id, text) VALUES 
(5, 'a', 'Melhorar disposição/energia diária'),
(5, 'b', 'Criar/retomar uma rotina consistente'),
(5, 'c', 'Aprimorar desempenho em atividades específicas');
