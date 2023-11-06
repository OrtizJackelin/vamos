<?php
    require_once ('sessionStart.php');
    try{
    include "bd/conexion.php";

    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
            header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }

    extract($_POST);
    $esValido = true;

    if(isset($idPublicacion) && isset($idUsuario) && isset($costo) && isset($fechaInicio) 
        && !empty($fechaInicio) && isset($fechaFin) && !empty($fechaFin )){
    
   
        if($_SESSION['esVerificado'] === 0){
            
            $consulta = "SELECT fecha_fin
                        FROM alquiler
                        WHERE id_usuario = ? and fecha_fin >= NOW()";

            $sentencia = $conexion->stmt_init();
            if(!$sentencia->prepare($consulta)){
                echo "error preparando la consulta para verificar fecha ";
            } else{
                $sentencia ->bind_param("s", $idUsuario);
                $sentencia->execute();
                $resultadoFecha = $sentencia->get_result();
                $sentencia->close();
                
                if($resultadoFecha->num_rows > 0){                             
                    $esValido = false;
                    var_dump($esValido);
                    echo "ya tiene una solicitud en curso";
                }
                echo "debe esperar que su solicitud sea revisada";
            }

        } 
       
        if($esValido){

            $consulta = "INSERT INTO alquiler (id_publicacion, id_usuario, fecha_inicio, fecha_fin, costo, aprobado)
            VALUES (?,?,?,?,?,?)";
            $sentencia = $conexion->stmt_init();
            if(!$sentencia->prepare($consulta)){
                echo "error preparando la consulta para insertar datos ";
            } else{
                $sentencia ->bind_param("ssssss", $idPublicacion, $idUsuario, $fechaInicio, $fechaFin, $costo, $_SESSION['esVerificado']);
                $sentencia->execute();

                if($sentencia->affected_rows <= 0) {
                    echo "error guardando <br>";     
                }
                $sentencia->close();   
                echo "solicitud aprobada";
            }     
        }
    }  else {
        echo "Parametros de entradas invalidos no se realizo la reserva";
    }
    
    include "bd/cerrar_conexion.php";      

?>