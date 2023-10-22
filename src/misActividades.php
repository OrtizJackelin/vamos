<?php 
    require_once ('sessionStart.php'); 
    
    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        var_dump($_SESSION);
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

    $consulta = "SELECT *
    FROM publicacion
    WHERE id_usuario = ?
    ORDER BY fecha_solicitud ASC";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
    } else {
        $sentencia->bind_param("s",$_SESSION['id']);
        $sentencia->execute();
        $resultadoPublicacion = $sentencia->get_result();
        $sentencia->close();
    }
 
    $consulta = "SELECT alquiler.*, publicacion.titulo, publicacion.ubicacion 
                FROM alquiler, publicacion 
                WHERE alquiler.id_usuario = ?
                and alquiler.id_publicacion = publicacion.id 
                ORDER BY fecha_inicio ASC";
                
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
    } else {
        $sentencia->bind_param("s",$_SESSION['id']);
        $sentencia->execute();
        $resultadoAlquiler = $sentencia->get_result();
        $sentencia->close();
    }

    $consulta = "SELECT alquiler.*, user.nombre, user.apellido, user.email, publicacion.titulo, publicacion.ubicacion
                FROM alquiler
                INNER JOIN publicacion ON alquiler.id_publicacion = publicacion.id
                INNER JOIN user ON alquiler.id_usuario = user.id
                WHERE publicacion.id_usuario = ? 
                AND alquiler.aprobado = 0";

    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
    } else {
        $sentencia->bind_param("s",$_SESSION['id']);
        $sentencia->execute();
        $resultadoSolicitud = $sentencia->get_result();
        $sentencia->close();
    }        
?>
<html>
<head>
    <title>Mis Actividades</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <link rel="stylesheet" href="../static/css/style2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../static/css/flatpickr.min.css">
    <link rel="stylesheet" href="../static/css/jquery-ui.min.css">
    <link rel="stylesheet" href="../static/css/jquery-ui.theme.css">
    <link href="../static/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script type = "text/javascript" src = "../static/js/code.jquery.com_jquery-3.7.1.min.js"></script>
    <script type = "text/javascript" src = "../static/js/jquery-ui.js"></script>
    <script type = "text/javascript" src = "../static/js/jquery-ui.min.js"></script>
    <script src="../static/js/flatpickr.js"></script>
    

    <script> 
        $( function() {
            $( "#tabs" ).tabs();
        } );

        function confirmarPublicacion(id, id_usuario, estado, id_publicacion){  
            console.log(id);          

            var parametros = {
                "id" : id,
                "idUsuario" : id_usuario,
                "estado" : estado,
                
            };
            $.ajax({
                data : parametros,
                url : 'verificarSolicitudAlquiler.php',
                type : 'post',
                beforeSend: function () {
                    $("#resultadoSol").html("procesando");
                },
                success: function (response) {
                    const posicionError = response.indexOf("Error");

                    // Actualiza el contenido antes de eliminar la fila
                    $("#resultadoSol").html(response);

                    if (posicionError == -1) {
                        console.log('eliminar fila: ' + id_publicacion);
                        const tabla = document.getElementById("solicitudes");
                        const filaAEliminar = document.getElementById("solicitud_fila_"+id_publicacion);

                        if (filaAEliminar) {
                            tabla.deleteRow(filaAEliminar.rowIndex);
                        }
                    } else {
                        console.log('posicion:' + posicionError);
                    }
                }
            });
        }
 
    </script>
   
</head>

<body>
    <header>
        <?php include("barraDeNavegacion.php"); ?><br><br>
    </header>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-1">Mis Publicaciones</a></li>
            <li><a href="#tabs-2">Mis Alquileres</a></li>
            <li><a href="#tabs-3">Solicitudes</a></li>
        </ul>
        <div id="tabs-1">
            <section class = "sectionPrincipal">
                <div class="container w-100" >    
                    
                    <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                        <h4> Mis Publicaciones</h4>
                    </div>
                    <div class = "table-responsive">
                        <table class="table table-striped table-hover" id = "misPublicaciones" name = "misPublicaciones">
                            
                            <tr>
                                <td>Publicaci&oacute;n N°</td>
                                <td>T&iacute;tulo</td>
                                <td>Ubicaci&oacute;n</td>
                                <td>Fecha Publicaci&oacute;n</td>
                                <td>Estatus</td>
                                <td colspan="3">Detalle De Publicaci&oacute;n</td>                              
                            </tr>

                            <?php while($fila = $resultadoPublicacion->fetch_array(MYSQLI_ASSOC)) { 
                                extract($fila);
                            ?>
                                <tr id="verificacion_fila_<?php echo $id; ?>">  
                                    <td><?php echo $id ?></td>                                
                                    <td><?php echo $titulo ?></td>
                                    <td><?php echo $ubicacion ?></td>
                                    <td><?php echo $fecha_solicitud ?></td>
                                    <td><?php switch ($estado) {
                                            case 0:
                                                echo "En proceso de revisión";
                                                break;
                                            case 1:
                                                echo "Aprobado/Publicado";
                                                break;
                                            case 2:
                                                echo "Rechazado";
                                                break;
                                            }                                
                                        ?>
                                    </td>
                                    <td><a href="detallePublicacion.php?id=<?php echo $id; ?> " target = "_blank"> Ver detalle
                                            <img src="../static/imagenes/redes/box-arrow-up-right.svg" alt="abrir en otra ventana">
                                        </a>
                                    </td>
                                            
                                </tr>
                            <?php                        
                            }  
                            ?>
                        </table>
                    </div>               
                    <span id = "resultadoPublicacion"> Nada aqui </span>
                </div>      
            </section>
        </div>
        <div id="tabs-2">
            <section class = "sectionPrincipal">
                <div class="container w-100" >            
                    <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                        <h4> Mis Aquileres </h4>
                    </div>
                    <div class = "table-responsive">
                        <table class="table table-striped table-hover" id = "misAlquileres" name = "misAlquileres">
                            
                            <tr>
                                <td>T&iacute;tulo</td>
                                <td>Ubicaci&oacute;n</td>
                                <td>Fecha inicio alquiler</td>
                                <td>Fecha fin alquiler</td>
                                <td>Costo por d&iacute;a</td>
                                <td>Estatus</td>
                                <td colspan="3">Detalle De Publicaci&oacute;n</td>                              
                            </tr>

                            <?php while($fila = $resultadoAlquiler->fetch_array(MYSQLI_ASSOC)) { 
                                extract($fila);
                            ?>
                                <tr>                                  
                                    <td><?php echo $titulo ?></td>
                                    <td><?php echo $ubicacion ?></td>
                                    <td><?php echo $fecha_inicio ?></td>
                                    <td><?php echo $fecha_fin ?></td>
                                    <td><?php echo $costo ?></td>
                                    <td><?php switch ($aprobado) {
                                            case 0:
                                                echo "En proceso de revisión";
                                                break;
                                            case 1:
                                                echo "Aprobado";
                                                break;
                                            case 2:
                                                echo "Rechazado";
                                                break;
                                            }                                
                                        ?>
                                    </td>
                                    <td><a href="detallePublicacion.php?id=<?php echo $id_publicacion; ?> " target = "_blank"> Ver detalle
                                            <img src="../static/imagenes/redes/box-arrow-up-right.svg" alt="abrir en otra ventana">
                                        </a>
                                    </td>
                                            
                                </tr>
                            <?php                        
                            }  
                            ?>
                        </table>
                    </div>
                    <span id = "resultadoAlquiler"> Nada aqui </span>
                </div>
            </section>
        </div>

        <div id="tabs-3">
            <section class = "sectionPrincipal">
                <div class="container w-100" >            
                    <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                        <h4> Solicitudes </h4>
                    </div>
                    <div class = "table-responsive">
                        <table class="table table-striped table-hover" id = "solicitudes" name = "solicitudes">
                            
                            <tr>
                                <td>Publicaci&oacute;n N°</td>                 
                                <td>T&iacute;tulo</td>
                                <td>Usuario</td>
                                <td>Email</td>
                                <td>Fecha inicio alquiler</td>
                                <td>Fecha fin alquiler</td>
                                <td>Costo/d&iacute;a</td>
                                <td colspan="3">Detalle de publicaci&oacute;n</td>                              
                            </tr>

                            <?php while($fila = $resultadoSolicitud->fetch_array(MYSQLI_ASSOC)) { 
                                extract($fila);
                                
                            ?>
                                <tr id = "solicitud_fila_<?php echo $id_publicacion;?>">    
                                    <td><?php echo $id_publicacion ?></td>                              
                                    <td><?php echo $titulo ?></td>
                                    <td><?php echo $nombre . " ". $apellido  ?></td>
                                    <td><?php echo $email  ?></td>
                                    <td><?php echo $fecha_inicio ?></td>
                                    <td><?php echo $fecha_fin ?></td>
                                    <td><?php echo $costo ?></td>
                                    <td><a href="detallePublicacion.php?id=<?php echo $id_publicacion; ?> " target = "_blank"> Ver detalle
                                            <img src="../static/imagenes/redes/box-arrow-up-right.svg" alt="abrir en otra ventana">
                                        </a>
                                    </td>
                                    <td>                         
                                        <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                            onclick = "confirmarPublicacion(<?php echo $id . ',' . $id_usuario . ',1' . ',' . $id_publicacion;?>)" 
                                            name = "aprobar"> Aprobar 
                                        </button>                            
                                    </td>
                                    <td>                    
                                        <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                            onclick = "confirmarPublicacion(<?php echo $id . ',' . $id_usuario . ',2'. ',' . $id_publicacion;?>)"
                                            name = "rechazar">Rechazar
                                        </button>                            
                                    </td>                           
                                </tr>
                            <?php                        
                            }  
                            ?>
                        </table>
                    </div>
                    <span id = "resultadoSol"> Nada aqui </span>
                </div>
            </section>
        </div>
    </div>
 
    

    

   

    <?php include "bd/cerrar_conexion.php"; ?>
    <!--Footer-->
    <footer>
        <?php include("../static/html/footer.html"); ?>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
</body>
</html>