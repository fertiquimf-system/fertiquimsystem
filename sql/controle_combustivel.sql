CREATE TABLE controle_combustivel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  veiculo_id INT NOT NULL,
  posto VARCHAR(100) NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  litros DECIMAL(10,2) NOT NULL,
  preco_litro DECIMAL(10,2) NOT NULL,
  km DECIMAL(10,1) NOT NULL,
  data DATETIME NOT NULL,
  usuario VARCHAR(50) NOT NULL,
  FOREIGN KEY (veiculo_id) REFERENCES frota_veiculos(id)
);
