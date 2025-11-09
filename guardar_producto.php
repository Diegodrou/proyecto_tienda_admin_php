<?php
session_start();
require_once('baseDatos.php');

if (!isset($_SESSION['usuario']) || !$_SESSION['es_admin']) {
    header('Location: loginAdmin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = abrirConexionBD();
    
    // Procesar imagen
    $nombre_imagen = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $directorio = 'img/';
        $nombre_imagen = basename($_FILES['imagen']['name']);
        $ruta_completa = $directorio . $nombre_imagen;
        
        // Mover el archivo subido
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_completa)) {
            $nombre_imagen = '';
        }
    }
    
    // Insertar nuevo producto
    $query = "INSERT INTO productos (nombreAlbum, descripcion, precio, existencias, imagen) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "ssdis", 
        $_POST['nombreAlbum'],
        $_POST['descripcion'],
        $_POST['precio'],
        $_POST['existencias'],
        $nombre_imagen
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    cerrarConexionBD($conexion);
    
    header('Location: productos.php?mensaje=Producto añadido correctamente');
    exit();
}

header('Location: productos.php');
?>