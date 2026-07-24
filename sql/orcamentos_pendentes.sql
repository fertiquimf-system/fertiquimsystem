CREATE TABLE orcamentos_pendentes (
    id_orcamento INT AUTO_INCREMENT PRIMARY KEY,
    numero_orcamento VARCHAR(50) NOT NULL,
    cliente VARCHAR(255) NOT NULL,
    cpf_cnpj VARCHAR(20),
    telefone VARCHAR(20),
    endereco VARCHAR(255),
    cep VARCHAR(15),
    responsavel VARCHAR(255),
    data_orcamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE itens_orcamento_pendentes (
    id_item INT AUTO_INCREMENT PRIMARY KEY,
    id_orcamento INT NOT NULL,
    produto VARCHAR(255) NOT NULL,
    quantidade INT NOT NULL,
    unidade VARCHAR(50),
    tipo VARCHAR(50),
    estoque VARCHAR(50),
    valor_unitario DECIMAL(10,2),
    valor_total DECIMAL(10,2),
    FOREIGN KEY (id_orcamento) REFERENCES orcamentos_pendentes(id_orcamento) ON DELETE CASCADE
);
