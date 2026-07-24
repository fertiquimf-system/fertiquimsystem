<?php
// rodape.php
?>

<footer class="rodape">
    © 2025 Fertiquim Fertilizantes. Todos os direitos reservados.
</footer>

<?php if (isset($_SESSION['nome_usuario']) && isset($_SESSION['funcao'])): ?>
  <div class="usuario-logado">
    <?php echo htmlspecialchars($_SESSION['nome_usuario']); ?> 
    <small>(<?php echo htmlspecialchars($_SESSION['funcao']); ?>)</small>
  </div>
<?php endif; ?>

</body>
</html>
