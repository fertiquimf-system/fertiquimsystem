<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Página em Manutenção</title>
  <style>
    body {
      background: #f0f2f5;
      font-family: Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .box {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      max-width: 400px;
      width: 100%;
    }
    .box h1 {
      font-size: 28px;
      margin-bottom: 15px;
      color: #333;
    }
    .box p {
      font-size: 16px;
      color: #555;
      margin-bottom: 20px;
    }
    .spinner {
      border: 6px solid #f3f3f3;
      border-top: 6px solid #4caf50;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      margin: 20px auto;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .btn-home {
      display: inline-block;
      background-color: #4caf50;
      color: #fff;
      padding: 10px 20px;
      border-radius: 6px;
      text-decoration: none;
      font-size: 14px;
      transition: background 0.3s;
    }
    .btn-home:hover {
      background-color: #43a047;
    }
    .footer {
      font-size: 12px;
      color: #888;
      margin-top: 15px;
    }
  </style>
</head>
<body>
  <div class="box">
    <h1>🚧 Em Manutenção</h1>
    <p>Estamos trabalhando para melhorar esta área.<br>
       Por favor, volte em alguns instantes.</p>
    <div class="spinner"></div>
    
    <a href="../pginicial/pginicial.php" class="btn-home">Ir para Página Inicial</a>

    <div class="footer">© <?php echo date("Y"); ?> - Fertiquim Fertilizantes LTDA.</div>
  </div>
</body>
</html>
