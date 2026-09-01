ALTER TABLE vendas
ADD valor_pago DECIMAL(10,2) NOT NULL DEFAULT 0,
ADD saldo DECIMAL(10,2) NOT NULL DEFAULT 0;


CREATE TABLE pagamentos_venda (
    id_pagamento INT AUTO_INCREMENT PRIMARY KEY,

    id_venda INT NOT NULL,

    valor_pago DECIMAL(10,2) NOT NULL,

    data_pagamento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    forma_pagamento ENUM(
        'Pix',
        'Dinheiro',
        'Cartão de Crédito',
        'Cartão de Débito',
        'Cheque',
        'Transferência',
        'Boleto',
        'Outro'
    ) DEFAULT NULL,

    usuario VARCHAR(100) DEFAULT NULL,

    observacao TEXT DEFAULT NULL,

    CONSTRAINT fk_pagamento_venda
        FOREIGN KEY (id_venda)
        REFERENCES vendas(id_venda)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);

ALTER TABLE vendas
MODIFY COLUMN status ENUM('pendente','parcial','aprovada')
NOT NULL DEFAULT 'pendente';