<!DOCTYPE html>
<html lang="es">

<head>
    <title>Vamos</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <link rel="stylesheet" href="../static/css/style2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <header>
        <?php include("barraDeNavegacion.php"); ?>
    </header>

    <!--FORMULARIO-->
    <section>
        <?php

            $valido = true;
            $textoErrorTitulo = "";
            $textoError = "";
            $directorioDestino = "../static/imagenes/publicaciones/";
            $nombreArchivoTxt = "";
            /*if (isset($_POST['enviar'])){


                if (!isset($_POST['titulo'])){
                
                    echo "Debe ingresar un titulo";
                    $valido = false;
                }


                if (!isset($_POST['ubicacion'])){
                    echo "Debe ingresar una ubicacion";
                    $valido = false;                    
                }

                if (!isset($_POST['decripcion'])){
                    echo "Debe ingresar una ubicacion";
                    $valido = false;                    
                }

                if (!isset($_POST['costo'])){
                    echo "Debe ingresar un costo";
                    $valido = false;                    
                }

                if (!isset($_POST['cupo'])){
                    echo "Debe ingresar un cupo";
                    $valido = false;                    
                }

                if (!isset($_POST['tiempo_minimo_permanencia'])){
                    echo "Debe ingresar un tiempo minimo de permanencia";
                    $valido = false;                    
                }
                
                if (!isset($_POST['tiempo_maximo_permanencia'])){
                    echo "Debe ingresar un tirmpo maximo de permanencia";
                    $valido = false;                    
                }

                
            }*/

            if (isset($_POST['enviar']) && $valido){
                
                include "bd/conexion.php";

                if($conexion->connect_errno) {
                    echo"error, no se conecto <br>";
                    die("$conexion->connect_errno: $conexion->connect_errno");
                }else{

                    $consulta = "INSERT INTO  publicacion (titulo, descripcion, ubicacion, costo, 
                    cupo, tiempo_minimo, tiempo_maximo, fecha_inicio_publicacion, fecha_fin_publicacion, id_usuario)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?) ";

                    $sentencia = $conexion->stmt_init();

                    if(!$sentencia->prepare($consulta)) {
                        echo "fallo la preparacion de la consulta <br>";
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
                                    echo "Error al crear la carpeta '$rutaDestino '.";
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
                                            echo "El archivo se subió correctamente a: " . $rutaArchivo;

                                            $consulta = "INSERT INTO imagen(ruta, id_publicacion)
                                            VALUES (?, ?) ";
                        
                                            $sentencia = $conexion->stmt_init();
                        
                                            if(!$sentencia->prepare($consulta)) {
                                                echo "fallo la preparacion de la consulta <br>";
                                            } else{
                                                $sentencia->bind_param("ss", $nombreArchivo, $id_publicacion);
                        
                                                $sentencia->execute();
                                                if($sentencia->affected_rows <= 0) {
                                                    echo"error guardando imagen<br>"; 
                                                }
                                                $sentencia->close(); 
                                            }

                                        } else {
                                            echo "Hubo un error al mover el archivo.";
                                        }
                                    } else {
                                        //echo "Error al subir el archivo. Código de error: " . $errorArchivo;
                                    }
                                }  
                            }  
                            
                            foreach($_POST['servicio'] as $id_servicio){
                              
                                $consulta = "INSERT INTO  servicio_publicacion(id_publicacion, id_servicio)
                                VALUES (?, ?) ";
            
                                $sentencia = $conexion->stmt_init();
            
                                if(!$sentencia->prepare($consulta)) {
                                    echo "fallo la preparacion de la consulta <br>";
                                } else{
                                    $sentencia->bind_param("ss",$id_publicacion, $id_servicio);
            
                                    $sentencia->execute();
                                    
                                    if($sentencia->affected_rows <= 0) {
                                        echo"error guardando imagen<br>"; 
                                    }
                                    $sentencia->close();   
                                }                 
                            }  
                                                
                        }else{
                            echo"error guardando<br>"; // ver aqi 
                        }
                    }                           
                    
                }
                include "bd/cerrar_conexion.php";

                         
            } 

        ?>
        
        <div class="container w-75 ">

            <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                <h2> Publicar </h2>
            </div><br><br>

            <form class="row g-3 " id="formulario" method="post" action="publicar.php"  enctype="multipart/form-data">

                <div class="col-md-12">
                    <label for="titulo" class="form-label">T&iacute;tulo</label>
                    <input type="text" class="form-control" id="titulo" name = "titulo"  required>
                </div>

                <div class="col-md-12">
                    <label for="descripcion" class="form-label">Descripci&oacute;n</label>
                    <textarea class="form-control" id="descripcion" name = "descripcion" rows = "4" required></textarea>
                </div>

                <div class="col-md-12">
                    <label for="ubicacion" class="form-label">Ubicaci&oacute;n</label>
                    <textarea class="form-control" id="ubicacion" name = "ubicacion"  rows="3" required></textarea>
                </div>

                <div class="col-md-3">
                    <label for="tiempo_minimo" class="form-label">Minimo Estadia </label>
                    <input type="number" class="form-control" id="tiempo_minimo" name = "tiempo_minimo" min="1" 
                    maxlength="30" required>
                </div>
                
                <div class="col-md-3">
                    <label for="tiempo_maxino" class="form-label">M&aacute;ximo Estadia</label>
                    <input type="number" class="form-control" id="tiempo_maximo" name = "tiempo_maximo" min="1" 
                    maxlength="30" required>
                </div>

                <div class="col-md-3">
                    <label for="cupo" class="form-label">Cupo</label>
                    <input type="number" class="form-control" id="cupo" name = "cupo" min="1" maxlength="50"
                    required>
                </div>

                <div class="col-md-3">
                    <label for="costo" class="form-label">Costo</label>
                    <input type="number" class="form-control" id="costo" name = "costo" min="100" maxlength="800000"
                    required>
                </div>
    
            
                <p>Servicios</p>
                <?php
                    include "bd/conexion.php";

                    if($conexion->connect_errno) {
                        echo"error, no se conecto <br>";
                        die("$conexion->connect_errno: $conexion->connect_errno");
                    }else{
                    
                        $consulta = "SELECT id, nombre
                                    FROM servicio";        
                        $sentencia = $conexion->stmt_init();

                        if(!$sentencia->prepare($consulta)){
                            echo "fallo la preparacion de la consulta <br>";
                        }
                        else{
                            $sentencia->execute();
                            $resultado = $sentencia->get_result();
                            while($fila = $resultado->fetch_array(MYSQLI_ASSOC)){

                               echo "<div class=\"col-md-2\">
                                    <div class=\"form-check\">
                                        <input class=\"form-check-input\" type=\"checkbox\" name = \"servicio[]\" id=\"flexCheckDefault\" 
                                        value = " . $fila['id'] . ">
                                        <label class=\"form-check-label\" for=\"flexCheckDefault\">"
                                            .$fila['nombre'].
                                        "</label>
                                    </div>                        
                                </div>";
                            }                          
                        }                           
                      include "bd/cerrar_conexion.php";  
                    }

                ?><br><br>
            
                <div class="col-md-4">
                    <label for="formFile" class="form-label">Subir Fotos</label>
                    <input class="form-control" type="file" id="formFile"  name="imagenes[]" multiple accept="image/*">
                </div>

                <div class="col-md-4">
                    <label for="fecha_inicio" class="form-label">Fecha inicio publicaci&oacute;n</label>
                    <input type="date" class="form-control" id="fecha_inicio" name= "fecha_inicio" 
                     min="16" max="150" required>
                </div>
               
                <div class="col-md-4">
                    <label for="fecha_fin" class="form-label">Fecha fin publicaci&oacute;n</label>
                    <input type="date" class="form-control" id="fecha_fin" name= "fecha_fin" 
                     min="16" max="150" required>
                </div>
     
                <div class="col-12 ">
                    <button type="submit" class="btn btn-secondary" id="btn_submit_form_evento" name = "enviar">ENVIAR</button>
                </div>

            </form><br>

        </div>
    </section>


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