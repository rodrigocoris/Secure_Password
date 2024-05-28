<?php
require 'send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "registro_usuarios");

    if ($conn->connect_error) {
        echo "Conexión fallida: " . $conn->connect_error;
        exit;
    }

    // Comprobar si el correo electrónico existe
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Generar un token
        $token = bin2hex(random_bytes(50));

        // Insertar el token en la base de datos
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $token);
        if ($stmt->execute()) {
            // Enviar el correo electrónico de restablecimiento de contraseña
            $resetLink = "http://yourwebsite.com/reset_password.php?token=" . $token;//revisar esta linea de codigoooooooooooooooooooooo------
            $asunto = "Recuperar Contraseña";
            $mensaje = "Haz clic en el siguiente enlace para restablecer tu contraseña: " . $resetLink;
            enviarCorreo($email, $asunto, $mensaje);

            echo "Correo enviado. Revisa tu bandeja de entrada.";
        } else {
            // Mostrar el error si la inserción falla
            echo "Error al insertar el token: " . $stmt->error;
        }
    } else {
        echo "No se encontró ninguna cuenta con ese correo electrónico.";
    }

    $stmt->close();
    $conn->close();
}
?>
