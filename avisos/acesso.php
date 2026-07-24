<?php
// Se quiser mandar uma mensagem dinâmica, você pode usar:
// $mensagem = $mensagem ?? "Acesso negado.";
?>

<style>
  /* Fundo escuro */
  #popup-bg {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.55);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 99999;
  }

  /* Caixa do popup */
  #popup-box {
    background: white;
    width: 350px;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
    font-family: Arial, sans-serif;
    box-shadow: 0 0 15px rgba(0,0,0,0.4);
    animation: fadeIn 0.3s ease-out;
  }

  #popup-box h3 {
    margin-top: 0;
  }

  /* Botão */
  #popup-box button {
    margin-top: 15px;
    padding: 8px 18px;
    background: #225B0B;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
  }

  #popup-box button:hover {
    background: #163f07;
  }

  /* Animação */
  @keyframes fadeIn {
    from {
      transform: scale(0.85);
      opacity: 0;
    }
    to {
      transform: scale(1);
      opacity: 1;
    }
  }
</style>

<!-- Estrutura do popup -->
<div id="popup-bg">
  <div id="popup-box">
    <h3 id="popup-titulo">Aviso</h3>
    <p id="popup-msg">Mensagem aqui</p>
    <button onclick="fecharPopup()">OK</button>
  </div>
</div>

<script>
  function abrirPopup(mensagem = "Acesso negado.", titulo = "Aviso") {
    document.getElementById("popup-msg").innerText = mensagem;
    document.getElementById("popup-titulo").innerText = titulo;
    document.getElementById("popup-bg").style.display = "flex";
  }

  function fecharPopup() {
    document.getElementById("popup-bg").style.display = "none";
  }
</script>
