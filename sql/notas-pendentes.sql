CREATE TABLE nf_pendente (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero_nf VARCHAR(50),
  nome_fantasia VARCHAR(100),
  cnpj VARCHAR(20),
  telefone VARCHAR(20),
  endereco VARCHAR(200),
  cep VARCHAR(10),
  data_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE fertilizantes_pendentes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nf_id INT,
  nome VARCHAR(100),
  quantidade DECIMAL(10,2),
  unidade VARCHAR(20),
  FOREIGN KEY (nf_id) REFERENCES nf_pendente(id)
);
