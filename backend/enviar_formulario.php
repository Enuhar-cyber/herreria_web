<?php
require_once 'config.php'; // Incluir la configuración de la BD

$conexion = new mysqli(DB_HOST, DB_USUARIO, DB_CONTRA, DB_NOMBRE);
if ($conexion->connect_error) {
    header("Location: ../contacto.html?status=error_db_connect");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 1. Validar datos de texto
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $tipo_proyecto = trim($_POST['tipo_proyecto'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../contacto.html?status=invalid_input");
        exit();
    }

    // 2. Procesar el archivo subido
    $nombre_archivo = null; // Inicializar como null
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $info_archivo = getimagesize($_FILES['archivo']['tmp_name']);
        $tamaño_archivo = $_FILES['archivo']['size'];
        if($info_archivo==false){
            header("Location: ../contacto.html?status=error_not_image");
            exit();
        }
        if ($tamaño_archivo > 5000000) { // 5,000,000 bytes = 5MB
            header("Location: ../contacto.html?status=error_filesize");
            exit();
        }
        $directorio_subidas = '../uploads/'; // La carpeta que creaste
        $nombre_temporal = $_FILES['archivo']['tmp_name'];
        
        // Crear un nombre de archivo único para evitar sobreescribir archivos
        $nombre_archivo = uniqid() . '-' . basename($_FILES['archivo']['name']);
        $ruta_destino = $directorio_subidas . $nombre_archivo;

        // Mover el archivo del directorio temporal al directorio de subidas
        if (!move_uploaded_file($nombre_temporal, $ruta_destino)) {
            // Si falla la subida del archivo, redirigir con error
            header("Location: ../contacto.html?status=error_upload");
            exit();
        }
    }

    // 3. Insertar en la base de datos (incluyendo el nombre del archivo si existe)
    $sql = "INSERT INTO mensajes (nombre, email, telefono, tipo_proyecto, mensaje, archivo_adjunto) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        header("Location: ../contacto.html?status=error_prepare");
        exit();
    }
    
    // El tipo de dato para el archivo es 's' (string), o NULL si no se subió archivo
    $stmt->bind_param("ssssss", $nombre, $email, $telefono, $tipo_proyecto, $mensaje, $nombre_archivo);

    if ($stmt->execute()) {
        header("Location: ../contacto.html?status=success");
    } else {
        header("Location: ../contacto.html?status=error_query");
    }
    
    $stmt->close();
} else {
    header("Location: ../contacto.html");
}

$conexion->close();
?>