<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Controle de Consumíveis</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#dcdcdc;
    padding:20px;
}

.folha{
    width:210mm;
    min-height:297mm;
    background:white;
    margin:auto;
    padding:20mm;
}

h1{
    text-align:center;
    margin-bottom:30px;
    font-size:28px;
    letter-spacing:2px;
}

.topo{
    display:flex;
    gap:30px;
    margin-bottom:30px;
}

.campo{
    flex:1;
}

.campo label{
    display:block;
    font-weight:bold;
    margin-bottom:10px;
    font-size:18px;
}

.linha-campo{
    border-bottom:2px solid #000;
    height:35px;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    border:2px solid #000;
    padding:12px;
    font-size:18px;
    background:#f1f1f1;
}

td{
    border:1px solid #000;
    height:45px;
}

.assinatura{
    margin-top:80px;
    text-align:center;
}

.assinatura .linha{
    width:350px;
    border-bottom:2px solid #000;
    margin:0 auto 10px auto;
    height:40px;
}

@media print{

    body{
        background:white;
        padding:0;
    }

    .folha{
        width:100%;
        min-height:100%;
        margin:0;
        box-shadow:none;
        padding:15mm;
    }

}

</style>
</head>

<body>

<div class="folha">

    <h1>CONTROLE DE CONSUMÍVEIS</h1>

    <div class="topo">

        <div class="campo">
            <label>NOME:</label>
            <div class="linha-campo"></div>
        </div>

        <div class="campo">
            <label>CPF:</label>
            <div class="linha-campo"></div>
        </div>

    </div>

    <table>

        <thead>
            <tr>
                <th>PRODUTO</th>
                <th>DATA</th>
                <th>QUANTIDADE</th>
            </tr>
        </thead>

        <tbody>

            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>
            <tr><td></td><td></td><td></td></tr>

        </tbody>

    </table>

    <div class="assinatura">

        <div class="linha"></div>
        <p>ASSINATURA</p>

    </div>

</div>

</body>
</html>