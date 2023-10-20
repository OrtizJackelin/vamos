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

        $consulta = "UPDATE solicitud_verificacion
                SET estado = ?, fecha_revision = CURRENT_TIMESTAMP()
                WHERE id = ?";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        echo "error preparando la consulta para insertar datos ";
    } else{
        $sentencia ->bind_param("ss", $estado,  $id );
        $sentencia->execute();

        if($sentencia->affected_rows <= 0) {
            echo "error guardando <br>";     
        }
        $sentencia->close();   
        echo "solicitud actualizada con éxito en bd verificacion de solicitudes";
    }  

    $consulta = "UPDATE user
    SET es_verificado = ?, documento_verificado = ?
    WHERE id = ?";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        echo "error preparando la consulta para insertar datos ";
    } else {
        $sentencia ->bind_param("sss", $estado, $documento, $idUsuario );
        $sentencia->execute();

        if($sentencia->affected_rows <= 0) {
            echo "error guardando <br>";     
        }
        $sentencia->close();   
        echo "solicitud actualizada con éxito en bd user";  
        
    }
    
    include "bd/cerrar_conexion.php";      
?>