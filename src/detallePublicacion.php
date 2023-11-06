<?php
    require_once ('sessionStart.php');

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
    $visible = "none";
    $mostrarR = "none";
    $mostrarRespuesta = "none";
    

    $consulta = "SELECT p.*, u.nombre
                FROM publicacion p, user u
                WHERE p.id = ?
                AND u.id = p.id_usuario";

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
            
            if(isset($_SESSION['esVerificado']) && $_SESSION['esVerificado'] == 1){

                $consulta = "SELECT * 
                            FROM alquiler  
                            WHERE id_usuario = ? 
                            AND id_publicacion = ?
                            AND aprobado = 1
                            AND fecha_fin < CURRENT_TIMESTAMP";

                $sentencia = $conexion->stmt_init();
                if(!$sentencia->prepare($consulta)){
                    //echo "fallo la preparacion de la consulta para buscar alquileres <br>";
                }
                else{               
                    $sentencia->bind_param("ss", $_SESSION['id'], $_GET['id']);
                    $sentencia->execute();
                    $resultadoAlquiler = $sentencia->get_result();
                    $sentencia->close();
                    
                    if($resultadoAlquiler->num_rows>0){
                        $consulta = "SELECT *
                                    FROM reseña
                                    WHERE id_publicacion = ?
                                    AND id_usuario = ?";
                        $sentencia = $conexion->stmt_init();
                        if(!$sentencia->prepare($consulta)){
                           // echo "fallo la preparacion de la consulta para consulta reseña. <br>";
                        }
                        else{                
                            $sentencia->bind_param("ss", $_GET['id'], $_SESSION['id']);
                            $sentencia->execute();
                            $resultadoResena = $sentencia->get_result();
                            $sentencia->close();
                            if($resultadoResena->num_rows == 0){
                               // echo "no consiguio reseña";
                                $visible = "block";
                            }
                        }
                    }
                }
            } else {
                $consulta = "SELECT aprobado
                            FROM alquiler
                            WHERE id_usuario = ?
                            AND (aprobado = 2
                            OR fecha_fin < CURRENT_TIMESTAMP)
                            ORDER BY fecha_fin DESC LIMIT 1";

                $sentencia = $conexion->stmt_init();
                if(!$sentencia->prepare($consulta)){
                    //echo "fallo la preparacion de la consulta para consulta reseña. <br>";
                }
                else{                
                    $sentencia->bind_param("s", $_SESSION['id']);
                    $sentencia->execute();
                    $resultadoHabilitarAlquiler= $sentencia->get_result();
                    $sentencia->close();
                    if($resultadoHabilitarAlquiler->num_rows != 0){
                       // echo "no consiguio reseña";
                        $habilitado = "disabled";
                        
                    }
                   // var_dump($resultadoHabilitarAlquiler);
                }

            }

            $consulta = "SELECT reseña.*, user.nombre, user.apellido
                        FROM reseña, user
                        WHERE reseña.id_publicacion = ?
                        And user.id = reseña.id_usuario";
            
            $sentencia = $conexion->stmt_init();
            if(!$sentencia->prepare($consulta)){
                echo "fallo la preparacion de la consulta para buscar alquileres <br>";
            }
            else{               
                $sentencia->bind_param("s", $id );
                $sentencia->execute();
                $resultadoTraerResena = $sentencia->get_result();
                $sentencia->close();
                if($resultadoTraerResena->num_rows > 0){
                    $mostrarR = "block";
                } 
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
                    
                    // Obtener los valores de las fechas desde los campos de entrada
                    var fechainicioStr = partes[0];
                    var fechaFinStr = partes[1];
                    
                    // Convertir las fechas de texto a objetos Date
                    var fecha1 = new Date(fechainicioStr);
                    var fecha2 = new Date(fechaFinStr);
                    
                    // Realizar la resta de fechas (en milisegundos)
                    var diferenciaEnMilisegundos = fecha2 - fecha1;
                    
                    // Calcular la diferencia en días
                    var diferenciaEnDias = diferenciaEnMilisegundos / (1000 * 60 * 60 * 24);
                    console.log(diferenciaEnDias);

                    var montoTotal = diferenciaEnDias * <?php echo$costo?>;
                    
                    // Mostrar la diferencia
                    // Formatear el número con dos decimales y separadores de miles
                    const numeroFormateado = montoTotal.toLocaleString('en-US', { style: 'currency', currency: 'USD', minimumFractionDigits: 2 });
                    $("#montoTotal").val(numeroFormateado);

                }
            }
           
        });
      
        $("#reserveCantidadPersonas").on("blur", function() {
        // Obtiene el valor del campo
            var valor = $(this).val();
            resultado = true;
            
            // Realiza la verificación que desees
            if (valor === "") {
                alert("El campo está vacío. Por favor, ingresa un valor.");
                resultado = false;
            } 
            if(!validarCantidadPersonas(valor)){
                alert("El campo debe contener solo numeros.");
                resultado = false;
            }
            if(valor > <?php echo $cupo ?>){
                alert("Excede la cantidad de personas permitidas.");
                resultado = false;
            }
            if(resultado){      
                $("#reserveCantidadPersonas").css("border-color", "#ced4da");
            }else{    
                $("#reserveCantidadPersonas").css("border-color", "crimson");
            }
        });


    });

    function validarCantidadPersonas(numero) {
        const regex = /^[1-9]\d*$/;
        return regex.test(numero);
    }

    function resenar(){
        var valorSeleccionado = 0;
        var comentario = $("#comentar").value;
        if(comentario == ""){
            alert("el mensaje está vacío");
        } else {
        
            // Encuentra el radio button seleccionado dentro del grupo                   
            var valorSeleccionado = $('input[name=rating]:checked').val();
            // Verifica si hay un radio button seleccionado
            if (valorSeleccionado.length > 0) {      
                console.log('El radio button seleccionado tiene el valor: ' + valorSeleccionado);
            } else {
                console.log('Ningún radio button seleccionado');
            }
            console.log(valorSeleccionado);
            var parametros = {
                "comentario" : document.getElementById("comentar").value,
                "calificacion" : valorSeleccionado, 
                "idUsuario" : <?php echo $_SESSION['id'] ?>,
                "idPublicacion" : <?php echo $_GET['id'] ?>
            };
            $.ajax({
                data : parametros,
                url : 'guardarResena.php',
                type : 'post',
                beforeSend : function(){
                    $("#resultadoResena").html("procesando");
                },
                success : function(response){
                    
                    const subcadena = "Error";
                    const posicion = response.indexOf(subcadena);
                    $("#resultadoResena").html(response);
                    if (posicion == -1) {
                      $('#divResena').hide();
                    }
                }
            });

        }
    }

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

<body class="background2" >
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
            <div class = "row g-0">
                <div class = "col-md-8" style=" border-radius:10px; margin-bottom: 20px;">
                    <div class= "row g-3  " style=" border-radius:10px; margin-bottom: 20px;">
                        
                        <div class="col-md-10">
                            <label id = "titulo"><b>T&iacutetulo:</b></label>
                            <input type = "text" id = "titulo" class="form-control" 
                            value = "<?php echo $titulo ?>" disabled>                       
                        </div> 

                        <div class="col-md-10">
                            <label id = "descripcion"><b>Descripci&oacute;n:</b></label>
                            <input type = "text" id = "descripcion" class="form-control" 
                                value = " <?php echo $descripcion ?>" disabled>                        
                        </div> 

                        <div class="col-md-10">
                            <label for = "ubicacion"><b>Ubicaci&oacute;n:</b></label>
                            <input type = "text" id = "ubicacion" class="form-control" 
                                value = " <?php echo $ubicacion ?>" disabled>                        
                        </div> 

                        <div class="col-md-6">
                            <label for = "disponible"><b>Disponible: </b></label>
                            <input type = "text" id = "disponible"class="form-control" 
                                value = "<?php echo "del  ".date("d/m/Y", strtotime($fecha_inicio_publicacion)).
                                "  al  ". date("d/m/Y", strtotime($fecha_fin_publicacion));?>" disabled>                        
                        </div> 

                        <div class="col-md-4">
                            <label for = "cantidadPersonas"><b>Personas Permitidas:</b></label>
                            <input type = "text" id = "cantidadPersonas" class="form-control" 
                                value = " <?php echo $cupo ?>" disabled>                        
                        </div> 

                        <div class="col-md-6">
                            <label id = "serviciosDisponobles"><b>Servicios Disponibles:</b></label>
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
                        </div> 

                        <div class="col-md-4">
                            <label id = "costo"><b>Costo / d&iacute;a:</b></label>
                            <input type = "text" id = "costo" class="form-control" 
                                value = " $ <?php echo $numeroFormateado = number_format($costo , 2, '.', ',')?>" disabled>                        
                        </div> 

                    </div>  
                </div>

                <div class = "col-md-4">                    
                    
                    <div class="row g-3" style="border-block: 3px solid green; border-radius:10px; margin-bottom: 20px;">
                        <label><b><h5>Reserve:</h5></b></label>                    
                        <div class="col-md-12">
                            <label for="rangoFechas" class="form-label"><b>Seleccione Rango De Fecha: </b></label>
                            <input type="text" class="form-control" id="rangoFechas" name= "rangoFechas" 
                            min="16" max="130" required  <?php echo $habilitado; ?>>
                        </div>
                    
                        <div class="col-md-12">
                            <label for="reserveCantidadPersonas" class="form-label"><b>Cantidad de Personas: </b></label>
                            <input type="text" class="form-control" id="reserveCantidadPersonas" name= "reserveCantidadPersonas" 
                            min="16" max="130" pattern="^[1-9]\d*$" required  <?php echo $habilitado; ?>>
                        </div>
                
                        <div class="col-md-12">
                            <label for="montoTotal" class="form-label"><b>Monto Total: </b></label>
                            <input type="text" class="form-control" id="montoTotal" name= "montoTotal" 
                            min="16" max="130" disabled >
                        </div>          
            
                        <div class="col-md-4 " style = "margin-bottom: 20px;">
                            <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                            onclick = "alquilar()" name = "enviar"  <?php echo $habilitado; ?> >ENVIAR</button>
                        </div>
                    
                    </div> 
                </div>
            </div>             
        
            <input type = "hidden" id = "hidPublicacion" name = "hidPublicacion" value = "<?php echo $id; ?>">  
            <input type = "hidden" id = "hidUsuario" name = "hidUsuario" 
                value = "<?php if(isset($_SESSION['id']))echo $_SESSION['id']; ?>">  
            <input type = "hidden" id = "hcosto" name = "hcosto" value = "<?php echo $costo; ?>">
            <input type = "hidden" id = "fechaInicio" name = "fechaInicio" >
            <input type = "hidden" id = "fechaFin" name = "fechaFin">
             
            <span id = "resultado"></span>
        </article>

        <article >    
            <h4><p style="display: 
                <?php if ($visible === "block" || $mostrarR === "block") { echo "block"; } else { echo "none"; } ?>">
                <b>Reseñas</b>
            </p></h4>
            <?php while($fila = $resultadoTraerResena->fetch_array(MYSQLI_ASSOC)){
                extract($fila); 
                if($respuesta != NULL && $respuesta !=""){
                    $mostrarRespuesta = "block";
                }
                ?>
                
                <div class = "row g-2" style="border-block: 1px solid gray; border-radius:10px; margin-bottom: 20px;
                    display : <?php echo $mostrarR?>">

                    <div class = "col-md-4" id = "mostrarResena">
                        <?php if($calificacion > 0){
                            for($i = 0; $i < $calificacion; $i++){ ?>                            
                                <img src="../static/imagenes/redes/star-fill.svg" alt="star">
                            <?php    
                            }
                        }?>                            
                    </div>

                    <div class = "col-md-12">
                        <label id = "fechaComentario"><b><?php echo $fecha_comentario ?></b></label>
                        <label id = "usuarioCliente"><b><?php echo $nombre . " " . $apellido?></b></label>
                        <label id = "comentario"><?php echo $comentario ?></label>
                        <div style = "display:<?php echo $mostrarRespuesta?>">
                            <label id = "fechaRespuesta"><b><?php echo $fecha_respuesta ?></b></label>
                            <label><b>@<?php echo $publicacion['nombre']. ": " ?></b></label>
                            <label id = "respuesta"><?php echo $respuesta ?></label>
                        </div>
                    </div>
    
                </div>
            <?php }; ?>

            <div class = "row g-0" style="border-block: 1px solid gray; border-radius:10px; margin-bottom: 20px; 
                display:<?php echo $visible?>" id = "divResena">
                 
                <div class = "col-md-5" style=" border-radius:10px; margin-bottom: 20px;">
                        <label for = "star1"><b>Calificar:</b><br><br><br></label> 
                        <input type="radio" class = "start" id="star1" name="rating" value="1">
                        <label for="star1" class="star-label">1★</label>

                        <input type="radio" class = "start" id="star2" name="rating" value="2">
                        <label for="star2" class="star-label">2★</label>

                        <input type="radio" class = "start" id="star3" name="rating" value="3">
                        <label for="star3" class="star-label">3★</label>

                        <input type="radio" class = "start" id="star4" name="rating" value="4">
                        <label for="star4" class="star-label">4★</label>

                        <input type="radio" class = "start" id="star5" name="rating" value="5">
                        <label for="star5" class="star-label">5★</label>
                </div>  
    
                <div class = "col-md-7" style=" border-radius:10px; margin-bottom: 20px;">
                    <div class= "row g-0  " style=" border-radius:10px; margin-bottom: 20px;">  
                        <label for="comentar" class="form-label"><b>Comentar: </b></label>
                        <textarea class="form-control" id="comentar" name="comentar" rows="2"></textarea>
                    </div>
                </div>
                <div class="col-md-4 " style = "margin-bottom: 20px;">
                    <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                            onclick = "resenar()" name = "enviar" >ENVIAR
                    </button>
                </div>
                <span id = "resultadoResena"></span>
                          
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