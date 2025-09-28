<?php

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: index.php'); // Llevar de vuelta a la página de login
    exit;
}

require_once '../backend/config.php';

$conexion = new mysqli(DB_HOST, DB_USUARIO, DB_CONTRA, DB_NOMBRE);
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$sql = "SELECT id, nombre, email, telefono, tipo_proyecto, mensaje, archivo_adjunto, fecha_envio FROM mensajes ORDER BY fecha_envio DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Mensajes - Herrería López</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7f6;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #34495e;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        .logout-btn {
            float: right;
            background-color: #e74c3c;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.95em;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        .no-messages {
            text-align: center;
            padding: 40px;
            font-size: 1.1em;
            color: #777;
        }
        .attachment-link {
            display: inline-block;
            margin-top: 5px;
            font-size: 0.9em;
            color: #3498db;
            text-decoration: none;
        }
        .attachment-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
    <h1>Bandeja de Entrada de Mensajes</h1>

    <div class="container">
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Tipo de Proyecto</th>
                        <th>Mensaje</th>
                        <th>Adjunto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($fila['fecha_envio']))); ?></td>
                            <td><?php echo htmlspecialchars($fila['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($fila['email']); ?></td>
                            <td><?php echo htmlspecialchars($fila['telefono']); ?></td>
                            <td><?php echo htmlspecialchars($fila['tipo_proyecto']); ?></td>
                            <td><?php echo nl2br(htmlspecialchars($fila['mensaje'])); ?></td>
                            <td>
                                <?php if ($fila['archivo_adjunto']): ?>
                                    <a href="../uploads/<?php echo htmlspecialchars($fila['archivo_adjunto']); ?>" target="_blank" class="attachment-link">Ver Adjunto</a>
                                <?php else: ?>
                                    No hay
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-messages">No hay mensajes en la bandeja de entrada todavía.</p>
        <?php endif; ?>
    </div>

</body>
</html>
<?php
$conexion->close();
?>