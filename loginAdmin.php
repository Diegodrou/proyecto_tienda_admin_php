<?php
session_start();
require_once("baseDatos.php");  // Asegúrate de que el archivo se llama así

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["password"];
    $verificacion = comprobarUsuario($usuario,$clave);
    if ($verificacion['encontrado']) {
        if($verificacion['es_admin']){
            $_SESSION["usuario"] = $usuario;
            $_SESSION["es_admin"] = $verificacion['es_admin']; 
            header("Location: index.php");
            exit();
        }
        else{
            $mensaje = "Este usuario no es administrador";
        }
        
    } else {
        $mensaje = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Tienda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center">Iniciar Sesión</h2>
                <form action=""<?php echo $_SERVER['PHP_SELF']; ?>"" method="POST" class="card p-3 shadow">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="usuario" placeholder="Usuario" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Acceder</button>
                    <p><?php echo $mensaje; ?></p>
                </form>
            </div> 
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>