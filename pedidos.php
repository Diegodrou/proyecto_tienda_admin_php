<?php
session_start();
require_once('baseDatos.php');

if (!isset($_SESSION['usuario']) || !$_SESSION['es_admin']) {
    header('Location: loginAdmin.php');
    exit();
}

$conexion = abrirConexionBD();

// Procesar cambio de estado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cambiar_estado'])) {
        $codigo_pedido = intval($_POST['codigo_pedido']);
        $nuevo_estado = intval($_POST['nuevo_estado']);
        
        // Actualizar estado del pedido
        $query = "UPDATE pedidos SET estado = ? WHERE codigo = ?";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "ii", $nuevo_estado, $codigo_pedido);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Si el nuevo estado es cancelado (asumimos que estado 5 es cancelado)
        if ($nuevo_estado == 5) {
            restaurarStock($conexion, $codigo_pedido);
        }
    } 
    elseif (isset($_POST['eliminar_pedido'])) {
        $codigo_pedido = intval($_POST['codigo_pedido']);
        
        // Verificar que el pedido está cancelado antes de eliminar
        $query = "DELETE FROM pedidos WHERE codigo = ? AND estado = 5";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "i", $codigo_pedido);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Función para restaurar stock cuando se cancela un pedido
function restaurarStock($conexion, $codigo_pedido) {
    // Obtener los productos del pedido
    $query = "SELECT codigo_producto, unidades FROM detalle WHERE codigo_pedido = ?";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "i", $codigo_pedido);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    
    while ($fila = mysqli_fetch_assoc($resultado)) {
        // Actualizar el stock de cada producto
        $update = "UPDATE productos SET existencias = existencias + ? WHERE codigo = ?";
        $stmt_update = mysqli_prepare($conexion, $update);
        mysqli_stmt_bind_param($stmt_update, "ii", $fila['unidades'], $fila['codigo_producto']);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);
    }
    mysqli_stmt_close($stmt);
}

// Obtener parámetros de filtrado del formulario
$filtro_usuario = $_GET['filtro_usuario'] ?? null;
$filtro_producto = $_GET['filtro_producto'] ?? null;
$filtro_fecha = $_GET['filtro_fecha'] ?? null;
$operador_fecha = $_GET['operador_fecha'] ?? '=';
$operador_logico = $_GET['operador_logico'] ?? 'AND';

// Construir consulta base con JOINs necesarios
$query_base = "SELECT DISTINCT p.codigo, p.fecha, p.importe, e.descripcion as estado, 
               CONCAT(u.nombre, ' ', u.apellidos) as cliente_nombre
               FROM pedidos p
               JOIN estados e ON p.estado = e.codigo
               JOIN usuarios u ON p.persona = u.codigo
               LEFT JOIN detalle d ON p.codigo = d.codigo_pedido
               LEFT JOIN productos pr ON d.codigo_producto = pr.codigo";

// Construir condiciones de filtrado
$condiciones = [];
$params = [];
$types = '';

if ($filtro_usuario) {
    $condiciones[] = "u.codigo = ?";
    $params[] = $filtro_usuario;
    $types .= 'i';
}

if ($filtro_producto) {
    $condiciones[] = "pr.codigo = ?";
    $params[] = $filtro_producto;
    $types .= 'i';
}

if ($filtro_fecha) {
    $operadores_validos = ['=', '<=', '>='];
    $operador_fecha = in_array($operador_fecha, $operadores_validos) ? $operador_fecha : '=';
    
    $condiciones[] = "p.fecha $operador_fecha ?";
    $params[] = $filtro_fecha;
    $types .= 's';
}

// Combinar condiciones con el operador lógico seleccionado
if (!empty($condiciones)) {
    $query_base .= " WHERE " . implode(" $operador_logico ", $condiciones);
}

// Ordenar resultados
$query_base .= " ORDER BY p.fecha DESC";

// Preparar y ejecutar consulta
$stmt = mysqli_prepare($conexion, $query_base);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$pedidos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// Obtener datos para los filtros desplegables
$query_usuarios = "SELECT codigo, CONCAT(nombre, ' ', apellidos) as nombre_completo FROM usuarios ORDER BY nombre";
$usuarios = mysqli_query($conexion, $query_usuarios);

$query_productos = "SELECT codigo, nombreAlbum FROM productos ORDER BY nombreAlbum";
$productos = mysqli_query($conexion, $query_productos);

// Obtener estados disponibles (tu código existente)
$query_estados = "SELECT * FROM estados";
$resultado_estados = mysqli_query($conexion, $query_estados);
$estados = mysqli_fetch_all($resultado_estados, MYSQLI_ASSOC);

// // Obtener todos los pedidos con información del cliente y estado
// $query = "SELECT p.codigo, p.fecha, p.importe, e.descripcion as estado, 
//                  CONCAT(u.nombre, ' ', u.apellidos) as cliente_nombre
//           FROM pedidos p
//           JOIN estados e ON p.estado = e.codigo
//           JOIN usuarios u ON p.persona = u.codigo
//           ORDER BY p.fecha DESC";
// $resultado = mysqli_query($conexion, $query);
// $pedidos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);

// // Obtener todos los estados disponibles
// $query_estados = "SELECT * FROM estados";
// $resultado_estados = mysqli_query($conexion, $query_estados);
// $estados = mysqli_fetch_all($resultado_estados, MYSQLI_ASSOC);

cerrarConexionBD($conexion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .estado-pendiente { color: #ffc107; font-weight: bold; }
        .estado-enviado { color: #17a2b8; font-weight: bold; }
        .estado-entregado { color: #28a745; font-weight: bold; }
        .estado-cancelado { color: #dc3545; font-weight: bold; text-decoration: line-through; }
        
        .producto-img {
            max-width: 50px;
            max-height: 50px;
            margin-right: 10px;
        }
        
        .detalles-pedido {
            background-color: #f8f9fa;
        }

        .filtros {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <mi-menu></mi-menu>
    <div class="container mt-5">
        <h2 class="mb-4">Gestión de Pedidos</h2>
        
        <?php if (isset($_POST['cambiar_estado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Estado del pedido actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_POST['eliminar_pedido'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Pedido cancelado eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <!-- Sección de Filtros -->
        <div class="filtros mb-4">
            <h4>Filtrar Pedidos</h4>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="filtro_usuario" class="form-label">Usuario</label>
                    <select class="form-select" id="filtro_usuario" name="filtro_usuario">
                        <option value="">Todos los usuarios</option>
                        <?php while ($usuario = mysqli_fetch_assoc($usuarios)): ?>
                            <option value="<?= $usuario['codigo'] ?>" <?= $filtro_usuario == $usuario['codigo'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usuario['nombre_completo']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="filtro_producto" class="form-label">Producto</label>
                    <select class="form-select" id="filtro_producto" name="filtro_producto">
                        <option value="">Todos los productos</option>
                        <?php while ($producto = mysqli_fetch_assoc($productos)): ?>
                            <option value="<?= $producto['codigo'] ?>" <?= $filtro_producto == $producto['codigo'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($producto['nombreAlbum']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="filtro_fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="filtro_fecha" name="filtro_fecha" value="<?= htmlspecialchars($filtro_fecha) ?>">
                </div>
                
                <div class="col-md-2">
                    <label for="operador_fecha" class="form-label">Operador Fecha</label>
                    <select class="form-select" id="operador_fecha" name="operador_fecha">
                        <option value="=" <?= $operador_fecha == '=' ? 'selected' : '' ?>>Igual a</option>
                        <option value="<=" <?= $operador_fecha == '<=' ? 'selected' : '' ?>>Menor o igual</option>
                        <option value=">=" <?= $operador_fecha == '>=' ? 'selected' : '' ?>>Mayor o igual</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="operador_logico" class="form-label">Operador Lógico</label>
                    <select class="form-select" id="operador_logico" name="operador_logico">
                        <option value="AND" <?= $operador_logico == 'AND' ? 'selected' : '' ?>>AND (todas las condiciones)</option>
                        <option value="OR" <?= $operador_logico == 'OR' ? 'selected' : '' ?>>OR (cualquier condición)</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="pedidos.php" class="btn btn-secondary">Limpiar Filtros</a>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Importe</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                            <td><?= htmlspecialchars($pedido['codigo']) ?></td>
                            <td><?= htmlspecialchars($pedido['cliente_nombre']) ?></td>
                            <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                            <td><?= htmlspecialchars($pedido['importe']) ?> €</td>
                            <td>
                                <span class="estado-<?= strtolower($pedido['estado']) ?>">
                                    <?= htmlspecialchars($pedido['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <!-- Botón para ver detalles -->
                                    <button class="btn btn-info btn-sm" data-bs-toggle="collapse" 
                                            data-bs-target="#detalles-<?= $pedido['codigo'] ?>">
                                        Ver Detalles
                                    </button>
                                    
                                    <!-- Formulario para cambiar estado -->
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="codigo_pedido" value="<?= $pedido['codigo'] ?>">
                                        <select name="nuevo_estado" class="form-select form-select-sm d-inline" style="width: auto;">
                                            <?php foreach ($estados as $estado): ?>
                                                <option value="<?= $estado['codigo'] ?>" 
                                                    <?= $estado['descripcion'] == $pedido['estado'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($estado['descripcion']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="cambiar_estado" class="btn btn-warning btn-sm">
                                            Cambiar
                                        </button>
                                    </form>
                                    
                                    <!-- Botón para eliminar pedidos cancelados -->
                                    <?php if ($pedido['estado'] == 'cancelado'): ?>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="codigo_pedido" value="<?= $pedido['codigo'] ?>">
                                            <button type="submit" name="eliminar_pedido" class="btn btn-danger btn-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Detalles del pedido (productos) -->
                                <tr class="collapse detalles-pedido" id="detalles-<?= $pedido['codigo'] ?>">
                                    <td colspan="6">
                                        <h5>Productos del pedido:</h5>
                                        <?php
                                        $conexion = abrirConexionBD();
                                        $query = "SELECT d.unidades, d.precio_unitario, 
                                                 pr.descripcion, pr.nombreAlbum, pr.imagen
                                                 FROM detalle d
                                                 JOIN productos pr ON d.codigo_producto = pr.codigo
                                                 WHERE d.codigo_pedido = ?";
                                        $stmt = mysqli_prepare($conexion, $query);
                                        mysqli_stmt_bind_param($stmt, "i", $pedido['codigo']);
                                        mysqli_stmt_execute($stmt);
                                        $resultado = mysqli_stmt_get_result($stmt);
                                        $productos = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
                                        mysqli_stmt_close($stmt);
                                        cerrarConexionBD($conexion);
                                        ?>
                                        
                                        <div class="row">
                                            <?php foreach ($productos as $producto): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card">
                                                        <div class="card-body d-flex">
                                                            <?php if ($producto['imagen']): ?>
                                                                <img src="img/<?=$producto['imagen']?>" 
                                                                     alt="<?= htmlspecialchars($producto['nombreAlbum']) ?>" 
                                                                     class="producto-img">
                                                            <?php endif; ?>
                                                            <div>
                                                                <h6><?= htmlspecialchars($producto['nombreAlbum'] ?? $producto['descripcion']) ?></h6>
                                                                <p>
                                                                    <?= htmlspecialchars($producto['unidades']) ?> x 
                                                                    <?= htmlspecialchars($producto['precio_unitario']) ?> € = 
                                                                    <strong><?= htmlspecialchars($producto['unidades'] * $producto['precio_unitario']) ?> €</strong>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
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