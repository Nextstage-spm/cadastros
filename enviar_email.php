<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = $_POST['email'];
    $subject = $_POST['assunto'];
    $message = $_POST['mensagem'];
    $headers = "From: seuemail@seudominio.com\r\n";
    
    if (mail($to, $subject, $message, $headers)) {
        echo "E-mail enviado com sucesso!";
    } else {
        echo "Falha ao enviar o e-mail.";
    }
}
?>
