CREATE TABLE estoque_fertilizantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_produto VARCHAR(100) NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    unidade VARCHAR(20) DEFAULT 'tonelada',
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario VARCHAR(100) NOT NULL
);
