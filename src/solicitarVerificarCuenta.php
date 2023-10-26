<?php
    require_once ('sessionStart.php');

     if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        //var_dump($_SESSION);
    }
    
    $valido = true;
    $mensaje = "";
    $directorioDestino = "../static/imagenes/documentoUsuarios/";
    $nombreFotoDocumento = "";
    $solicitudEnProceso = true;
 
            
    try{
        include "bd/conexion.php";
    
    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }

    $consulta = "SELECT fecha_solicitud, estado, fecha_revision FROM verificacion_cuenta WHERE id_usuario = ?";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
    } else {
        $sentencia->bind_param("s", $_SESSION['id']);
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $sentencia->close();

        if($resultado->num_rows > 0){

            if($dato = $resultado->fetch_row()){
               // var_dump($dato);
                
                switch ($dato[1]) {
                    case 0:
                        $mensaje = $mensaje. "Usted ya posee una solicitud pendiente de fecha: " . $dato[0] . "<br>";
                        break;
                    case 1:
                        $mensaje = $mensaje. "Usted ya posee una cuenta verificada de fecha: " . $dato[2] . "<br>";
                        break;
                    case 2:
                        $mensaje = $mensaje. "Última Solicitud rechazada, fecha: " . $dato[2] . "<br>";
                        $solicitudEnProceso = false;
                        $valido = false;
                        echo "aqui";
                        break;
                    default:
                        $mensaje = $mensaje. "La opción no coincide con ninguna de las anteriores.<br>";
                        echo"default";
                }

            }
        } else {
            $solicitudEnProceso = false;
        }
    }

   
    if(isset($_POST['enviar'])){
        //var_dump($_FILES); 
    
        if (file_exists($directorioDestino) && ($_FILES['foto']['size']>0) ) {
                    
            $nombreFotoDocumento = $_FILES["foto"]["name"];
            $tipoArchivo = $_FILES["foto"]["type"];
            $tamanoArchivo = $_FILES["foto"]["size"];
            $archivoTmpName = $_FILES["foto"]["tmp_name"];
            $errorArchivo = $_FILES["foto"]["error"];

            $imageFileType = strtolower(pathinfo($nombreFotoDocumento, PATHINFO_EXTENSION));

            if ($errorArchivo === UPLOAD_ERR_OK) {
                $check = getimagesize($archivoTmpName);
                if ($check !== false) {
                    $maxFileSize = 3 * 1024 * 1024; // 5 MB
                    if ($tamanoArchivo <= $maxFileSize) {                       
                    
                        // Mover el archivo temporal al destino deseado   
                        $nombreFotoDocumento = uniqid() . "." . $imageFileType;             
                        if (move_uploaded_file($archivoTmpName, $directorioDestino . $nombreFotoDocumento)) {
                            //$mensaje = $mensaje . ": " . $nombreFotoDocumento. "<br>";
                            $consulta = "INSERT INTO verificacion_cuenta (id_usuario, documento, comentario) 
                                        VALUE  (?, ?, ?)";
                            
                            $sentencia = $conexion->stmt_init();
            
                            if(!$sentencia->prepare($consulta)) {
                                $mensaje = $mensaje . "fallo la preparacion de la consulta " . $sentencia->error . "<br>";
                                $valido = false;
                            } else{
                                $sentencia->bind_param("sss", $_SESSION['id'], $nombreFotoDocumento, $_POST['comentario']);        
                                $sentencia->execute();
                                if($sentencia->affected_rows <= 0) {
                                    $mensaje = $mensaje . "error guardando datos" . $sentencia->error . "<br>"; 
                                    $valido = false;
                                }
                                $sentencia->close(); 
                            }

                            $consulta = "UPDATE user
                                        SET documento_verificado = ? 
                                        WHERE id = ?";
                
                            $sentencia = $conexion->stmt_init();

                            if(!$sentencia->prepare($consulta)) {
                                $mensaje = $mensaje . "fallo la preparacion de la consulta " . $sentencia->error . "<br>";
                                $valido = false;
                            } else{
                                $sentencia->bind_param("ss", $nombreFotoDocumento, $_SESSION['id'] );        
                                $sentencia->execute();
                                if($sentencia->affected_rows <= 0) {
                                    $mensaje = $mensaje . "error guardando datos ". $sentencia->error . "<br>"; 
                                    $valido = false;
                                }
                                $sentencia->close(); 
                            }


                        } else {
                            $mensaje = $mensaje ."Hubo un error al mover el archivo. ". $errorArchivo . "<br>";
                            $valido = false;
                        }
                    }else {
                        $mensaje = $mensaje ."El archivo es demasiado grande. El tamaño máximo permitido es 3 MB..". $errorArchivo . "<br>";
                            $valido = false;
                    }
                } else {
                    $mensaje = $mensaje . "El archivo no es una imagen válida.". $errorArchivo . "<br>";
                    $valido =false;
                }
            } else {
                $mensaje = $mensaje . "Error al subir el archivo. Código de error: " . $errorArchivo . "<br>";
                $valido = false;
                
            }

        } else {
            $mensaje = $mensaje . "seleccione un archivo <br>";
            $solicitudEnProceso = false;
            $valido = false;

        } 
    }  
    include "bd/cerrar_conexion.php";            
        
?>

<!DOCTYPE html>
<html lang="es">

    <head>
        <title>Vamos</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../static/css/style.css" type="text/css">

        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
        <link href="../static/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <body>
        <header>
            <?php include("barraDeNavegacion.php"); ?>
        </header>

        <?php if(!$solicitudEnProceso) { ?>
            <section class = "sectionPrincipal">
                <div class="container w-75 ">

                    <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                        <h2> Verificaci&oacute;n De Cuenta</h2>
                    </div>

                    <form class="row g-5 p-5 " id="formulario" method="post" action="solicitarVerificarCuenta.php" enctype = "multipart/form-data" >


                        <div class="col-md-12 mb-3">
                            <label for="comentario" class="form-label">Comentarios</label>
                            <textarea class="form-control" id="comentario" name= "comentario" rows="3" ></textarea>
                        </div>  
                        
                
                        <div class="mb-3">
                            <label for="formFile" class="form-label"></label>
                            <input class="form-control" type="file" id="foto" name = "foto">
                        </div>     
        
                        <div class="col-12 ">
                            <button type="submit" class="btn btn-success" id="validar" name = "enviar">Solicitar</button>
                        </div>

                    </form><br>

                    <?php if(!$valido) { ?>

                        <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                            style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                            <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                            <div>
                                <H6><b><?php echo $mensaje ?></H6></b>
                            </div>
                            <button type="submit" class="btn-close btn position-absolute top-0 end-0 m-2" rol="alert"  
                                data-bs-dismiss="alert" aria-label="Cerrar"></button>
                        </div> 

                    <?php } ?>
                </div>
               
            </section>

        <?php } else { ?>
            <section class = "sectionPrincipal">    
                <div class = "container w-75">
                    <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                    style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                        <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                        <div>
                            <H6><b><?php echo $mensaje ?></H6></b>
                        </div>
                        <button type="submit" class="btn-close position-absolute top-0 end-0 m-2" rol="alert" 
                        data-bs-dismiss="alert"aria-label="Close"></button>
                    </div> 
                </div>
            </section>
        <?php } ?>
       
        <!--FOOTER-->
        <footer>
            <?php include("../static/html/footer.html"); ?>
        </footer>

        <!-- SCRIPT -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
   
    </body>
</html>