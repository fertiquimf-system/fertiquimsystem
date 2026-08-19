# Fertiquim — Sistema de Gestão

Sistema web desenvolvido para gerenciamento das operações da **Fertiquim**, utilizando PHP, MySQL, HTML, CSS e JavaScript, executado localmente através do XAMPP.

O sistema foi desenvolvido com foco em facilitar o controle de estoque, depósitos, produtos, vendas, revendedores, clientes e demais processos internos da empresa.

---

## 📌 Tecnologias utilizadas

* **PHP** — Back-end e regras de negócio
* **MySQL** — Banco de dados
* **HTML5** — Estrutura das páginas
* **CSS3** — Estilização e interface
* **JavaScript** — Interações e funcionalidades dinâmicas
* **XAMPP** — Ambiente de desenvolvimento local
* **Apache** — Servidor web
* **phpMyAdmin** — Gerenciamento do banco de dados
* **Dompdf** — Geração de documentos PDF

---

## 📂 Estrutura do sistema

O projeto é executado dentro do diretório do XAMPP:

```text
C:\xampp\htdocs\fertiquim\
```

A estrutura pode conter módulos semelhantes a:

```text
fertiquim/
│
├── deposito/
├── vendas/
├── estoque/
├── clientes/
├── revendedores/
├── funcionarios/
├── balanca/
├── etiquetas/
├── drive/
├── login/
├── includes/
├── assets/
├── css/
├── js/
│
├── index.php
├── conexao.php
└── ...
```

> A estrutura pode variar conforme os módulos instalados e atualizações realizadas no sistema.

---

## 🗄️ Banco de dados

O sistema utiliza o **MySQL** para armazenamento das informações.

Banco principal:

```text
fertiquim
```

Entre as tabelas utilizadas no sistema estão:

```text
clientes
estoque
estoque_fertilizantes
inventario_funcionario
usuarios
vendas
balanca_entrada
balanca_saida
etiquetas
revenda
vistorias_veiculos
```

Algumas tabelas possuem relacionamentos entre funcionários, produtos, revendedores, vendas, estoque e demais processos.

### Importante

Antes de realizar alterações estruturais no banco de dados, recomenda-se realizar um **backup completo** pelo phpMyAdmin.

---

# 🚀 Instalação

## 1. Instalar o XAMPP

Instale o XAMPP no computador.

Por padrão:

```text
C:\xampp\
```

Os serviços necessários são:

* Apache
* MySQL

---

## 2. Copiar o sistema

Copie a pasta do projeto para:

```text
C:\xampp\htdocs\
```

Resultado esperado:

```text
C:\xampp\htdocs\fertiquim\
```

---

## 3. Iniciar o XAMPP

Abra o **XAMPP Control Panel** e inicie:

```text
Apache
MySQL
```

Ambos devem permanecer em execução para que o sistema funcione.

---

## 4. Criar o banco de dados

Abra o phpMyAdmin:

```text
http://localhost/phpmyadmin
```

Crie o banco:

```text
fertiquim
```

Depois importe o arquivo `.sql` contendo a estrutura e os dados do sistema.

---

# 🔐 Login

O sistema possui autenticação de usuários.

As informações de acesso são armazenadas no banco de dados, normalmente na tabela:

```text
usuarios
```

A autenticação utiliza sessões PHP para manter o usuário conectado enquanto navega pelo sistema.

---

# 📦 Estoque

O módulo de estoque é responsável pelo controle dos produtos cadastrados no sistema.

Entre as informações utilizadas estão:

* Código do material
* Nome do produto
* Preço unitário
* Quantidade
* Informações relacionadas ao estoque

Para fertilizantes, existe também a tabela:

```text
estoque_fertilizantes
```

permitindo um controle específico dos produtos comercializados pela Fertiquim.

---

# 🏭 Depósito

O módulo de depósito permite registrar e controlar movimentações relacionadas aos produtos armazenados.

As operações devem ser realizadas respeitando as chaves primárias e relacionamentos definidos no banco de dados.

### ⚠️ Atenção

Ao inserir registros manualmente, não deve ser informado um valor inválido ou duplicado para uma coluna definida como `PRIMARY KEY`.

Exemplo de erro:

```text
Duplicate entry '0' for key 'PRIMARY'
```

Esse erro normalmente indica que o sistema está tentando inserir novamente um registro com uma chave primária já existente.

---

# 💰 Vendas

O módulo de vendas registra as operações realizadas pelos revendedores.

O sistema utiliza a **matrícula do revendedor** para identificar o vendedor em determinadas consultas e filtros.

Exemplo conceitual:

```text
revenda.matricula
        ↓
vendas.revendedor
```

Isso permite apresentar somente as vendas pertencentes ao revendedor selecionado.

---

# 👨‍💼 Revendedores

O cadastro de revendedores possui informações utilizadas para identificar e controlar suas operações.

Um dos principais identificadores utilizados é:

```text
matricula
```

A matrícula deve ser tratada de forma consistente entre os módulos que utilizam o revendedor.

---

# ⚖️ Balança

O sistema possui módulos relacionados às operações de entrada e saída da balança:

```text
balanca_entrada
balanca_saida
```

Esses módulos podem ser utilizados para registrar movimentações de veículos e cargas.

---

# 🚛 Vistorias de veículos

O sistema também possui estrutura para controle de vistorias:

```text
vistorias_veiculos
```

Esse módulo permite registrar informações relacionadas aos veículos e seus processos de conferência.

---

# 🏷️ Etiquetas

O sistema possui gerenciamento de etiquetas.

A tabela utiliza informações como:

```text
etiqueta
cor_etiqueta
```

A cor pode ser utilizada para diferenciar etiquetas.

Atualmente existe uma lógica permitindo que determinados números de etiqueta sejam utilizados novamente quando a **cor for diferente**, conforme as regras implementadas no sistema.

---

# 📁 Drive / Arquivos

O sistema possui um módulo semelhante a um armazenamento interno de arquivos.

Entre suas funcionalidades estão:

* Upload de arquivos
* Criação de pastas
* Organização de documentos
* Proteção de pastas através de senha

Os arquivos são armazenados no computador servidor e o caminho de armazenamento deve ser configurado de acordo com a instalação utilizada.

---

# 🧾 Geração de PDF

O sistema utiliza o **Dompdf** para geração de documentos PDF.

A biblioteca deve estar corretamente instalada no projeto através do Composer ou disponibilizada na estrutura utilizada pelo sistema.

Exemplo de instalação:

```bash
composer require dompdf/dompdf
```

---

# 🌐 Acesso pela rede

Quando o sistema estiver sendo utilizado em outros computadores da mesma rede, o computador que executa o XAMPP funciona como servidor.

Os outros computadores devem acessar o endereço IP desse servidor.

Exemplo:

```text
http://192.168.0.100/fertiquim/
```

O firewall do Windows e as configurações do Apache devem permitir as conexões necessárias.

---

# ⚙️ Configuração do Apache

O Apache normalmente utiliza:

```text
Porta 80 → HTTP
Porta 443 → HTTPS
```

Caso essas portas estejam ocupadas por outro programa, será necessário alterar a configuração do Apache ou liberar as portas utilizadas.

---

# 🖥️ Inicialização automática

Para ambientes onde o computador funciona como servidor, o XAMPP pode ser configurado para iniciar automaticamente com o Windows.

Os serviços principais são:

```text
Apache
MySQL
```

Isso evita a necessidade de iniciar manualmente os serviços após cada reinicialização.

---

# 🔧 Manutenção

Antes de realizar alterações importantes:

1. Fazer backup do banco de dados.
2. Fazer backup dos arquivos do sistema.
3. Testar alterações em ambiente controlado.
4. Verificar erros do PHP.
5. Conferir os relacionamentos do banco de dados.
6. Validar as consultas SQL após alterações.

---

# 🐛 Solução de problemas

## Apache não inicia

Verifique se as portas:

```text
80
443
```

estão sendo utilizadas por outro programa.

Também é possível verificar os logs do Apache dentro da pasta:

```text
C:\xampp\apache\logs\
```

---

## MySQL não inicia

Verifique o status do serviço e os logs localizados em:

```text
C:\xampp\mysql\data\
```

Não apague arquivos da pasta `data` sem possuir um backup.

---

## Erro de conexão com banco

Verifique:

* MySQL está iniciado?
* Nome do banco está correto?
* Usuário está correto?
* Senha está correta?
* Host está correto?
* Porta do MySQL está correta?

Configuração local comum:

```text
Host: localhost
Banco: fertiquim
Usuário: root
Senha: 
```

> A configuração real deve ser conferida no arquivo de conexão utilizado pelo projeto.

---

## Erro `Duplicate entry '0' for key 'PRIMARY'`

Esse erro indica tentativa de inserir um valor duplicado em uma coluna definida como chave primária.

Verifique:

* `AUTO_INCREMENT`
* valor enviado pelo formulário
* consulta `INSERT`
* definição da chave primária
* existência de registros duplicados

Exemplo de estrutura esperada:

```sql
id INT NOT NULL AUTO_INCREMENT PRIMARY KEY
```

---

# 🔒 Segurança

Em ambiente de produção, recomenda-se:

* Não utilizar o usuário `root` do MySQL sem senha.
* Utilizar usuários específicos para a aplicação.
* Validar e sanitizar dados recebidos pelos formulários.
* Utilizar prepared statements nas consultas SQL.
* Proteger páginas que exigem autenticação.
* Evitar exposição direta de arquivos sensíveis.
* Manter backups periódicos.
* Restringir acesso ao servidor pela rede.

---

# 💾 Backup

Recomenda-se manter pelo menos:

```text
Backup do banco de dados
+
Backup dos arquivos PHP/HTML/CSS/JS
+
Backup dos arquivos enviados pelo sistema
```

Uma estrutura de backup pode ser:

```text
BACKUP/
│
├── banco/
│   └── fertiquim_YYYY-MM-DD.sql
│
├── sistema/
│   └── fertiquim_YYYY-MM-DD.zip
│
└── arquivos/
    └── uploads_YYYY-MM-DD.zip
```

---

# 📌 Boas práticas para desenvolvimento

Ao adicionar uma nova funcionalidade:

1. Criar ou alterar a estrutura do banco.
2. Criar o back-end PHP.
3. Criar a interface.
4. Implementar validações.
5. Testar inserção.
6. Testar edição.
7. Testar exclusão.
8. Testar filtros e pesquisas.
9. Testar diferentes usuários.
10. Fazer backup após a implementação.

---

# 👨‍💻 Desenvolvimento

O sistema foi desenvolvido de forma modular, permitindo que novos módulos sejam adicionados conforme as necessidades da empresa.

A arquitetura atual permite futuras implementações como:

* Dashboard aprimorado
* Relatórios
* Controle financeiro
* Mais filtros de estoque
* Histórico de movimentações
* Integração com NF-e
* Melhorias de permissões de usuários
* Relatórios em PDF
* Integrações com outros sistemas
* Acesso pela rede local
* Automatização de backups

---

# 📄 Licença

Sistema desenvolvido para uso interno da **Fertiquim**.

A distribuição, cópia ou utilização do código fora do ambiente autorizado deve ser realizada somente mediante autorização dos responsáveis pelo projeto.

---

## 📞 Observação

Este README deve ser atualizado sempre que novos módulos, tabelas, integrações ou alterações importantes forem adicionados ao sistema.

**Fertiquim — Sistema de Gestão Interna**
