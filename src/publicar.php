<?php
    require_once ('sessionStart.php');

    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        var_dump($_SESSION);
    }

    $valido = true;
    $textoErrorTitulo = "";
    $textoError = "";
    $directorioDestino = "../static/imagenes/publicaciones/";
    $nombreArchivoTxt = "";
    $puedePublicar = true;
    $mensaje = "";


    try{
        include "bd/conexion.php";

    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }

    $consulta = "SELECT id, nombre
    FROM servicio";        
    $sentencia = $conexion->stmt_init();

    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. " fallo la preparacion de la consulta para buscar lista de servicios <br>";
    }
    else{
        $sentencia->execute();
        $resultadoServicio = $sentencia->get_result();
        $sentencia->close();
    }

    $consulta = "SELECT id, nombre
    FROM etiqueta";        
    $sentencia = $conexion->stmt_init();

    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. " fallo la preparacion de la consulta para buscar lista de servicios <br>";
    }
    else{
        $sentencia->execute();
        $resultadoEtiqueta = $sentencia->get_result();
        $sentencia->close();
    }


   // var_dump($_SESSION['esVerificado']);
   if(isset($_SESSION['esVerificado'])){

        if($_SESSION['esVerificado'] === 0){
            $consulta = "SELECT estado
                        FROM publicacion
                        WHERE id_usuario = ? AND id = (SELECT MAX(id) FROM publicacion WHERE id_usuario = ?)";

            $sentencia = $conexion->stmt_init();
            if(!$sentencia->prepare($consulta)){
                $mensaje = $mensaje. " fallo la preparación de la consula. <br>";
            } else {
                $sentencia->bind_param("ss", $_SESSION['id'], $_SESSION['id']);
                $sentencia->execute();
                $resultado = $sentencia->get_result();
                if($resultado->num_rows >0){
                    if($dato=$resultado->fetch_row()){
                        if($dato[0]===2){
                            $mensaje = $mensaje. " ultima solicitud rechazada. <br>";
                        } else {
                            $mensaje = $mensaje. "Usted ya posee una publicación ó una solicitud en proceso.<br>
                                                Si desea más beneficios en su cuenta deberá solicitar la certificación
                                                de la misma. <br>";
                            $puedePublicar = false;
                        }
                    }  
                }
            }
        }


        if($puedePublicar){


            if (isset($_POST['enviar'])){


                if (isset($_POST['titulo']) && empty($_POST['titulo'])){
                
                    $mensaje = $mensaje. "Debe ingresar un titulo";
                    $valido = false;
                }


                if (isset($_POST['ubicacion']) && empty($_POST['ubicacion'])){
                    $mensaje = $mensaje. "Debe ingresar una ubicacion";
                    $valido = false;                    
                }

                if (isset($_POST['decripcion']) && empty($_POST['descripcion'])){
                    $mensaje = $mensaje. "Debe ingresar una ubicacion";
                    $valido = false;                    
                }

                if (isset($_POST['costo']) && empty($_POST['costo'])){
                    $mensaje = $mensaje. "Debe ingresar un costo";
                    $valido = false;                    
                }

                if (isset($_POST['cupo']) && empty($_POST['cupo'])){
                    $mensaje = $mensaje. "Debe ingresar un cupo";
                    $valido = false;                    
                }

                if (isset($_POST['tiempo_minimo_permanencia']) && empty($_POST['tiempo_minimo_permanencia'])){
                    $_POST['tiempo_minimo_permanencia'] = null;               
                }
                
                if (isset($_POST['tiempo_maximo_permanencia']) && empty($_POST['tiempo_maximo_permanencia'])){
                    $_POST['tiempo_maximo_permanencia'] = null;               
                }

                if (isset($_POST['fecha_fin']) && empty($_POST['fecha_fin'])){
                    $_POST['fecha_fin']= null;               
                }
                
                if (isset($_POST['fecha_inicio']) && empty($_POST['fecha_inicio'])){
                    $_POST['fecha_inicio']= null;              
                }

                
            }

            if (isset($_POST['enviar']) && $valido){
                
                $consulta = "INSERT INTO  publicacion (titulo, descripcion, ubicacion, costo, 
                cupo, tiempo_minimo, tiempo_maximo, fecha_inicio_publicacion, fecha_fin_publicacion, id_usuario, estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?, 1) ";

                $sentencia = $conexion->stmt_init();

                if(!$sentencia->prepare($consulta)) {
                   // $mensaje = $mensaje. "fallo la preparacion de la consulta para guardar datos de publicación <br>";
                } else{
                    $sentencia->bind_param("ssssssssss", $_POST['titulo'], $_POST['descripcion'], $_POST['ubicacion'], 
                    $_POST['costo'], $_POST['cupo'], $_POST['tiempo_minimo'], $_POST['tiempo_maximo'], 
                    $_POST['fecha_inicio'], $_POST['fecha_fin'], $_SESSION['id']);

                    $sentencia->execute();
                    
                    if($sentencia->affected_rows > 0){
                        unset($_SESSION['email']);
                        $id_publicacion = $sentencia->insert_id;
                        $sentencia->close();
                                    
                        $rutaDestino = $directorioDestino . $id_publicacion . "/";

                        if (!file_exists($rutaDestino )) {
                            if (!mkdir($rutaDestino )) {
                                //$mensaje = $mensaje. "Error al crear la carpeta '$rutaDestino '.";
                            } 
                        }    
                        //var_dump($_FILES["imagenes"]);
                        if (file_exists($rutaDestino) &&  isset($_FILES["imagenes"]) && is_array($_FILES["imagenes"]["name"])) {
                            $totalArchivos = count($_FILES["imagenes"]["name"]);
                            
                            for ($i = 0; $i < $totalArchivos; $i++) {
                                $nombreArchivo = $_FILES["imagenes"]["name"][$i];
                                $tipoArchivo = $_FILES["imagenes"]["type"][$i];
                                $tamanoArchivo = $_FILES["imagenes"]["size"][$i];
                                $archivoTmpName = $_FILES["imagenes"]["tmp_name"][$i];
                                $errorArchivo = $_FILES["imagenes"]["error"][$i];
                                if ($errorArchivo === UPLOAD_ERR_OK) {

                                    // Mover el archivo temporal al destino deseado
                                    // Cambia esta ruta a la carpeta donde deseas guardar los archivos
                                    $rutaArchivo = $rutaDestino . $nombreArchivo;
                            
                                    if (move_uploaded_file($archivoTmpName, $rutaArchivo)) {
                                        //$mensaje = $mensaje. "El archivo se subió correctamente a: " . $rutaArchivo;

                                        $consulta = "INSERT INTO imagen(ruta, id_publicacion)
                                        VALUES (?, ?) ";
                    
                                        $sentencia = $conexion->stmt_init();
                    
                                        if(!$sentencia->prepare($consulta)) {
                                            //$mensaje = $mensaje. " fallo la preparacion de la consulta para 
                                                      //  guardar ruta de carpeta de imagenes <br>";
                                        } else{
                                            $sentencia->bind_param("ss", $nombreArchivo, $id_publicacion);
                    
                                            $sentencia->execute();
                                            if($sentencia->affected_rows <= 0) {
                                                $mensaje = $mensaje." No se guardó la imagen<br>"; 
                                            }
                                            $sentencia->close(); 
                                        }

                                    } else {
                                        //$mensaje = $mensaje. " Hubo un error al mover el archivo.<br>";
                                    }
                                } else {
                                    //echo "Error al subir el archivo. Código de error: " . $errorArchivo;
                                }
                            }  
                        }  
                        if (isset($_POST['servicio'])){
                            foreach($_POST['servicio'] as $id_servicio){
                            
                                $consulta = "INSERT INTO  servicio_publicacion(id_publicacion, id_servicio)
                                VALUES (?, ?) ";

                                $sentencia = $conexion->stmt_init();

                                if(!$sentencia->prepare($consulta)) {
                                   // $mensaje = $mensaje. " fallo la preparacion de la consulta para guardar servicios seleccionados <br>";
                                } else{
                                    $sentencia->bind_param("ss",$id_publicacion, $id_servicio);

                                    $sentencia->execute();
                                    
                                    if($sentencia->affected_rows <= 0) {
                                        //echo"error guardando imagen<br>"; 
                                    }
                                    $sentencia->close();   
                                }                 
                            }  
                        
                        } 
                        if (isset($_POST['etiqueta'])){
                            foreach($_POST['etiqueta'] as $id_etiqueta){
                            
                                $consulta = "INSERT INTO  etiqueta_publicacion(id_publicacion, id_etiqueta)
                                VALUES (?, ?) ";

                                $sentencia = $conexion->stmt_init();

                                if(!$sentencia->prepare($consulta)) {
                                    //$mensaje = $mensaje. " fallo la preparacion de la consulta para guardar servicios seleccionados <br>";
                                } else{
                                    $sentencia->bind_param("ss",$id_publicacion, $id_etiqueta);

                                    $sentencia->execute();
                                    
                                    if($sentencia->affected_rows <= 0) {
                                        //echo"error guardando imagen<br>"; 
                                    }
                                    $sentencia->close();   
                                }                 
                            }  
                        
                        }  
                        
                        $mensaje = "Publicación creada con éxito!!";
                        $valido = "false";
                    } else{
                        $mensaje = $mensaje." No se guardarón los datos de la publicación.<br>"; // ver aqi
                        $valido = false; 
                    }
                }                              
            } else {

            }
        } else {
            $mensaje = $mensaje . "No puede realizar la publicación porque ya tiene una, debe verificar la cuenta para obtener más beneficios ";
            $valido = false;
        }
    } else {
        $mensaje = $mensaje . " Debe logearse para crear una publicación. <br>";
        $puedePublicar = false;
   }

    include "bd/cerrar_conexion.php";
?>

<!DOCTYPE>
<html>

    <head>
        <title>Vamos</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../static/css/style.css" type="text/css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
        <link href="../static/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <body class="background2">
        <header>
            <?php include("barraDeNavegacion.php"); ?>
        </header>

        <!--FORMULARIO-->
        <?php if($puedePublicar) { ?>
            <section>            
                <div class="container w-85" >

                    <div class=" col-md-12 text-center" style=" margin-top: 40px;">
                        <h2> Publicar </h2>
                    </div><br>

                    <form class="row g-3 " id="formulario" method="post" action="publicar.php"  enctype="multipart/form-data">

                        <div class="col-md-12">
                            <label for="titulo" class="form-label">T&iacute;tulo</label>
                            <input type="text" class="form-control" id="titulo" name = "titulo" 
                            pattern="[A-Za-z0-9 ]{2,90}" required>
                        </div>

                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripci&oacute;n</label>
                            <textarea class="form-control" id="descripcion" name = "descripcion" rows = "4" required></textarea>
                        </div>

                        <div class="col-md-12">
                            <label for="ubicacion" class="form-label">Ubicaci&oacute;n</label>
                            <textarea class="form-control" id="ubicacion" name = "ubicacion"  rows="3" 
                            pattern="^[A-Za-z0-9 .#' /-_]{2,300}" required></textarea>
                        </div>

                        <div class="col-md-3">
                            <label for="tiempo_minimo" class="form-label">Minimo Estadia </label>
                            <input type="text" class="form-control" id="tiempo_minimo" name = "tiempo_minimo" min="1" 
                            maxlength="30" pattern="^[1-9]{1,}" required>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="tiempo_maxino" class="form-label">M&aacute;ximo Estadia</label>
                            <input type="text" class="form-control" id="tiempo_maximo" name = "tiempo_maximo" min="1" 
                            maxlength="30" pattern="^[1-9]{1,}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="cupo" class="form-label">Cupo</label>
                            <input type="text" class="form-control" id="cupo" name = "cupo" min="1" maxlength="50"
                            pattern="^[1-9]{1,}" required>
                        </div>

                        <div class="col-md-3">
                            <label for="costo" class="form-label">Costo</label>
                            <input type="text" class="form-control" id="costo" name = "costo" 
                            pattern="^[0-9]{5,}"required>
                        </div>     
                        
                        <label>Etiquetas</label>
                        <?php                 
                            while($fila = $resultadoEtiqueta->fetch_array(MYSQLI_ASSOC)){
                               // var_dump($fila);
                                echo "<div class=\"col-md-2\">
                                    <div class=\"form-check\">
                                        <input class=\"form-check-input\" type=\"checkbox\" name = \"etiqueta[]\" id=\"flexCheckDefault\" 
                                        value = " . $fila['id'] . " >
                                        <label class=\"form-check-label\" for=\"flexCheckDefault\">"
                                            .$fila['nombre'].
                                        "</label>
                                    </div>                        
                                </div>";
                            }                      
                        ?><br><br>
                    
                        <label>Servicios</label>
                        <?php                 
                            while($fila = $resultadoServicio->fetch_array(MYSQLI_ASSOC)){
                               // var_dump($fila);
                                echo "<div class=\"col-md-2\">
                                    <div class=\"form-check\">
                                        <input class=\"form-check-input\" type=\"checkbox\" name = \"servicio[]\" id=\"flexCheckDefault\" 
                                        value = " . $fila['id'] . " >
                                        <label class=\"form-check-label\" for=\"flexCheckDefault\">"
                                            .$fila['nombre'].
                                        "</label>
                                    </div>                        
                                </div>";
                            }                      
                        ?><br><br>
                    
                        <div class="col-md-4">
                            <label for="formFile" class="form-label">Subir Fotos</label>
                            <input class="form-control" type="file" id="formFile"  name="imagenes[]" multiple accept="image/*">
                        </div>

                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-label">Fecha inicio publicaci&oacute;n</label>
                            <input type="date" class="form-control" id="fecha_inicio" name= "fecha_inicio" 
                            min="16" max="150" >
                        </div>
                    
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-label">Fecha fin publicaci&oacute;n</label>
                            <input type="date" class="form-control" id="fecha_fin" name= "fecha_fin" 
                            min="16" max="150" >
                        </div>
            
                        <div class="col-12 ">
                            <button type="submit" class="btn btn-secondary" id="btn_submit_form_evento" name = "enviar">ENVIAR</button>
                        </div>

                    </form><br>
                    <?php if(!$valido){?>
                        <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                        style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                            <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                            <div>
                                <H6><b><?php echo $mensaje ?></H6></b>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>                   
                        </div> 
                    <?php } ?>  
                </div>
            </section>

         <?php } else {?>
            <section class="todoElAlto">    
                <div class = "container w-75">
                    <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                    style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                        <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                        <div>
                            <H6><b><?php echo $mensaje ?></H6></b>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>            

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
            crossorigin="anonymous">
        </script>
        
    </body>
</html>