<?php
    require_once ('sessionStart.php');

    try{
        include "bd/conexion.php";
        //throw new Exception("Error Processing Request", 1);        
    
    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }

    $consulta = "SELECT *
                FROM publicacion
                WHERE estado =1 and (fecha_fin_publicacion >= NOW() || fecha_inicio_publicacion IS NULL AND fecha_fin_publicacion  IS NULL)";

    $sentencia = $conexion->stmt_init();
    if (!$sentencia->prepare($consulta)) {
        echo "Fallo la preparación de la consulta <br>";
    } else {
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $sentencia->close();
    }

?>

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

        <section class = "sectionPrincipal">

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

            <div  style = "display:flex; justify-content:center; aling-item:center; width:100%;padding:10px; " >
     
                <div style="display:flex; justify-content:center; flex-wrap:wrap; "  >

                <?php while ($publicacion = $resultado->fetch_array(MYSQLI_ASSOC)) {
            
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

                    <div class="card bore border-2 border-end" style="width: 20rem; margin: 10px;">
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
                            <a href="detallePublicacion.php?id=<?php echo $publicacion['id'] ?> "
                            class = "btn" id = "btnCard" >Ir a publicaci&oacute;n
                            </a>                     
                        </div>
                    </div>
                        
                <?php  } }  include "bd/cerrar_conexion.php" ?>
                
                </div>

            </div><br><br>
        </section>
        <!--Footer-->
        <footer>
            <?php include("../static/html/footer.html"); ?>
        </footer>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
        </script>
    </body>
</html>