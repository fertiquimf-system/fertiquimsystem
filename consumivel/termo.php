<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Termo de Entrega de EPI</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
}

body{
    background:#e9edf2;
    padding:40px;
}

.documento{
    max-width:900px;
    margin:auto;
    background:#ffffff;
    padding:60px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    border-top:8px solid #0f172a;
}

.topo{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:50px;
    border-bottom:2px solid #e5e7eb;
    padding-bottom:25px;
}

.logo{
    font-size:28px;
    font-weight:700;
    color:#0f172a;
}

.info-empresa{
    text-align:right;
    color:#555;
    font-size:14px;
    line-height:1.6;
}

.titulo{
    text-align:center;
    margin-bottom:40px;
}

.titulo h1{
    font-size:32px;
    color:#0f172a;
    margin-bottom:10px;
}

.titulo p{
    color:#666;
    font-size:15px;
}

.funcionario{
    background:#f8fafc;
    border-left:5px solid #0f172a;
    padding:18px;
    margin-bottom:35px;
    border-radius:8px;
}

label{
    font-weight:600;
    color:#222;
    display:block;
    margin-bottom:8px;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:16px;
    margin-bottom:15px;
}

.conteudo p{
    font-size:18px;
    line-height:1.9;
    color:#333;
    text-align:justify;
    margin-bottom:25px;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#0f172a;
    color:#fff;
    padding:14px;
    text-align:left;
}

td{
    border:1px solid #d1d5db;
    padding:10px;
}

td input{
    margin:0;
    border:none;
    width:100%;
    background:transparent;
}

tr:nth-child(even){
    background:#f9fafb;
}

.botoes{
    display:flex;
    gap:15px;
    margin-top:25px;
}

button{
    padding:14px 22px;
    border:none;
    border-radius:8px;
    font-size:15px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

.btn-add{
    background:#0f172a;
    color:#fff;
}

.btn-add:hover{
    background:#1e293b;
}

.btn-remove{
    background:#dc2626;
    color:#fff;
}

.btn-remove:hover{
    background:#b91c1c;
}

.btn-print{
    background:#16a34a;
    color:#fff;
}

.btn-print:hover{
    background:#15803d;
}

.data{
    margin-top:60px;
    margin-bottom:70px;
    font-size:17px;
}

.assinaturas{
    display:flex;
    justify-content:space-between;
    gap:50px;
}

.assinatura{
    width:45%;
    text-align:center;
}

.assinatura .linha{
    border-top:1.5px solid #000;
    margin-bottom:10px;
    padding-top:10px;
    font-weight:600;
}

.footer{
    margin-top:60px;
    text-align:center;
    font-size:13px;
    color:#777;
    border-top:1px solid #ddd;
    padding-top:20px;
}

@media print{

    body{
        background:#fff;
        padding:0;
    }

    .documento{
        box-shadow:none;
        border-radius:0;
    }

    .botoes{
        display:none;
    }

    input{
        border:none;
        padding:0;
    }

}

</style>
</head>

<body>

<div class="documento">

    <div class="topo">

        <div class="logo">
            EMPRESA
        </div>

        <div class="info-empresa">
            Segurança do Trabalho<br>
            Controle de Entrega de EPI<br>
            Documento Interno
        </div>

    </div>

    <div class="titulo">
        <h1>TERMO DE ENTREGA DE EPI</h1>
        <p>Equipamentos de Proteção Individual</p>
    </div>

    <div class="funcionario">

        <label>Nome do Funcionário</label>
        <input type="text" placeholder="Digite o nome do funcionário">

        <label>CPF</label>
        <input type="text" placeholder="Digite o CPF">

    </div>

    <div class="conteudo">

        <p>
            Declaro para os devidos fins que recebi da empresa os Equipamentos de Proteção Individual (EPI’s) abaixo relacionados, em perfeitas condições de uso, comprometendo-me a utilizá-los corretamente durante minhas atividades profissionais.
        </p>

        <table id="tabelaEPI">

            <thead>
                <tr>
                    <th width="10%">Item</th>
                    <th>Equipamento</th>
                    <th width="20%">Quantidade</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>01</td>
                    <td><input type="text" placeholder="Nome do equipamento"></td>
                    <td><input type="text" placeholder="Quantidade"></td>
                </tr>

            </tbody>

        </table>

        <div class="botoes">

            <button class="btn-add" onclick="adicionarLinha()">
                + Adicionar Linha
            </button>

            <button class="btn-remove" onclick="removerLinha()">
                - Remover Linha
            </button>

            <button class="btn-print" onclick="window.print()">
                🖨 Imprimir Documento
            </button>

        </div>

    </div>

    <div class="data">
        Data: ________ / ________ / ______________
    </div>

    <div class="assinaturas">

        <div class="assinatura">
            <div class="linha"></div>
            Assinatura do Funcionário
        </div>

        <div class="assinatura">
            <div class="linha"></div>
            Assinatura da Empresa
        </div>

    </div>

    <div class="footer">
        Documento gerado para controle interno de entrega de EPI.
    </div>

</div>

<script>

function adicionarLinha(){

    let tabela = document.getElementById("tabelaEPI").getElementsByTagName('tbody')[0];

    let numeroLinha = tabela.rows.length + 1;

    let novaLinha = tabela.insertRow();

    let coluna1 = novaLinha.insertCell(0);
    let coluna2 = novaLinha.insertCell(1);
    let coluna3 = novaLinha.insertCell(2);

    coluna1.innerHTML = String(numeroLinha).padStart(2, '0');

    coluna2.innerHTML = `
        <input type="text" placeholder="Nome do equipamento">
    `;

    coluna3.innerHTML = `
        <input type="text" placeholder="Quantidade">
    `;
}

function removerLinha(){

    let tabela = document.getElementById("tabelaEPI").getElementsByTagName('tbody')[0];

    if(tabela.rows.length > 1){
        tabela.deleteRow(-1);
    }

}

</script>

</body>
</html>