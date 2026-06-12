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

-- 3.1. Localizações (Independente)
INSERT INTO localizaciones (edificio, piso, servico_departamento, sala_gabinete) VALUES 
('Edifício Central', 'Piso 0', 'Urgência Geral', 'Sala de Reanimação 1'),
('Edifício Central', 'Piso 1', 'Bloco Operatório Central', 'Sala de Operações 3'),
('Edifício Norte', 'Piso 2', 'Unidade de Cuidados Intensivos (UCI)', 'Box 5'),
('Edifício Norte', 'Piso 0', 'Imagiologia', 'Sala de Tomografia (TAC)');

-- 3.2. Fornecedores (Independente)
INSERT INTO fornecedores (nome_empresa, nif, contacto_telefonico, email, morada, website, pessoa_contacto, telefone_pessoa_contacto, observacoes) VALUES 
('MedTech Portugal S.A.', '501234567', '210000000', 'geral@medtech.pt', 'Av. da República, 1000, Lisboa', 'www.medtech.pt', 'Eng. Pedro Mendes', '910000001', 'Fornecedor principal de ventiladores e monitores.'),
('BioReparação Soluções Clínicas', '502987654', '220000000', 'suporte@bioreparacao.pt', 'Rua de Camões, 450, Porto', 'www.bioreparacao.pt', 'Cláudia Santos', '920000002', 'Empresa externa contratada para assistência técnica multimarca.'),
('Philips Healthcare Portugal', '503111222', '211111222', 'hospital.pt@philips.com', 'Parque das Nações, Edifício Philips, Lisboa', 'www.philips.pt', 'Dr. Ricardo Jorge', '933333444', 'Fabricante oficial de equipamentos de diagnóstico por imagem.');

-- 3.3. Equipamentos (Depende de Localizações)
INSERT INTO equipamentos (codigo_interno, designacao, categoria, marca, modelo, numero_serie, fabricante, data_aquisicao, ano_fabrico, custo_aquisicao, tipo_entrada, estado_atual, criticidade, localizacao_id, observacoes) VALUES 
('EQ-VENT-001', 'Ventilador Pulmonar de Alta Gama', 'Suporte de vida', 'Puritan Bennett', 'PB980', 'SN-PB980-9948', 'Covidien / Medtronic', '2025-01-15', 2024, 24500.00, 'Compra', 'Ativo', 'Suporte de vida', 1, 'Equipamento crítico para ventilação mecânica invasiva.'),
('EQ-MON-042', 'Monitor Multiparamétrico de Sinais Vitais', 'Monitorização', 'Mindray', 'BeneVision N17', 'SN-MY-774411', 'Mindray Bio-Medical', '2025-03-10', 2024, 8900.00, 'Compra', 'Ativo', 'Alta', 3, 'Ecrã tátil com visualização integrada de ECG, SpO2, PNI e PI.'),
('EQ-DESF-015', 'Desfibrilhador Bifásico Monitor', 'Terapia', 'Zoll', 'R Series', 'SN-ZOLL-8832', 'Zoll Medical Corporation', '2024-11-01', 2024, 11200.00, 'Compra', 'Em manutenção', 'Alta', 2, 'Enviado para calibração anual das pás e bateria.');

-- 3.4. Ligação Equipamento-Fornecedor (Depende de Equipamentos e Fornecedores)
INSERT INTO equipamento_fornecedor (equipamento_id, fornecedor_id, tipo_fornecedor) VALUES 
(1, 1, 'Distribuidor ou fornecedor comercial'),
(2, 1, 'Distribuidor ou fornecedor comercial'),
(2, 2, 'Empresa de assistência técnica'),
(3, 3, 'Empresa de assistência técnica');

-- 3.5. Garantias e Contratos (Depende de Equipamentos e Fornecedores)
INSERT INTO garantias_contratos (equipamento_id, data_inicio_garantia, data_fim_garantia, tem_contrato_manutencao, tipo_contrato, entidade_responsavel_id, periodicidade, observacoes) VALUES 
(1, '2025-01-15', '2027-01-15', TRUE, 'Manutenção Preventiva e Corretiva Integral', 1, 'Semestral', 'Contrato inclui substituição de kits de filtros de 6 em 6 meses.'),
(2, '2025-03-10', '2026-03-10', TRUE, 'Apenas Mão de Obra', 2, 'Anual', 'Peças de desgaste pagas individualmente.');

-- 3.6. Componentes Associados (Depende de Equipamentos)
INSERT INTO componentes_associados (equipamento_pai_id, codigo_componente, designacao_componente, numero_serie_componente, observacoes) VALUES 
(2, 'ACC-MON-01', 'Cabo de ECG de 5 Condutores', 'SN-CB-ECG-112', 'Acessório crítico reutilizável.'),
(2, 'ACC-MON-02', 'Sensor de Oximetria de Pulso (SpO2) de Dedo', 'SN-SENS-SPO2-90', 'Sensor compatível com tecnologia Nellcor.'),
(2, 'ACC-MON-03', 'Manga de Pressão Arterial Não Invasiva (PNI) Adulto', NULL, 'Componente consumível de desgaste rápido.');

-- 3.7. Documentação (Depende de Equipamentos)
INSERT INTO documentacao (tipo_documento, nome_documento, nome_ficheiro_caminho, data_documento, data_validade, equipamento_id) VALUES 
('Manual de utilizador', 'Manual do Operador PB980 - PT', 'uploads/manuais/manual_pb980_pt.pdf', '2024-05-20', NULL, 1),
('Certificado de calibração', 'Certificado de Calibração Inicial - Ventilador', 'uploads/certificados/calib_init_vent_01.pdf', '2025-01-12', '2026-01-12', 1),
('Manual de serviço', 'Mindray BeneVision Service Manual', 'uploads/manuais/service_manual_n17_en.pdf', '2023-11-15', NULL, 2),
('Relatório técnico', 'Relatório de Avaria de Bateria - Desfibrilhador', 'uploads/relatorios/rel_avaria_desf_15.pdf', '2026-05-18', NULL, 3);

--3.8. Utilizadores (Independente)
INSERT INTO utilizadores (username, password_hash) 
VALUES (
  'administrador', 
  '$2y$10$WRvqT5HHyT4JgXQx.E94Q.ZMpfZDAIi8mC6.1fKUjCjBmdDCq6WHK' 
); -- Hash BCRYPT gerada para: PasseSegura123!