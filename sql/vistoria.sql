CREATE TABLE vistorias_veiculos (
  id INT AUTO_INCREMENT PRIMARY KEY,

  veiculo_id INT NOT NULL,
  placa VARCHAR(20),
  modelo VARCHAR(100),
  marca VARCHAR(100),
  ano INT,
  km_atual INT,

  motorista VARCHAR(100),
  cnh VARCHAR(50),
  validade_cnh DATE,

  -- ITENS DE VISTORIA
  luz_alta ENUM('BOM','RUIM','NAO_POSSUI'),
  luz_baixa ENUM('BOM','RUIM','NAO_POSSUI'),
  setas_dianteiras ENUM('BOM','RUIM','NAO_POSSUI'),
  setas_traseiras ENUM('BOM','RUIM','NAO_POSSUI'),
  luz_re ENUM('BOM','RUIM','NAO_POSSUI'),
  luz_freio ENUM('BOM','RUIM','NAO_POSSUI'),
  meia_luz ENUM('BOM','RUIM','NAO_POSSUI'),
  limpador_vidros ENUM('BOM','RUIM','NAO_POSSUI'),
  pneus ENUM('BOM','RUIM','NAO_POSSUI'),
  estepe ENUM('BOM','RUIM','NAO_POSSUI'),
  cintos_seguranca ENUM('BOM','RUIM','NAO_POSSUI'),
  lataria_geral ENUM('BOM','RUIM','NAO_POSSUI'),
  limpeza_veiculo ENUM('BOM','RUIM','NAO_POSSUI'),
  para_brisa ENUM('BOM','RUIM','NAO_POSSUI'),
  oleo_motor ENUM('BOM','RUIM','NAO_POSSUI'),
  agua_radiador ENUM('BOM','RUIM','NAO_POSSUI'),
  vidros_travas ENUM('BOM','RUIM','NAO_POSSUI'),
  barulhos_anormais ENUM('BOM','RUIM','NAO_POSSUI'),

  observacoes TEXT,
  responsavel_vistoria VARCHAR(100),

  data_vistoria DATETIME DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (veiculo_id) REFERENCES frota_veiculos(id)
);
