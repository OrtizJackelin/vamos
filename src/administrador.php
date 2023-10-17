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
                FROM solicitud_verificacion
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
    
   // include "bd/cerrar_conexion.php";      
?>
<html>
<head>
    <title>Detalle de la publicaci&oacute;n</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <link rel="stylesheet" href="../static/css/style2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script type = "text/javascript" src = "../static/js/code.jquery.com_jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script> 

        function aceptar(id, id_usuario, estado, documento){  
            console.log(id);

            var parametros = {
                "id" : id,
                "idUsuario" : id_usuario,
                "estado" : estado,
                "documento" : documento
            };
            $.ajax({
                data : parametros,
                url : 'verificar.php',
                type : 'post',
                beforeSend : function(){
                    $("#resultado").html("procesando");
                },
                success : function(response){
                    $("#resultado").html(response);
                }
            });
        }

        function rechazar(id){


        }

    </script>
   
</head>

<body>
    <header>
        <?php include("barraDeNavegacion.php"); ?><br><br>
    </header>

    <section class = "sectionPrincipal">
        <div class="container w-100" >    
            
            <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                <h4> Solicitudes de verificaci&oacute;n de cuentas de usuarios</h4>
            </div>
     
            <table class="table table-striped table-hover" id = "solicitudVerificacion" name = "solicitudVerificacion">
                
                <tr>
                    <td>Solicitud N°</td>
                    <td>Nombre</td>
                    <td>Documento</td>
                    <td>Comentario De Usuario</td>
                    <td>Fecha De Solicitud</td>
                    <td>Aceptar</td>
                    <td>Rechazar</td>
                 
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
                        if($filaNombre = $resultado->fetch_row()){ ?>
                            <tr>  
                                <td><?php echo $id ?></td>                                
                                <td><?php echo $filaNombre[0] ?></td>
                                <td><img src=../static/imagenes/documentoUsuarios/<?php echo $documento ?>
                                        class="card-img-top" alt="documento">
                                </td>
                                <td><?php echo $comentario ?></td>
                                <td><?php echo $fecha_solicitud ?></td>
                                <td>
                                    <div class="col-12 ">
                                        <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                         onclick = "aceptar(<?php echo $id . ',' . $id_usuario . ',1 ,\'' . $documento . '\''; ?>)" 
                                         name = "aprobar"> Aprobar </button>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-12 ">
                                        <button type="button" class="btn btn-secondary" id="btn_submit_form_evento"
                                        onclick = "aceptar(<?php echo $id . ',' . $id_usuario . ',2 ,\'' . $documento . '\''; ?>)">Rechazar</button>
                                    </div>
                                </td>
                               
                            </tr>
                         <?php   
                        }
                    }
                }  include "bd/cerrar_conexion.php";  ?>
            </table>
            <!-- <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                    <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                    <div>
                        <p id = "resultado"><H6><b></H6></b></p>
                    </div>
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-2" rol="alert" aria-label="Close"></button>
            </div> -->
               
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