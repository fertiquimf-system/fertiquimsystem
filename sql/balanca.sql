CREATE TABLE balanca_entrada (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100),
    placa VARCHAR(20),
    motorista VARCHAR(100),
    peso_entrada INT,
    data_entrada DATETIME
);

CREATE TABLE balanca_saida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    placa VARCHAR(20),
    peso_saida INT,
    data_saida DATETIME
);

ALTER TABLE balanca_saida ADD COLUMN destino VARCHAR(255) AFTER peso_saida;
ALTER TABLE balanca_entrada ADD COLUMN telefone VARCHAR(20) AFTER cpf_motorista;