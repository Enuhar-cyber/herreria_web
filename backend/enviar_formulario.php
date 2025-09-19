<?php
// ====================================================================
// CONFIGURACIÓN DE BASE DE DATOS (guárdalo en archivo separado, ej. config.php)
// ====================================================================
$db_host = "localhost";
$db_nombre = "nombre_de_tu_base_de_datos";
$db_usuario = "usuario_de_tu_base_de_datos";
$db_contra = "contraseña_de_tu_base_de_datos";
// Conexión segura
$conexion = new mysqli($db_host, $db_usuario, $db_contra, $db_nombre);
if ($conexion->connect_error) {
    header("Location: ../contacto.html?status=error_db_connect");
    exit();
}
// ====================================================================
// PROCESAR FORMULARIO
// ====================================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre        = trim($_POST['nombre'] ?? '');
    $email         = trim($_POST['email'] ?? '');
    $telefono      = trim($_POST['telefono'] ?? '');
    $tipo_proyecto = trim($_POST['tipo_proyecto'] ?? '');
    $mensaje       = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: contacto.html?status=invalid_input");
        exit();
    }
    $sql = "INSERT INTO mensajes (nombre, email, telefono, tipo_proyecto, mensaje) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        header("Location: contacto.html?status=error_prepare");
        exit();
    }

    $stmt->bind_param("sssss", $nombre, $email, $telefono, $tipo_proyecto, $mensaje);

    if ($stmt->execute()) {
        header("Location: contacto.html?status=success");
    } else {
        header("Location: ../contacto.html?status=error_query");
    }

    $stmt->close();
} else {
    header("Location: contacto.html");
}
// ====================================================================
// CERRAR CONEXIÓN
// ====================================================================
$conexion->close();
?>
