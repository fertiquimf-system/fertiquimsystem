<?php
require_once '../conexaohost/conexao.php';
session_start();

if (!isset($_SESSION['nome_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Treinamentos - Fertiquim</title>
  <link rel="stylesheet" href="../css/estilo.css" />
  <style>
    .grid-videos {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 20px;
    }
    .video-card {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.15);
      padding: 15px;
      text-align: center;
      transition: transform 0.2s ease-in-out;
    }
    .video-card:hover {
      transform: scale(1.03);
    }
    .video-card h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }
    video {
      width: 100%;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <header>
    <h1>FERTIQUIM Fertilizantes</h1>
    <nav>
      <a href="../pginicial/pginicial.php">Início</a>
      <a href="../pglogin/pglogin.php">Sair</a>
    </nav>
  </header>

  <div class="container">
    <h2 class="titulo">Treinamentos Disponíveis</h2>

    <div class="grid-videos">
      <div class="video-card">
        <h3>Segurança no Trabalho</h3>
        <video controls>
          <source src="../videos/seguranca.mp4" type="video/mp4">
          Seu navegador não suporta vídeo.
        </video>
      </div>

      <div class="video-card">
        <h3>Manuseio de Fertilizantes</h3>
        <video controls>
          <source src="../videos/manuseio.mp4" type="video/mp4">
          Seu navegador não suporta vídeo.
        </video>
      </div>

      <div class="video-card">
        <h3>Uso de EPI e EPC</h3>
        <video controls>
          <source src="../videos/epc-epi.mp4" type="video/mp4">
          Seu navegador não suporta vídeo.
        </video>
      </div>

      <div class="video-card">
        <h3>Boas Práticas no Carregamento</h3>
        <video controls>
          <source src="../videos/carregamento.mp4" type="video/mp4">
          Seu navegador não suporta vídeo.
        </video>
      </div>
    </div>
  </div>

  <footer>
    &copy; 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
  </footer>
</body>
</html>
