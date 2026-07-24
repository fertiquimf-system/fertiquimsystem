CREATE TABLE movimentacao_saida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade DECIMAL(10,2) NOT NULL,
    data_saida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario VARCHAR(100) NOT NULL,
    FOREIGN KEY (ticket_id) REFERENCES balanca_saida(id),
    FOREIGN KEY (produto_id) REFERENCES deposito(id)
);

ALTER TABLE movimentacao_saida
  ADD COLUMN data_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
