<?php
function enviarCorreo($email, $asunto, $mensaje) {
    $headers = "From: ramoslozanorodrigoalejandro@gmail.com";
    mail($email, $asunto, $mensaje, $headers);
}
?>
