<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validación de la contraseña
    if ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
        exit;
    }
    if (strlen($password) < 8 || !preg_match("/[A-Z]/", $password) || !preg_match("/[\W]/", $password)) {
        echo "La contraseña debe tener al menos 8 caracteres, una letra mayúscula y un carácter especial.";
        exit;
    }

    // Encriptación de la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Conexión a la base de datos
    $conn = new mysqli("localhost", "root", "", "registro_usuarios");

    if ($conn->connect_error) {
        echo "Conexión fallida: " . $conn->connect_error;
        exit;
    }

    // Insertar el usuario en la base de datos
    $stmt = $conn->prepare("INSERT INTO usuarios (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashed_password);

    if ($stmt->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
