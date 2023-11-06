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
                FROM verificacion_cuenta
                WHERE estado = 0
                ORDER BY fecha_solicitud ASC";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
    } else {
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $sentencia->close();
    }
    
    $consulta = "SELECT *
                FROM publicacion
                WHERE estado = 0
                ORDER BY fecha_solicitud ASC";
    $sentencia = $conexion->stmt_init();
    if(!$sentencia->prepare($consulta)){
        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
    } else {
        $sentencia->execute();
        $resultadoPublicacion = $sentencia->get_result();
        $sentencia->close();
    }

   // include "bd/cerrar_conexion.php";      
?>
<html>
<head>
    <title>Solicitudes Administrador</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
    <link href="../static/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../static/css/jquery-ui.min.css">
    <link rel="stylesheet" href="../static/css/jquery-ui.theme.css">
    <script type = "text/javascript" src = "../static/js/code.jquery.com_jquery-3.7.1.min.js"></script>
    <script type = "text/javascript" src = "../static/js/jquery-ui.js"></script>
    <script type = "text/javascript" src = "../static/js/jquery-ui.min.js"></script>
    <script src="../static/js/flatpickr.js"></script>
    <link rel="stylesheet" href="../static/css/flatpickr.min.css">
    <script> 
            $( function() {
                $( "#tabs" ).tabs();
            } );
        document.addEventListener("DOMContentLoaded", () => {

            // Fecha inicial 
            var fechaInicial = new Date(); 

            // Se Calcula la fecha final sumando un año a la fecha inicial
            var fechaFinal = new Date(fechaInicial);
            fechaFinal.setFullYear(fechaFinal.getFullYear() + 1);

            // Convierte las fechas a cadenas en el formato "Y/m/d"
            var fechaInicialStr = fechaInicial.toISOString().slice(0, 10);
            var fechaFinalStr = fechaFinal.toISOString().slice(0, 10);

            // Inicializa Flatpickr con las fechas por defecto
            // Inicializa Flatpickr con la fecha por defecto
            flatpickr("#fechaVencimiento", {
                minDate: "today",
                dateFormat: "Y-m-d",
                defaultDate: fechaFinalStr, // Solo una fecha
                onValueUpdate: function (selectedDates, dateStr, instance) {
                    // Aquí almacenamos la fecha seleccionada en un campo oculto
                    document.getElementById("fechaFin").value = dateStr; 
                    
                }
            });
        });

        function confirmarVerificacion(id, id_usuario, estado){  
            var fechaVencimiento = document.getElementById("fechaVencimiento").value;
            console.log(id);
            console.log(fechaVencimiento);

            var parametros = {
                "id" : id,
                "idUsuario" : id_usuario,
                "estado" : estado,
                "fechaVencimiento" : fechaVencimiento,
            };
            $.ajax({
                data : parametros,
                url : 'verificarCuenta.php',
                type : 'post',
                beforeSend : function(){
                    $("#resultado").html("procesando");
                },
                success : function(response){
                    //$("#resultado").html(response);
                    const subcadena = "Error";
                    const posicion = response.indexOf(subcadena);

                    // Actualiza el contenido antes de eliminar la fila
                    $("#resultado").html(response);

                    if (posicion == -1) {
                        console.log('eliminar fila: ' + id);
                        const tabla = document.getElementById("solicitudVerificacion");
                        const filaAEliminar = document.getElementById("verificacion_fila_" + id);

                        if (filaAEliminar) {
                            tabla.deleteRow(filaAEliminar.rowIndex);
                        }
                    } else {
                        console.log('posicion:' + posicion);
                    }
                }
            });
        }

        function confirmarPublicacion(id, id_usuario, estado){  
            console.log(id);
           

            var parametros = {
                "id" : id,
                "idUsuario" : id_usuario,
                "estado" : estado,
              
            };
            $.ajax({
                data : parametros,
                url : 'verificarPublicacion.php',
                type : 'post',
                beforeSend: function () {
                    $("#resultadoVerificarPublicacion").html("procesando");
                },
                success: function (response) {
                    const subcadena = "Error";
                    const posicion = response.indexOf(subcadena);

                    // Actualiza el contenido antes de eliminar la fila
                    $("#resultadoVerificarPublicacion").html(response);

                    if (posicion == -1) {
                        console.log('eliminar fila: ' + id);
                        const tabla = document.getElementById("solicitudPublicacion");
                        const filaAEliminar = document.getElementById("publicacion_fila_" + id);

                        if (filaAEliminar) {
                            tabla.deleteRow(filaAEliminar.rowIndex);
                        }
                    } else {
                        console.log('posicion:' + posicion);
                    }
                }
            });

        }

    </script>
   
</head>

<body class ="background2">
    <header>
        <?php include("barraDeNavegacion.php"); ?><br><br>
    </header>

    <div class="todoElAlto" id="tabs">
        <ul>
            <li><a href="#tabs-1">Solicitudes: Verificaci&oacute;n de Cuentas</a></li>
            <li><a href="#tabs-2">Solicitudes: Pubicaciones</a></li>
        </ul>
        <div id="tabs-1">  
            <div class="container-fluid" >               
                <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                    <h4> Solicitudes: Verificaci&oacute;n de Cuentas</h4>
                </div>
                <div class = "table-responsive">
                    <table class="table table-striped table-hover" id = "solicitudVerificacion" name = "solicitudVerificacion">
                        
                        <tr>
                            <td>Solicitud N°</td>
                            <td>Usuario</td>
                            <td>Documento</td>
                            <td>Comentario De Usuario</td>
                            <td>Fecha De Solicitud</td>
                            <td colspan="3">Fecha De Vencimiento</td>
                                    
                        </tr>
                        <?php while($fila = $resultado->fetch_array(MYSQLI_ASSOC)) { 
                            extract($fila);
                            $consulta = "SELECT nombre
                            FROM user
                            WHERE id = ?";
                        
                            $sentencia = $conexion->stmt_init();
                            if(!$sentencia->prepare($consulta)){
                                $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
                            } else {
                                $sentencia->bind_param("s", $id_usuario);
                                $sentencia->execute();
                                $resultado = $sentencia->get_result();
                                $sentencia->close();
                                if($filaNombre = $resultado->fetch_array(MYSQLI_ASSOC)){ ?>
                                    <tr id="verificacion_fila_<?php echo $id; ?>">  
                                        <td><?php echo $id ?></td>                                
                                        <td><?php echo $filaNombre['nombre'] ?></td>
                                        <td><img src=../static/imagenes/documentoUsuarios/<?php echo $documento ?>
                                                class="card-img-top" alt="documento">
                                        </td>
                                        <td><?php echo $comentario ?></td>
                                        <td><?php echo $fecha_solicitud ?></td>
                                        <td>
                                            <label for="fechaVencimiento" class="form-label"></label>
                                            <input type="text" class="form-control" id="fechaVencimiento" name= "fechaVencimiento" 
                                            min="16" max="120" required>                                      
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                                onclick = "confirmarVerificacion(<?php echo $id . ',' . $id_usuario . ',1'; ?>)" 
                                                name = "aprobar"> Aprobar 
                                            </button>
                                        </td>
                                        <td>
                                            <div class="col-12 ">
                                                <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                                onclick = "confirmarVerificacion(<?php echo $id . ',' . $id_usuario . ',2 '; ?>)">Rechazar</button>
                                            </div>
                                        </td>
                                    
                                    </tr>
                                <?php   
                                }
                            }
                        }   ?>
                    </table>
                </div>
                <span id = "resultado"></span>
                <input type = "hidden" id = "fechaInicio" name = "fechaInicio" >
                <input type = "hidden" id = "fechaFin" name = "fechaFin">
            </div>
        </div>
            

        <div id="tabs-2"> 
            <div class="container-fluid" >    
                
                <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                    <h4> Solicitudes: Pubicaciones </h4>
                </div>
                <div class = "table-responsive">
                    <table class="table table-striped table-hover" id = "solicitudPublicacion" name = "solicitudPublicacion">
                        
                        <tr>
                            <td>Publicaci&oacute;n N°</td>                    
                            <td>Usuario</td>
                            <td>T&iacute;tulo de Publicaci&oacute;n</td>
                            <td>Fecha De Solicitud</td>
                            <td colspan="3">Detalle De Publicaci&oacute;n</td>
                        
                        </tr>
                        <?php while($fila = $resultadoPublicacion->fetch_array(MYSQLI_ASSOC)) { 
                            extract($fila);
                            $consulta = "SELECT nombre
                            FROM user
                            WHERE id = ?";
                        
                            $sentencia = $conexion->stmt_init();
                            if(!$sentencia->prepare($consulta)){
                                $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
                            } else {
                                $sentencia->bind_param("s", $id_usuario);
                                $sentencia->execute();
                                $resultado = $sentencia->get_result();
                                $sentencia->close();
                                if($filaNombre = $resultado->fetch_row()){ ?>
                                    <tr id="publicacion_fila_<?php echo $id; ?>">  
                                        <td><?php echo $id ?></td>                                
                                        <td><?php echo $filaNombre[0] ?></td>
                                        <td><?php echo $titulo ?></td>
                                        <td><?php echo $fecha_solicitud ?></td>
                                        <td><a href="detallePublicacion.php?id=<?php echo $id; ?> " target = "_blank"> Ver detalle
                                            <img src="../static/imagenes/redes/box-arrow-up-right.svg" alt="abrir en otra ventana"></a></td>
                                        <td>
                                            <div class="col-12 ">
                                                <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                                onclick = "confirmarPublicacion(<?php echo $id . ',' . $id_usuario . ',1'; ?>)" 
                                                name = "aprobar"> Aprobar </button>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="col-12 ">
                                                <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                                onclick = "confirmarPublicacion(<?php echo $id . ',' . $id_usuario . ',2'; ?>)">Rechazar</button>
                                            </div>
                                        </td>
                                    
                                    </tr>
                                <?php   
                                }
                            }
                        } ?>
                    </table>
                </div>
                <span id = "resultadoVerificarPublicacion"></span>
            </div>
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