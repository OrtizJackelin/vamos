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

        $consulta = "UPDATE verificacion_cuenta
                SET estado = ?, fecha_vencimiento = ?, fecha_revision = CURRENT_TIMESTAMP()
                WHERE id = ?";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        echo "error preparando la consulta para insertar datos <br> ";
    } else{
        $sentencia ->bind_param("sss", $estado, $fechaVencimiento, $id );
        $sentencia->execute();

        if($sentencia->affected_rows <= 0) {
            echo "error guardando los datos de verificacion de cuenta <br>";     
        } else {             
            echo "solicitud de verificacion actualizada con éxito en bd verificacion de solicitudes. <br>";
        }
        $sentencia->close(); 
    }  
var_dump($_POST);
    $consulta = "UPDATE user
                SET es_verificado = ?
                WHERE id = ?";
    $sentencia2 = $conexion->stmt_init();
    if(!$sentencia2->prepare($consulta)){
        echo "error preparando la consulta para actualizar datos. <br>";
    } else {
        $sentencia2 ->bind_param("ss", $estado, $idUsuario );
        $sentencia2->execute();
      
        if($sentencia2->affected_rows <= 0) {
            echo "error guardando el estado de verificacion del usuario <br>";     
        } else {            
            echo "solicitud actualizada con éxito en bd user. <br>";  
        }
        $sentencia2->close();
    }
    
    include "bd/cerrar_conexion.php";      
?>