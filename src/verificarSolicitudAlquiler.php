<?php 
    require_once ('sessionStart.php'); 
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        var_dump($_SESSION);
    }
    
    $valido = true;
    $mensaje = "";    
            
    try{
        include "bd/conexion.php";
    
    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }
    //var_dump($_POST);
    extract($_POST);

        $consulta = "UPDATE alquiler
                SET aprobado = ?
                WHERE id = ?";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        echo "Error preparando la consulta para insertar datos ";
    } else{
        $sentencia ->bind_param("ss", $estado,  $id );
        $sentencia->execute();

        if($sentencia->affected_rows <= 0) {
            echo "No se guardo en bd el valor ya existia <br>";     
        }
        $sentencia->close();   
        echo "Solicitud actualizada con éxito en bd verificacion de solicitudes";
    }  
   
    include "bd/cerrar_conexion.php";      
?>