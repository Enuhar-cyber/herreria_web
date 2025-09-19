<?php
$db_host = "localhost"; // Generalmente 'localhost' en Hostinger
$db_nombre = "nombre_de_tu_base_de_datos"; // ESTO LO CREAS EN HOSTINGER (ej: u123456789_herreria)
$db_usuario = "usuario_de_tu_base_de_datos"; // ESTO LO CREAS EN HOSTINGER (ej: u123456789_admin)
$db_contra = "contraseña_de_tu_base_de_datos"; // ESTO LO CREAS EN HOSTINGER (¡MUY IMPORTANTE GUARDARLA!)
$conexion = mysqli_connect($db_host, $db_usuario, $db_contra, $db_nombre);

if (!$conexion) {
    header("Location: contacto.html?status=error_db_connect");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre'] ?? ''); 
    $email = mysqli_real_escape_string($conexion, $_POST['email'] ?? '');
    $telefono = mysqli_real_escape_string($conexion, $_POST['telefono'] ?? '');
    $tipo_proyecto = mysqli_real_escape_string($conexion, $_POST['tipo_proyecto'] ?? ''); 
    $mensaje = mysqli_real_escape_string($conexion, $_POST['mensaje'] ?? '');

    $sql = "INSERT INTO mensajes (nombre, email, telefono, tipo_proyecto, mensaje) 
            VALUES ('$nombre', '$email', '$telefono', '$tipo_proyecto', '$mensaje')";

    if (mysqli_query($conexion, $sql)) {
        header("Location: contacto.html?status=success");
    } else {
        header("Location: contacto.html?status=error_query&msg=" . urlencode(mysqli_error($conexion)));
    }
    
} else {
    header("Location: contacto.html");
}

// ====================================================================
// CERRAR CONEXIÓN
// ====================================================================
mysqli_close($conexion);
?>