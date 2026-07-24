-- Tabela principal de vendas
CREATE TABLE vendas (
    id_venda INT AUTO_INCREMENT PRIMARY KEY,
    numero_venda INT NOT NULL UNIQUE, -- número sequencial da venda
    cliente VARCHAR(150) NOT NULL,
    cpf_cnpj VARCHAR(20) NOT NULL,
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    cep VARCHAR(15),
    responsavel VARCHAR(100),
    data_venda DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de itens da venda
CREATE TABLE itens_venda (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_venda INT NOT NULL,
    produto VARCHAR(150) NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    unidade VARCHAR(20),
    tipo VARCHAR(50),
    estoque DECIMAL(10,2), -- estoque atual no momento da venda (se quiser controlar)
    valor_unitario DECIMAL(10,2),
    valor_total DECIMAL(10,2),
    FOREIGN KEY (id_venda) REFERENCES vendas(id_venda) ON DELETE CASCADE
);
