CREATE TABLE localizaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    edificio VARCHAR(50) NOT NULL,
    piso VARCHAR(20) NOT NULL,
    servico_departamento VARCHAR(100) NOT NULL, -- Ex: UCI, Urgência, Bloco Operatório
    sala_gabinete VARCHAR(50) NOT NULL,
    UNIQUE KEY uq_localizacao (edificio, piso, servico_departamento, sala_gabinete)
) ENGINE=InnoDB;

-- ============================================================================
-- 3. TABELA DE FORNECEDORES / ENTIDADES (Módulo de Fornecedores)
-- ============================================================================
CREATE TABLE fornecedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_empresa VARCHAR(150) NOT NULL,
    nif VARCHAR(20) NOT NULL UNIQUE,
    contacto_telefonico VARCHAR(20),
    email VARCHAR(100),
    morada TEXT,
    website VARCHAR(255),
    pessoa_contacto VARCHAR(100),
    telefone_pessoa_contacto VARCHAR(20),
    observacoes TEXT
) ENGINE=InnoDB;

-- ============================================================================
-- 4. TABELA DE EQUIPAMENTOS MÉDICOS (Módulo Core de Inventário)
-- ============================================================================
CREATE TABLE equipamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo_interno VARCHAR(50) NOT NULL UNIQUE, -- Código interno único de inventário
    designacao VARCHAR(150) NOT NULL,          -- Nome do equipamento (Ex: Ventilador)
    categoria ENUM(
        'Monitorização', 
        'Suporte de vida', 
        'Terapia', 
        'Diagnóstico', 
        'Laboratório', 
        'Esterilização', 
        'Reabilitação'
    ) NOT NULL,                                 -- Categorias controladas do enunciado
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    numero_serie VARCHAR(100) NOT NULL,
    fabricante VARCHAR(150) NOT NULL,
    data_aquisicao DATE NOT NULL,
    ano_fabrico INT NOT NULL,
    custo_aquisicao DECIMAL(10, 2) NOT NULL,
    tipo_entrada ENUM('Compra', 'Doação', 'Aluguer', 'Empréstimo') NOT NULL,
    estado_atual ENUM(
        'Ativo', 
        'Em manutenção', 
        'Inativo', 
        'Em calibração', 
        'Em quarentena', 
        'Abatido'
    ) NOT NULL,                                 -- Lista controlada obrigatória
    criticidade ENUM('Baixa', 'Média', 'Alta', 'Suporte de vida') NOT NULL, -- Lista controlada obrigatória
    localizacao_id INT NOT NULL,                -- Chave Estrangeira (Localização Atual)
    observacoes TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Restrição: Evitar números de série duplicados para o mesmo fabricante e modelo
    CONSTRAINT uq_fabricante_modelo_serie UNIQUE (marca, modelo, numero_serie),
    
    -- Chave Estrangeira para Localizações
    CONSTRAINT fk_equipamentos_localizacao FOREIGN KEY (localizacao_id) 
        REFERENCES localizaciones(id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================================
-- 5. TABELA DE LIGAÇÃO: EQUIPAMENTOS <-> FORNECEDORES (Relação N:M)
-- Um equipamento pode ter mais do que um fornecedor e um fornecedor vários equipamentos.
-- Permite atribuir os papéis exigidos (Fabricante, Assistência Técnica, etc.)
-- ============================================================================
CREATE TABLE equipamento_fornecedor (
    equipamento_id INT NOT NULL,
    fornecedor_id INT NOT NULL,
    tipo_fornecedor ENUM(
        'Fabricante', 
        'Distribuidor ou fornecedor comercial', 
        'Empresa de assistência técnica', 
        'Fornecedor de consumíveis ou acessórios'
    ) NOT NULL,                                 -- Classificação exigida no Módulo 11.3
    PRIMARY KEY (equipamento_id, fornecedor_id, tipo_fornecedor),
    
    CONSTRAINT fk_eq_forn_equipamento FOREIGN KEY (equipamento_id) 
        REFERENCES equipamentos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_eq_forn_fornecedor FOREIGN KEY (fornecedor_id) 
        REFERENCES fornecedores(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 6. TABELA DE GARANTIAS E CONTRATOS
-- Informações relativas ao ciclo de vida e cobertura legal/técnica
-- ============================================================================
CREATE TABLE garantias_contratos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipamento_id INT NOT NULL UNIQUE,          -- Relação 1:1 ou 1:N (Apoiada na ficha do equipamento)
    data_inicio_garantia DATE,
    data_fim_garantia DATE,
    tem_contrato_manutencao BOOLEAN DEFAULT FALSE,
    tipo_contrato VARCHAR(100),                 -- Ex: Total, Mão de Obra, Preventivo
    entidade_responsavel_id INT,                -- Fornecedor responsável pela assistência
    periodicidade VARCHAR(50),                  -- Ex: Semestral, Anual
    observacoes TEXT,
    
    CONSTRAINT fk_garantias_equipamento FOREIGN KEY (equipamento_id) 
        REFERENCES equipamentos(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_garantias_fornecedor FOREIGN KEY (entidade_responsavel_id) 
        REFERENCES fornecedores(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 7. TABELA DE COMPONENTES E ACESSÓRIOS (Relação Hierárquica)
-- Exemplo do enunciado: Unidade principal e os seus sensores/cabos periféricos
-- ============================================================================
CREATE TABLE componentes_associados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipamento_pai_id INT NOT NULL,            -- Associa o acessório ao equipamento principal
    codigo_componente VARCHAR(50) NOT NULL,      -- Ex: 04.002.01
    designacao_componente VARCHAR(150) NOT NULL, -- Ex: Sensor de oximetria (SpO2)
    numero_serie_componente VARCHAR(100),
    observacoes TEXT,
    
    CONSTRAINT fk_componentes_equipamento FOREIGN KEY (equipamento_pai_id) 
        REFERENCES equipamentos(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ============================================================================
-- 8. TABELA DE DOCUMENTAÇÃO MÓDULO (Gestão Documental)
-- Guarda os caminhos dos manuais, relatórios e calibrações
-- ============================================================================
CREATE TABLE documentacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_documento ENUM(
        'Manual de utilizador', 
        'Manual de serviço', 
        'Certificado de calibração', 
        'Contrato de manutenção', 
        'Fatura ou guia de aquisição', 
        'Declaração de conformidade', 
        'Relatório técnico'
    ) NOT NULL,                                 -- Tipos de documento definidos no âmbito
    nome_documento VARCHAR(150) NOT NULL,       -- Nome descritivo dado pelo utilizador
    nome_ficheiro_caminho VARCHAR(255) NOT NULL, -- Caminho do ficheiro para download/upload real
    data_documento DATE NOT NULL,
    data_validade DATE NULL,                    -- Aplicável a calibrações ou contratos temporários
    equipamento_id INT NOT NULL,                -- Rastreabilidade direta com o Dispositivo
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_documentacao_equipamento FOREIGN KEY (equipamento_id) 
        REFERENCES equipamentos(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;