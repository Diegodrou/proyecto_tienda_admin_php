<?php
function abrirConexionBD(){
    $recurso = mysqli_connect("localhost","root","DawLab","mi_tienda_base",3306);
    if (mysqli_connect_error()){
        echo "Error al conectar a la base de datos:". mysqli_connect_error();
        return false;
    }

    $recurso->set_charset("utf8");
    return $recurso;
}

function cerrarConexionBD(&$recurso){
    if ($recurso){
        mysqli_close($recurso);
    }
}

function comprobarUsuario($usuario, $clave) {
    $recurso = abrirConexionBD();
    $resultado = array(
        'encontrado' => false,
        'es_admin' => false
    );

    if($recurso) {
        $consulta = "SELECT * FROM usuarios WHERE usuario='" . mysqli_real_escape_string($recurso, $usuario) . "' AND clave='" . mysqli_real_escape_string($recurso, $clave) . "'";
        
        if($query_result = mysqli_query($recurso, $consulta)) {
            if(mysqli_num_rows($query_result) > 0) {
                $fila = mysqli_fetch_assoc($query_result);
                $resultado['encontrado'] = true;
                $resultado['es_admin'] = ($fila['admin'] == 1);
            }
            mysqli_free_result($query_result);
        } else {
            echo "Error en la consulta: " . mysqli_error($recurso);
        }
        
        cerrarConexionBD($recurso);
    }

    return $resultado;
}

function obtenerProductos(){
    $recurso = abrirConexionBD();
    $productos = [];

    if($recurso){
        $consulta = "SELECT * FROM productos";
        $resultado = mysqli_query($recurso, $consulta);
        if ($resultado) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $productos[] = $fila;
            }
            mysqli_free_result($resultado);
        }
        cerrarConexionBD($recurso);
    }

    return $productos;
}


?>