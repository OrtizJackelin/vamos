<?php 
    require_once ('sessionStart.php'); 
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        //var_dump($_SESSION);
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

    $consulta = "UPDATE reseña
                SET respuesta = ?, fecha_respuesta = CURRENT_TIMESTAMP()
                WHERE id = ?";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){     
        $mensaje = $mensaje. "fallo la preparación". $sentencia ->error . "<br>";

    } else {
    
        $sentencia->bind_param("ss",$respuesta, $id);
        $sentencia->execute();
        if($sentencia->affected_rows >0){

            echo "Se realizó actualizacion en respuesta a reseña". "<br>";
        } else {

            echo"No hubo actualizacion en la respuesta a la reseña". "<br>";
        }    

    }
    $sentencia->close();



?>