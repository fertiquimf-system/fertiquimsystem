<?php
// Configuração da câmera
$ip = "192.168.0.21";
$porta = "";
$usuario = "admin";
$senha = "Nippon@123";

// URL do snapshot
$url = "http://$ip:$porta/cgi-bin/snapshot.cgi";

// Inicializa cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "$usuario:$senha");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

// Executa requisição
$data = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Se deu certo, devolve a imagem
if ($httpCode == 200 && $data !== false) {
    header("Content-Type: image/jpeg");
    echo $data;
} else {
    header("Content-Type: text/plain; charset=utf-8");
    echo "❌ Erro ao acessar a câmera em $url";
}
