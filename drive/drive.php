<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

$usuario = $_SESSION['nome_usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Fertiquim - Drive</title>

<link rel="stylesheet" href="../css/estilo.css">
<link rel="stylesheet" href="../css/drive.css">

</head>
<body>

<?php include '../base/cabecalho.php'; ?>

<div class="container">

<!-- HEADER -->
<div class="drive-header">

    <div>
        <div class="titulo">📁 Drive Fertiquim</div>
        <div class="subtitulo">Bem-vindo, <?php echo htmlspecialchars($usuario); ?></div>
    </div>

    <div class="acoes-drive">

        <input type="text" id="pesquisa" placeholder="Pesquisar arquivos e pastas..." onkeyup="pesquisar()">

        <button class="btn-drive" onclick="abrirModalPasta()">📁 Nova Pasta</button>
        <button class="btn-drive upload" onclick="abrirModalUpload()">⬆ Upload</button>

    </div>

</div>

<!-- PASTAS -->
<div id="pastas" class="pastas-grid">
    <div class="vazio">Carregando pastas...</div>
</div>

<!-- ARQUIVOS -->
<div class="arquivos-box">
    <div class="titulo-arquivos">Arquivos</div>
    <div id="arquivos">
        <div class="vazio">Selecione uma pasta</div>
    </div>
</div>

</div>

<!-- MODAL PASTA -->
<div id="modalPasta" class="modal-bg">
    <div class="modal">

        <h3>📁 Nova Pasta</h3>

        <input type="text" id="nomePasta" placeholder="Nome da pasta">

        <div class="modal-actions">
            <button class="btn-danger" onclick="fecharModalPasta()">Cancelar</button>
            <button class="btn-success" onclick="criarPasta()">Criar</button>
        </div>

    </div>
</div>

<!-- MODAL UPLOAD -->
<div id="modalUpload" class="modal-bg">
    <div class="modal">

        <h3>⬆ Upload</h3>

        <input type="file" id="arquivoUpload">
        <input type="hidden" id="pastaSelecionada">

        <div class="modal-actions">
            <button class="btn-danger" onclick="fecharModalUpload()">Cancelar</button>
            <button class="btn-primary" onclick="uploadArquivo()">Enviar</button>
        </div>

    </div>
</div>

<!-- JS COMPLETO -->
<script>

// ================= MODAIS =================
function abrirModalPasta(){
    document.getElementById('modalPasta').style.display='flex';
}

function fecharModalPasta(){
    document.getElementById('modalPasta').style.display='none';
}

function abrirModalUpload(){
    document.getElementById('modalUpload').style.display='flex';
}

function fecharModalUpload(){
    document.getElementById('modalUpload').style.display='none';
}

// ================= CRIAR PASTA =================
function criarPasta() {

    let nome = document.getElementById('nomePasta').value;

    console.log("Nome enviado:", nome);

    fetch('criar_pasta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'nome=' + encodeURIComponent(nome)
    })
    .then(r => r.json())
    .then(res => {
        console.log(res);
        alert(res.mensagem);
    })
    .catch(err => console.error(err));

}

// ================= UPLOAD =================
function uploadArquivo(){

    let file = document.getElementById('arquivoUpload').files[0];
    let pasta = document.getElementById('pastaSelecionada').value;

    let form = new FormData();
    form.append('arquivo', file);
    form.append('pasta_id', pasta);

    fetch('upload.php',{
        method:'POST',
        body:form
    })
    .then(r=>r.json())
    .then(res=>{

        alert(res.mensagem);

        if(res.status){
            fecharModalUpload();
            carregarArquivos(pasta);
        }

    });

}

// ================= CARREGAR PASTAS =================
function carregarPastas(){

    fetch('listar_pastas.php')
    .then(r=>r.json())
    .then(res=>{

        let box = document.getElementById('pastas');
        box.innerHTML='';

        if(res.pastas.length == 0){
            box.innerHTML = "<div class='vazio'>Nenhuma pasta</div>";
            return;
        }

        res.pastas.forEach(p=>{

            box.innerHTML += `
                <div class="pasta" onclick="abrirPasta(${p.id})">

                    <div class="icone">📁</div>
                    <span>${p.nome}</span>

                    <div style="margin-top:10px; display:flex; justify-content:center; gap:8px;">

                        <button onclick="event.stopPropagation(); renomearPasta(${p.id})">✏</button>
                        <button onclick="event.stopPropagation(); excluirPasta(${p.id})">🗑</button>

                    </div>

                </div>
            `;

        });

    });

}

// ================= ABRIR PASTA =================
function abrirPasta(id){

    document.getElementById('pastaSelecionada').value = id;
    carregarArquivos(id);

}

// ================= ARQUIVOS =================
function carregarArquivos(pasta){

    fetch('listar_arquivos.php?pasta_id='+pasta)
    .then(r=>r.json())
    .then(res=>{

        let box = document.getElementById('arquivos');
        box.innerHTML='';

        if(res.arquivos.length == 0){
            box.innerHTML = "<div class='vazio'>Nenhum arquivo</div>";
            return;
        }

        res.arquivos.forEach(a=>{

            box.innerHTML += `
                <div class="arquivo-item">
                    <div>📄 ${a.nome}</div>
                    <div>${a.tamanho}</div>
                </div>
            `;

        });

    });

}

// ================= PESQUISA =================
function pesquisar(){

    let q = document.getElementById('pesquisa').value;

    if(q.length < 2){
        carregarPastas();
        return;
    }

    fetch('pesquisar.php?q='+q)
    .then(r=>r.json())
    .then(res=>{

        let box = document.getElementById('pastas');
        box.innerHTML='';

        res.pastas.forEach(p=>{

            box.innerHTML += `
                <div class="pasta">
                    <div class="icone">📁</div>
                    <span>${p.nome}</span>
                </div>
            `;

        });

    });

}

// INIT
window.onload = carregarPastas;


</script>

<?php include '../base/rodape.php'; ?>

</body>
</html>