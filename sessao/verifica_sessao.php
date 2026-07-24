<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include '../avisos/acesso.php';

// Tempo máximo de inatividade em segundos (30 minutos)
$tempo_max = 1800;

// Verifica expiração de sessão
if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso']) > $tempo_max) {
    session_unset();
    session_destroy();
    header("Location: ../pglogin/pglogin.php?expirado=1");
    exit;
}

// Atualiza o horário do último acesso
$_SESSION['ultimo_acesso'] = time();

// Verifica se está logado
if (!isset($_SESSION['nome_usuario']) || !isset($_SESSION['funcao_usuario'])) {
    header("Location: ../pglogin/pglogin.php");
    exit;
}

// FUNÇÃO: Restringe acesso por função e abre POPUP no lugar do alert
function restringirAcesso(array $funcoesPermitidas) {
    $usuarioFuncao = $_SESSION['funcao_usuario'] ?? '';

    if (!in_array($usuarioFuncao, $funcoesPermitidas)) {

        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                abrirPopup(
                    'Você não tem permissão para acessar esta área.',
                    'Acesso Negado'
                );
            });

            // Redireciona automaticamente após 2.5 segundos
            setTimeout(() => {
                window.location.href = '../pginicial/pginicial.php';
            }, 2500);
        </script>";

        exit;
    }
}
?>
