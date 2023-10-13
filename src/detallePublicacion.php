<html>
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
        <?php include("barraDeNavegacion.php"); ?><br><br>
    </header>

    <section>
        <div class="container w-100" >    

        <?php
            $se_conecto = false;
            try{
                include "bd/conexion.php";
                $se_conecto= true;
            } catch (mysqli_sql_exception $e) {
                $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
                // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
                 header("Location: error.php?mensaje=" . urlencode($mensajeError));
                   
            }

            if($se_conecto){
                
            if(!isset($_GET['id'])){
                header("Location: index.php");
                exit;
            }          

            $consulta = "SELECT *
                        FROM publicacion
                        WHERE id = ?";

            $sentencia = $conexion->stmt_init();

            if (!$sentencia->prepare($consulta)) {
                ?>
                <div class="alert alert-primary d-flex align-items-center" role="alert" style = "margin-top: 20px; margin-bottom: 5px">
                    <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                    <div>
                        <H6><b>Fallo la preparación de la consulta <br>"</H6></b>
                    </div>
                </div> 
                <?php 
                echo "Fallo la preparación de la consulta <br>";
            } else {
                $sentencia->bind_param("s", $_GET['id']);
                $sentencia->execute();
                $resultado = $sentencia->get_result();
              
                if($resultado->num_rows==0){
                    header("Location: index.php");
                    exit;
                }

                if ($publicacion = $resultado->fetch_array(MYSQLI_ASSOC)) {
                    //var_dump($publicacion);
                    extract($publicacion);
                    // Obtener imágenes de esta publicación
                    $consultaImagenes = "SELECT ruta
                                        FROM imagen
                                        WHERE id_publicacion = ?";
                    $sentenciaImagenes = $conexion->stmt_init();
                    
                    if (!$sentenciaImagenes->prepare($consultaImagenes)) {
                        echo "Fallo la preparación de la consulta de imagen <br>";
                    } else {
                        $sentenciaImagenes->bind_param("s", $publicacion['id']);
                        $sentenciaImagenes->execute();
                        $resultadoImagenes = $sentenciaImagenes->get_result();
                        
                    ?>

                    <article>
                       
                        <p id = "inicio"><H3><?php echo $publicacion['titulo'] ?></H3></p>

                        <div id="carousel" class="carousel slide mx-auto" data-bs-ride="carousel">
                            <!--<div class="carousel-indicators">
                                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                
                            </div>-->
                            <div class="carousel-inner">
                            
                            <?php
                                $first = true; // Variable para controlar la clase "active" en el primer elemento
                                if ($resultadoImagenes->num_rows == 0) {
                                    echo "<img src=\"../static/imagenes/nofoto.jpg\" class=\"d-block w-100 \" 
                                    id = \"imagenCarousel\" alt=\"...\" >";
                                }
                                while ($nombre = $resultadoImagenes->fetch_array(MYSQLI_ASSOC)) {
                                    // Agregar la clase "active" solo al primer elemento
                                    $activeClass = $first ? 'active' : '';
                                    echo "<div class=\"carousel-item $activeClass\">
                                            <img src=\"../static/imagenes/publicaciones/" . $publicacion['id'] . "/" . $nombre['ruta'] . "\" 
                                            class=\"d-block w-100 img-fluid \"  alt=\"...\" id = \"imagenCarousel\">
                                        </div>";
                                    $first = false; // Desactivar la clase "active" después del primer elemento
                                }
                            ?>          
            
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        </div>
                    </article>
                    <?php
                    }
                }
            }
            ?>              
            <br><br><article>
                <form class="row g-3 " id="formulario" method="post" action="publicar.php"  enctype="multipart/form-data">
                    
                    <div class="col-md-12">
                        <label"><?php echo $descripcion ?></label>                        
                    </div> 

                    <div class="col-md-12">
                        <label"><b>Ubicaci&oacute;n: </b><?php echo $ubicacion ?></label>                        
                    </div>  
                    <div class="col-md-12">
                        <label"> <b>Disponible :</b> 
                        desde el <?php echo date("d/m/Y", strtotime($fecha_inicio_publicacion));?> 
                        al <?php echo date("d/m/Y", strtotime($fecha_fin_publicacion)) ?></label>                        
                    </div> 

                    <div class="col-md-12">
                        <label"><b>Cantidad de personas permitidas: </b><?php echo $cupo ?></label>                        
                    </div> 

                    <div class="col-md-12">
                        <label"><b>Costo por d&iacute;a: </b><?php echo $costo ?></label>                        
                    </div> 

               

                    <p><b>Servicios que ofrece el lugar: </b></p>
                    
                    <?php
                     
                        if($conexion->connect_errno) {
                            echo"error, no se conecto <br>";
                            die("$conexion->connect_errno: $conexion->connect_errno");
                        }else{
                        
                            $consulta = "SELECT nombre
                                        FROM servicio_publicacion, servicio
                                        WHERE servicio_publicacion.id_servicio=servicio.id 
                                        and servicio_publicacion.id_publicacion = ?";       
                            $sentencia = $conexion->stmt_init();

                            if(!$sentencia->prepare($consulta)){
                                echo "fallo la preparacion de la consulta <br>";
                            }
                            else{
                                
                                $sentencia->bind_param("s", $_GET['id']);
                                $sentencia->execute();
                                $resultado = $sentencia->get_result();
                                while($fila = $resultado->fetch_array(MYSQLI_ASSOC)){

                                echo "<div class=\"col-md-2\">
                                        <div class=\"form-check\">
                                            <input class=\"form-check-input\" type=\"checkbox\" name = \"interes[]\" id=\"flexCheckChecked\" 
                                            value = " . $fila['nombre'] . " checked>
                                            <label class=\"form-check-label\" for=\"flexCheckChecked\">"
                                                .$fila['nombre'].
                                            "</label>
                                        </div>                        
                                    </div>";
                                }                          
                            }                           
                            include "bd/cerrar_conexion.php";
                        }
                    }
                    ?> 
                    
                     <p><br><b>Reserve: </b></p>
                                          
                        <div class="col-md-4">
                            <label for="reserva_desde" class="form-label"><b>Desde: </b></label>
                            <input type="date" class="form-control" id="reserva_desde" name= "reserva_desde" 
                            min="16" max="150" required>
                        </div>
                
                        <div class="col-md-4">
                            <label for="reserva_hasta" class="form-label"><b>Hasta: </b></label>
                            <input type="date" class="form-control" id="reserva_hasta" name= "reserva_hasta" 
                            min="16" max="150" required>
                        </div><br><br>

                        <div class="col-md-12 ">
                            <button type="submit" class="btn btn-secondary" id="btn_submit_form_evento" name = "enviar">RESERVAR</button>
                        </div>
                    
                  
                   
                    
                   
                </form><br>                       
            </article>
        </div>
    </section>
    <!--Footer-->
    <footer>
        <?php include("../static/html/footer.html"); ?>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
</body>
</html>