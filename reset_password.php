<?php
require 'send_email.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validar la nueva contraseña
    if ($new_password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }
    if (strlen($new_password) < 8 || !preg_match("/[A-Z]/", $new_password) || !preg_match("/[\W]/", $new_password)) {
        echo "La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un carácter especial.";
        exit;
    }

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "registro_usuarios");

    if ($conn->connect_error) {
        echo "Conexión fallida: " . $conn->connect_error;
        exit;
    }

    // Comprobar el token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];

        // Encriptar la nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Actualizar la contraseña del usuario
        $stmt = $conn->prepare("UPDATE usuarios SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        // Eliminar el token de restablecimiento
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Enviar el correo de confirmación
        $asunto = "Contraseña Cambiada Correctamente";
        $mensaje = "Tu contraseña ha sido cambiada correctamente y la verificación en dos pasos está activada.";
        enviarCorreo($email, $asunto, $mensaje);

        echo "Tu contraseña ha sido cambiada correctamente.";
    } else {
        echo "Token inválido.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <form method="post">
            <h2>Restablecer Contraseña</h2>
            <div class="form-group">
                <label for="new_password">Nueva Contraseña</label>
                <input type="password" id="new_password" name="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar Contraseña</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
        </form>
    </div>
</body>
</html>
