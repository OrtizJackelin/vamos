
<?php
    require_once ('sessionStart.php');

    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        var_dump($_SESSION);
    }

    $mensaje = "";


    try{
        include "bd/conexion.php";

    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }

    /////////////////////////////////////Modtrar Información de Publicación///////////////////////////////  
    $usuarioHabilitado = 1;
    $habilitado = "enable";

    $consulta = "SELECT *
                FROM publicacion
                WHERE id = ?";

    $sentencia = $conexion->stmt_init();

    if (!$sentencia->prepare($consulta)) {     
        echo "Fallo la preparación de la consulta <br>";
    } else {
        $sentencia->bind_param("s", $_GET['id']);
        $sentencia->execute();
        $resultado = $sentencia->get_result();
    
        if($resultado->num_rows==0){
            header("Location: index.php");
            exit;
        } else {

            if ($publicacion = $resultado->fetch_array(MYSQLI_ASSOC)) {
                extract($publicacion);
                $fechaInicio = $fecha_inicio_publicacion;
                $fechaFin = $fecha_fin_publicacion;
                
                if(isset($_SESSION['id'])){
                    if($_SESSION['id'] === $id_usuario){
                        $usuarioHabilitado = 0;
                        $habilitado = "disabled";
                    }
                } else {
                    $habilitado = "disabled";
                }

                // Obtener imágenes de esta publicación
                $consultaImagenes = "SELECT ruta
                                    FROM imagen
                                    WHERE id_publicacion = ?";
                $sentenciaImagenes = $conexion->stmt_init();
                
                if ($sentenciaImagenes->prepare($consultaImagenes)) {
                    $sentenciaImagenes->bind_param("s", $publicacion['id']);
                    $sentenciaImagenes->execute();
                    $resultadoImagenes = $sentenciaImagenes->get_result();
                    $sentenciaImagenes->close();
                
                    
                } else {
                    echo "Fallo la preparación de la consulta de imagen <br>";
                }
            }
        
            $consulta = "SELECT fecha_inicio , fecha_fin 
                        FROM alquiler 
                        WHERE id_publicacion = ? and aprobado = 1 and fecha_fin >= NOW()";

            $sentencia = $conexion->stmt_init();
        
            if(!$sentencia->prepare($consulta)){
                echo "fallo la preparacion de la consulta <br>";
            } else{                
                $sentencia->bind_param("s", $id);
                $sentencia->execute();
                $resultadoFechas = $sentencia->get_result();
                $sentencia->close();
                if($datos=$resultadoFechas->fetch_all(MYSQLI_ASSOC)){
                    //var_dump($datos);
                    $datosJson = json_encode($datos);
                    
                }
            }

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
                $resultadoServicios = $sentencia->get_result();
                $sentencia->close();
            }
        }
    }
    include "bd/cerrar_conexion.php";                     
?>

<html>
<head>
    <title>Detalle de la publicaci&oacute;n</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <link rel="stylesheet" href="../static/css/style2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
    <link href="../static/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script type = "text/javascript" src = "../static/js/code.jquery.com_jquery-3.7.1.min.js"></script>
    <script src="../static/js/flatpickr.js"></script>
    <link rel="stylesheet" href="../static/css/flatpickr.min.css">

    <script>   

    document.addEventListener("DOMContentLoaded", () => {

        var arregloEnJavaScript = <?php if(isset( $datosJson)){
                                            echo $datosJson;
                                        } else { 
                                            echo "[]";
                                        }
                                    ?>;
                                    
        var fechaMinimoPublicacion = <?php if(!isset($fechaInicio) || $fechaInicio === null){
                                                echo "\"today\"";
                                            } else {
                                                $fechaActual = new DateTime();
                                                $fechaObjeto = new DateTime($fechaInicio);
                                                if($fechaObjeto > $fechaActual){
                                                    echo "'" . $fechaInicio . "'";
                                                } else {
                                                    echo "\"today\"";
                                                }
                                            }?>;

        var fechaMaximoPublicacion = <?php if(!isset($fechaFin) || $fechaFin === null){
                                                echo "\"\"";
                                            } else {
                                                echo "'" . $fechaFin . "'";
                                            }?>;

        console.log(fechaMinimoPublicacion);
        console.log(fechaMaximoPublicacion);
        // Función para cambiar las claves en un objeto
        function cambiarClavesEnObjeto(objeto) {
            var objetoModificado = {};
            for (var clave in objeto) {
                if (objeto.hasOwnProperty(clave)) {
                    switch (clave) {
                        case "fecha_inicio":
                            objetoModificado["from"] = objeto[clave];
                            break;
                        case "fecha_fin":
                            objetoModificado["to"] = objeto[clave];
                            break;
                        default:
                            objetoModificado[clave] = objeto[clave];
                    }
                }
            }
            return objetoModificado;
        }

        // Cambiar las claves en cada objeto del arreglo
        var fechas_deshabilitadas = arregloEnJavaScript.map(cambiarClavesEnObjeto);
        
        flatpickr("#rangoFechas", {
            minDate: fechaMinimoPublicacion,
            maxDate: fechaMaximoPublicacion,
            mode: "range",
            disable: fechas_deshabilitadas,
            dateFormat: "Y/m/d",
            onValueUpdate: function(selectedDates, dateStr, instance) {

                var partes = dateStr.split(" to ");

                if (selectedDates.length > 1) {
                    document.getElementById("fechaInicio").value = partes[0];
                    document.getElementById("fechaFin").value = partes[1];
                }
            }
           
        });

       

    });

    function alquilar(){

            var idUsuario = document.getElementById("hidUsuario").value;
            var fechaInicio = document.getElementById("fechaInicio");
            var fechaFin = document.getElementById("fechaFin");
            var idUsuarioPublicacion = <?php echo $id_usuario?>;
            var usuarioHabilitado = <?php if(isset($usuarioHabilitado))
                                            echo $usuarioHabilitado;
                                       ?>;
              console.log(usuarioHabilitado);      
        if(idUsuario === null  || idUsuario === undefined){
            
            window.location.href = "iniciarSesion.php";    

        } 
        if(usuarioHabilitado){
            var parametros = {
                "idPublicacion" : document.getElementById("hidPublicacion").value,
                "idUsuario" : idUsuario,
                "costo" : document.getElementById("hcosto").value,
                "fechaInicio" : fechaInicio.value,
                "fechaFin" : fechaFin.value,
                "idUsuarioPublicacion" : idUsuarioPublicacion
            };
            $.ajax({
                data : parametros,
                url : 'alquilar.php',
                type : 'post',
                beforeSend : function(){
                    $("#resultado").html("procesando");
                },
                success : function(response){
                    $("#resultado").html(response);
                }
            });

        } else {
            console.log("no puede alquilar su propia propiedad");
        }
    }  
    </script>
</head>

<body>
    <header>
        <?php include("barraDeNavegacion.php"); ?><br><br>
    </header>

    <section>
        <div class="container w-100" >    
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
        </article><br><br>
             
        <article>
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
                    while($fila = $resultadoServicios->fetch_array(MYSQLI_ASSOC)){
                    echo "<div class=\"col-md-2\">
                            <div class=\"form-check\">
                                <input class=\"form-check-input\" type=\"checkbox\" name = \"interes[]\" id=\"flexCheckChecked\" 
                                value = " . $fila['nombre'] . " checked disabled>
                                <label class=\"form-check-label\" for=\"flexCheckChecked\">"
                                    .$fila['nombre'].
                                "</label>
                            </div>                        
                        </div>";
                    }
                ?> 
                
                <p><br><b>Reserve: </b></p>
                        
                <div class="col-md-4">
                    <label for="rangoFechas" class="form-label"><b>Seleccione Rango De Fecha: </b></label>
                    <input type="text" class="form-control" id="rangoFechas" name= "rangoFechas" 
                    min="16" max="150" required>
                </div>

                <div class="col-12 ">
                    <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                    onclick = "alquilar()" name = "enviar" <?php echo $habilitado; ?>>ENVIAR</button>
                </div>
              
                
            </form><br>  

            <input type = "hidden" id = "hidPublicacion" name = "hidPublicacion" value = "<?php echo $id; ?>">  
            <input type = "hidden" id = "hidUsuario" name = "hidUsuario" value = "<?php if(isset($_SESSION['id']))echo $_SESSION['id']; ?>">  
            <input type = "hidden" id = "hcosto" name = "hcosto" value = "<?php echo $costo; ?>">
            <input type = "hidden" id = "fechaInicio" name = "fechaInicio" >
            <input type = "hidden" id = "fechaFin" name = "fechaFin">

        </article>
        
        <span id = "resultado"> Nada aqui </span>
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