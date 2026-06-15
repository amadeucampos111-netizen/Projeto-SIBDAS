-- ============================================================================
-- 1. CRIAÇÃO DAS TABELAS (CORRIGIDO)
-- ============================================================================

CREATE TABLE `localizaciones` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `edificio` varchar(50) NOT NULL,
  `piso` varchar(20) NOT NULL,
  `servico_departamento` varchar(100) NOT NULL,
  `sala_gabinete` varchar(50) NOT NULL
);

CREATE TABLE `fornecedores` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `nome_empresa` varchar(150) NOT NULL,
  `nif` varchar(20) UNIQUE NOT NULL,
  `contacto_telefonico` varchar(20),
  `email` varchar(100),
  `morada` text,
  `website` varchar(255),
  `pessoa_contacto` varchar(100),
  `telefone_pessoa_contacto` varchar(20),
  `observacoes` text
);

CREATE TABLE `equipamentos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `codigo_interno` varchar(50) UNIQUE NOT NULL,
  `designacao` varchar(150) NOT NULL,
  `categoria` enum('Monitorização','Suporte de vida','Terapia','Diagnóstico','Laboratório','Esterilização','Reabilitação') NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `numero_serie` varchar(100) NOT NULL,
  `fabricante` varchar(150) NOT NULL,
  `data_aquisicao` date NOT NULL,
  `ano_fabrico` int NOT NULL,
  `custo_aquisicao` decimal(10,2) NOT NULL,
  `tipo_entrada` enum('Compra','Doação','Aluguer','Empréstimo') NOT NULL,
  `estado_atual` enum('Ativo','Em manutenção','Inativo','Em calibração','Em quarentena','Abatido') NOT NULL,
  `criticidade` enum('Baixa','Média','Alta','Suporte de vida') NOT NULL,
  `localizacao_id` int NOT NULL,
  `observacoes` text,
  `criado_em` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `equipamento_fornecedor` (
  `equipamento_id` int NOT NULL,
  `fornecedor_id` int NOT NULL,
  `tipo_fornecedor` enum('Fabricante','Distribuidor ou fornecedor comercial','Empresa de assistência técnica','Fornecedor de consumíveis ou acessórios') NOT NULL,
  PRIMARY KEY (`equipamento_id`, `fornecedor_id`, `tipo_fornecedor`)
);

CREATE TABLE `garantias_contratos` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `equipamento_id` int UNIQUE NOT NULL,
  `data_inicio_garantia` date,
  `data_fim_garantia` date,
  `tem_contrato_manutencao` boolean DEFAULT false,
  `tipo_contrato` varchar(100),
  `entidade_responsavel_id` int,
  `periodicidade` varchar(50),
  `observacoes` text
);

CREATE TABLE `componentes_associados` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `equipamento_pai_id` int NOT NULL,
  `codigo_componente` varchar(50) NOT NULL,
  `designacao_componente` varchar(150) NOT NULL,
  `numero_serie_componente` varchar(100),
  `observacoes` text
);

CREATE TABLE `documentacao` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `tipo_documento` enum('Manual de utilizador','Manual de serviço','Certificado de calibração','Contrato de manutenção','Fatura ou guia de aquisição','Declaração de conformidade','Relatório técnico') NOT NULL,
  `nome_documento` varchar(150) NOT NULL,
  `nome_ficheiro_caminho` varchar(255) NOT NULL,
  `data_documento` date NOT NULL,
  `data_validade` date,
  `equipamento_id` int NOT NULL,
  `criado_em` timestamp DEFAULT (CURRENT_TIMESTAMP)
);

CREATE TABLE `utilizadores` (
  `id` int PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(50) UNIQUE NOT NULL,
  `password_hash` varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS `textos_interface` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `chave` VARCHAR(100) UNIQUE NOT NULL,
  `conteudo` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ============================================================================
-- 2. ÍNDICES E RESTRIÇÕES DE INTEGRIDADE (CHAVES ESTRANGEIRAS)
-- ============================================================================

CREATE UNIQUE INDEX `uq_localizacao` ON `localizaciones` (`edificio`, `piso`, `servico_departamento`, `sala_gabinete`);
CREATE UNIQUE INDEX `uq_fabricante_modelo_serie` ON `equipamentos` (`marca`, `modelo`, `numero_serie`);

ALTER TABLE `equipamentos` ADD FOREIGN KEY (`localizacao_id`) REFERENCES `localizaciones` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `equipamento_fornecedor` ADD FOREIGN KEY (`equipamento_id`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `equipamento_fornecedor` ADD FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedores` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE `garantias_contratos` ADD FOREIGN KEY (`entidade_responsavel_id`) REFERENCES `fornecedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `componentes_associados` ADD FOREIGN KEY (`equipamento_pai_id`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `documentacao` ADD FOREIGN KEY (`equipamento_id`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- NOTA: A FK que criava ciclo entre 'equipamentos' e 'garantias_contratos' foi removida.
-- Esta linha abaixo é a única necessária para estabelecer a relação 1:1 de forma correta:
ALTER TABLE `garantias_contratos` ADD FOREIGN KEY (`equipamento_id`) REFERENCES `equipamentos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


-- ============================================================================
-- 3. INSERÇÃO DOS DADOS (ORDEM DE DEPENDÊNCIA RESPEITADA)
-- ============================================================================

INSERT INTO utilizadores (username, password_hash) 
VALUES (
  'administrador', 
  '$2y$10$WRvqT5HHyT4JgXQx.E94Q.ZMpfZDAIi8mC6.1fKUjCjBmdDCq6WHK' 
); -- Hash BCRYPT gerada para: PasseSegura123!