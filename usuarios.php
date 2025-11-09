<?php
// Verificar sesión y permisos de administrador
session_start();
require_once('baseDatos.php'); // Archivo con funciones de conexión y autenticación

if (!isset($_SESSION['usuario']) || !$_SESSION['es_admin']) {
    header('Location: loginAdmin.php');
    exit();
}

// Conexión a la base de datos
$conexion = abrirConexionBD();

// Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_estado'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $nuevo_estado = intval($_POST['nuevo_estado']);
    
    // Actualizar estado en la BD (solo para usuarios no admin)
    $query = "UPDATE usuarios SET activo = ? WHERE codigo = ? AND admin = 0";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "ii", $nuevo_estado, $id_usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

// Obtener lista de usuarios (no administradores)
$query = "SELECT codigo, nombre, apellidos, telefono, activo 
          FROM usuarios 
          WHERE admin = 0 
          ORDER BY nombre ASC";
$resultado = mysqli_query($conexion, $query);
$usuarios = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_free_result($resultado);
cerrarConexionBD($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .inactivo {
            color: #dc3545;
            font-weight: bold;
        }
        .activo {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <mi-menu></mi-menu>
    <div class="container mt-5">
        <h2 class="mb-4">Gestión de Usuarios</h2>
        
        <?php if (isset($_POST['cambiar_estado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Estado del usuario actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['codigo']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellidos']) ?></td>
                            <td><?= htmlspecialchars($usuario['telefono']) ?></td>
                            <td>
                                <span class="<?= $usuario['activo'] ? 'activo' : 'inactivo' ?>">
                                    <?= $usuario['activo'] ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id_usuario" value="<?= $usuario['codigo'] ?>">
                                    <input type="hidden" name="nuevo_estado" value="<?= $usuario['activo'] ? 0 : 1 ?>">
                                    <button type="submit" name="cambiar_estado" class="btn btn-sm <?= $usuario['activo'] ? 'btn-warning' : 'btn-success' ?>">
                                        <?= $usuario['activo'] ? 'Desactivar' : 'Activar' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script src="./js/mis-etiquetasAdmin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>