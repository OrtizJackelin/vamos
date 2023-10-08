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
            <?php include("barraDeNavegacion.php"); ?>

            <div class = "container">
                <form class="d-flex flex-row-reverse justify-content-center  bd-highlight mb-5" role="search">
                    <div class="d-flex flex-row bd-highlight ">
                        <div class="p-2 bd-highlight">
                            <input class="form-control me-auto" type="search" placeholder="Buscar" aria-label="Buscar" size="40px">
                        </div>
                        <div class="p-2 bd-highlight">
                            <button class="btn btn-outline-success" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>

        </header>

        <section>

            <div class="container" style="margin-bottom: 10px;">
                <p>
                    <button class="btn ;" id="botonFiltro" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                        Mostrar Filtros
                    </button>
                </p>

                <div class="collapse" id="collapseExample">

                    <div class="row g-3">
                        <di class="col-3">
                            <label for="inputFiltroCiudad" class="form-label">Ciudad</label>
                            <select id="inputFiltroCiudad" class="form-select">
                                <option value="1">Todas</option>
                            </select>
                        </di>
                        <div class="col-3">
                            <label for="inputFiltroDistancia" class="form-label">Distancia</label>
                            <select id="inputFiltroDistancia" class="form-select">
                                <option value="1">Todas</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container w-100" >
     
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4" >
                <?php
                    include "bd/conexion.php";

                    if ($conexion->connect_errno) {
                        echo "Fallo la conexión";
                        die("$conexion->connect_errno:$conexion->connect_errno");
                    } else {
                        $consulta = "SELECT id, ubicacion, costo, fecha_inicio_publicacion, fecha_fin_publicacion
                                    FROM publicacion";

                        $sentencia = $conexion->stmt_init();
                        if (!$sentencia->prepare($consulta)) {
                            echo "Fallo la preparación de la consulta <br>";
                        } else {
                            $sentencia->execute();
                            $resultado = $sentencia->get_result();
                           

                            while ($publicacion = $resultado->fetch_array(MYSQLI_ASSOC)) {
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
                                    <!--<article>-->
                                   
                                    <div class="card bore border-2 border-end" style="width: 18rem; margin-right: 10px;">
                                        <?php
                                        if($resultadoImagenes->num_rows==0) {
                                            echo "<img src=\"../static/imagenes/nofoto.jpg\" class=\"card-img-top\" alt=\"...\" >";
                                        }
                                        else {
                                            $nombre = $resultadoImagenes->fetch_array(MYSQLI_ASSOC); 

                                            echo "<img src=\"../static/imagenes/publicaciones/" . $publicacion['id']. "/" . $nombre['ruta'] . "\" 
                                            class=\"card-img-top \" alt=\"...\" >";
                                        }
                                        ?>
                                        <div class="card-body" style="background-color: rgb(223, 221, 221);">
                                            <p class="card-text"> Ubicación: <?php echo $publicacion['ubicacion'] ?> </p>
                                            <p class="card-text"> Disponible: <?php echo $publicacion['fecha_inicio_publicacion'] . " al " . $publicacion['fecha_fin_publicacion'] ?> </p>
                                            <p class="card-text"> Costo: <?php echo $publicacion['costo'] ?> </p>
                                            <p><a href="publicacion.php?id=<?php echo $publicacion['id'] ?> " class = "buttom" >Ir a publicaci&oacute;n</a> </p>
                                        </div>
                                    </div>
                                      
                                    <!--</article>-->
                                    <?php
                                }
                               // <div class="card" style="width: 18rem;">
 
                            }
                        }
                        include "bd/cerrar_conexion.php";
                    }
                ?>
                </div>
            </div><br><br>
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