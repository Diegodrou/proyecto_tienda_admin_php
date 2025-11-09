<?php
session_start();
require_once('baseDatos.php');

// Verificar permisos de administrador
if (!isset($_SESSION['usuario']) || !$_SESSION['es_admin']) {
    header('Location: loginAdmin.php');
    exit();
}

$conexion = abrirConexionBD();

// Procesar actualización de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actualizar_producto'])) {
    $codigo = intval($_POST['codigo']);
    $precio = floatval($_POST['precio']);
    $existencias = intval($_POST['existencias']);
    
    $query = "UPDATE productos SET precio = ?, existencias = ? WHERE codigo = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "dii", $precio, $existencias, $codigo);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    $mensaje = "Producto actualizado correctamente";
}

// Procesar eliminación de producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_producto'])) {
    $codigo = intval($_POST['codigo']);
    
    // Verificar si el producto está en algún pedido
    $query = "SELECT COUNT(*) as total FROM detalle WHERE codigo_producto = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "i", $codigo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt);
    
    if ($fila['total'] == 0) {
        // Eliminar producto si no está en pedidos
        $query = "DELETE FROM productos WHERE codigo = ?";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "i", $codigo);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $mensaje = "Producto eliminado correctamente";
    } else {
        $error = "No se puede eliminar: el producto está incluido en pedidos";
    }
}

// Obtener todos los productos
$query = "SELECT codigo, nombreAlbum, descripcion, precio, existencias, imagen FROM productos ORDER BY nombreAlbum";
$resultado = mysqli_query($conexion, $query);
$productos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

cerrarConexionBD($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .producto-img {
            max-width: 50px;
            max-height: 50px;
            margin-right: 10px;
        }
        
        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        
        .editable {
            background-color: #fff;
            border: 1px solid #ced4da;
        }
    </style>
</head>

<body>
    <mi-menu></mi-menu>
    <div class="container mt-5">
        <h2 class="mb-4">Gestión de Productos</h2>
        
        <!-- Mostrar mensajes -->
        <?php if (isset($mensaje)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAnadirProducto">
            Añadir Producto
        </button>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['codigo']) ?></td>
                            <td>
                                <?php if ($producto['imagen']): ?>
                                    <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" 
                                         alt="<?= htmlspecialchars($producto['nombreAlbum']) ?>" 
                                         class="producto-img">
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($producto['nombreAlbum']) ?></td>
                            <td><?= htmlspecialchars($producto['descripcion']) ?></td>
                            <td>
                                <form method="POST" class="form-inline">
                                    <input type="hidden" name="codigo" value="<?= $producto['codigo'] ?>">
                                    <input type="number" step="0.01" name="precio" 
                                           value="<?= htmlspecialchars($producto['precio']) ?>" 
                                           class="form-control form-control-sm editable" required>
                            </td>
                            <td>
                                    <input type="number" name="existencias" 
                                           value="<?= htmlspecialchars($producto['existencias']) ?>" 
                                           class="form-control form-control-sm editable" required>
                            </td>
                            <td>
                                    <button type="submit" name="actualizar_producto" class="btn btn-warning btn-sm">
                                        Guardar
                                    </button>
                                </form>
                                
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="codigo" value="<?= $producto['codigo'] ?>">
                                    <button type="submit" name="eliminar_producto" class="btn btn-danger btn-sm"
                                            onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal para añadir nuevo producto -->
    <div class="modal fade" id="modalAnadirProducto" tabindex="-1" aria-labelledby="modalAnadirProductoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAnadirProductoLabel">Añadir Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="guardar_producto.php" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="nombreAlbum" class="form-label">Nombre del Álbum</label>
                            <input type="text" class="form-control" id="nombreAlbum" name="nombreAlbum" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
                        </div>
                        <div class="mb-3">
                            <label for="existencias" class="form-label">Existencias</label>
                            <input type="number" class="form-control" id="existencias" name="existencias" required>
                        </div>
                        <div class="mb-3">
                            <label for="imagen" class="form-label">Imagen</label>
                            <input type="file" class="form-control" id="imagen" name="imagen" accept="image/*">
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Producto</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="./js/mis-etiquetasAdmin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>