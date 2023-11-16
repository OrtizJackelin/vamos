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
    
    $visible = "none";
    $visibleRecomendados = "none";
    $mensaje = "";

    $consulta = "SELECT p.id, p.fecha_inicio_publicacion, p.fecha_fin_publicacion, p.costo, p.ubicacion, p.titulo,
                    IF (u.es_verificado=1,'destacada','') estilo, 
                    (SELECT ruta from imagen i WHERE i.id_publicacion = p.id ORDER BY id LIMIT 1 ) ruta
                FROM publicacion p, user u
                WHERE p.id_usuario = u.id
                AND estado =1 
                and (fecha_fin_publicacion >= NOW() || fecha_inicio_publicacion IS NULL AND fecha_fin_publicacion  IS NULL)
                ORDER BY U.es_verificado DESC";

    $sentencia = $conexion->stmt_init();
    if (!$sentencia->prepare($consulta)) {
        echo "Fallo la preparación de la consulta <br>";
    } else {
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $sentencia->close();
        
    }
    if(isset($_SESSION['id'])){
        
        ////////////////////////////////////Buscar Recomendados////////////////////////////////////////////////
        $consulta = "SELECT p.id, p.fecha_inicio_publicacion, p.fecha_fin_publicacion, p.costo, p.ubicacion, p.titulo,
                    IF (u.es_verificado=1,'destacada','normal') estilo, 
                    (SELECT ruta from imagen i WHERE i.id_publicacion = p.id ORDER BY id LIMIT 1 ) ruta
                    FROM publicacion p, user u
                    WHERE p.id_usuario = u.id
                    AND estado =1 
                    and (fecha_fin_publicacion >= NOW() || fecha_inicio_publicacion IS NULL AND fecha_fin_publicacion  IS NULL)
                    AND p.id in (SELECT ep.id_publicacion 
                                from etiqueta e, etiqueta_publicacion ep 
                                WHERE e.id=ep.id_etiqueta 
                                AND e.nombre 
                                IN (SELECT it.nombre from interes it, interes_user iu WHERE it.id=iu.id_interes AND iu.id_usuario=?))
                    ORDER BY U.es_verificado DESC";

        $sentencia = $conexion->stmt_init();
        if (!$sentencia->prepare($consulta)) {
        echo "Fallo la preparación de la consulta <br>";
        } else {
        $sentencia->bind_param("s",$_SESSION['id']);
        $sentencia->execute();
        $resultadoRecomendados = $sentencia->get_result();
        $sentencia->close();
        }

    } else {
        $visibleRecomendados = "block";
        $mensaje = "Debe registrarse o ingresar a su cuenta para ver recomendados.<br>";

    }
    
    if(isset($_GET['msj']) && $_GET['msj'] != "" ){
        $visible = "block";
    }

?>

<html>
    <head>
        <title>Vamos</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../static/css/style.css" type="text/css">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
        <link rel="stylesheet" href="../static/css/jquery-ui.min.css">
        <link rel="stylesheet" href="../static/css/jquery-ui.theme.css">
        <link href="../static/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <script type = "text/javascript" src = "../static/js/code.jquery.com_jquery-3.7.1.min.js"></script>
        <script type = "text/javascript" src = "../static/js/jquery-ui.js"></script>
        <script type = "text/javascript" src = "../static/js/jquery-ui.min.js"></script>
        <script>
            $( function() {
                $( "#tabs" ).tabs();
            } );
            function filtrar(){
                var inputBuscar = document.getElementById("buscar").value;
                if(inputBuscar !=""){
                    var parametros = {
                        "buscar" : inputBuscar
                    };
                    $.ajax({
                        data : parametros,
                        url : 'busquedaPublicaciones.php',
                        type : 'POST',
                        beforeSend : function(){
                            $("#card").html("procesando");
                        },
                        success : function(response){
                
                            $("#card").html(response);
                    
                        }
                    });
                }
            };
        </script>
    </head>
    <body class="background2" >
        <header>
            <?php include("barraDeNavegacion.php"); ?>

         </header>


         <div class = "container;" style = "display : <?php echo $visible?>">
            <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                <div>
                    <H6><b><?php if(isset($_GET['msj'])) echo $_GET['msj'] ?></H6></b>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div> 
        </div>
                

        <div  id="tabs" style="border:none; margin-top:20px; background:transparent;" >
            <ul class="publicacionesRecomendados" >
                <li class="liTab" ><a style=" cursor: pointer!important;" href="#tabs-1">Publicaciones</a></li>
                <li class="liTab" ><a style=" cursor: pointer!important;" href="#tabs-2">Recomendados</a></li>
            </ul>

            <div  id="tabs-1">

                <div class = "container">
                    <div class="d-flex flex-row-reverse justify-content-center  bd-highlight mb-5" role="search">
                        <div class="d-flex flex-row bd-highlight ">
                            <div class="p-1 bd-highlight">
                                <input class="form-control me-auto" id = "buscar" name = "buscar" type="search" 
                                placeholder="Buscar" aria-label="Buscar" size="40px">
                            </div>
                            <div class="p-1 bd-highlight">
                                <button class="btn btn-outline-success" onclick = "filtrar()" >Buscar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div  style = "display:flex; justify-content:center; aling-item:center; width:100%;padding:10px; " >
                
                    <div style="display:flex; justify-content:center; flex-wrap:wrap;" id = "card" >

                    <?php while ($publicacion = $resultado->fetch_array(MYSQLI_ASSOC)) {?>

                        <!--<article>-->

                        <div class="card" style="width: 20rem;  border-radius: 15px;
                                background: linear-gradient(145deg, #e6dde5, #ffffff);
                                box-shadow:  12px 12px 24px #ede4ec,
                                -12px -12px 24px #ffffff; overflow:hidden; margin: 10px;">
                       
                            <?php
                            if($publicacion['ruta']=="") {
                                echo "<img src=\"../static/imagenes/nofoto.jpg\" style='margin:0px!important;' 
                                class=\"card-img-top\" alt=\"...\" >";
                            }
                            else {
                                echo "<img src=\"../static/imagenes/publicaciones/" . $publicacion['id']. "/" 
                                . $publicacion['ruta'] . "\" style='margin:0px!important; width:100%!important; 
                                height:200px; ' alt=\"...\" >";
                            }
                            if($publicacion['estilo'] === "destacada"){ ?>
                                <div style=" margin-top:-10px; margin-left:3px; border-radius:5px; width:min-content;  
                                    background: radial-gradient(at left top, rgba(3, 109, 190, 0.774), rgba(4, 1, 81, 0.758)) 
                                    !important; position:abasolute; top: 0px!important; " >
                                    <span style="color:white;padding: 2px 7px; font-size:12px; font-weight:bold;" >
                                        destacado
                                    </span>
                                </div>
                                <?php
                            }?>
                            <div class="card-body" style="display:flex; flex-direction:column; height:100%; flex:1; " >                                
                                <div style="display:flex;flex-direction:column; flex:1; padding-bottom:10px; " >                               
                                <span style="color:#5F5F5F; font-size:21px;"> 
                                    $ <?php echo $numeroFormateado = number_format($publicacion['costo'] , 2, '.', ',') ?> 
                                </span> 
                                <span style="color:#292929; font-weight:bold; margin-top:0px; font-size:21px; display:flex; justify-content:center;" >
                                    <?php echo $publicacion['titulo'] ?>
                                </span>
                                <p style="color:#5F5F5F; font-size:14px;  display:flex; justify-content:center;"> 
                                    <?php echo $publicacion['ubicacion'] ?> 
                                </p>
                                <span style="color:#5F5F5F; font-size:17px;"> 
                                    Disponibilidad 
                                    <?php if($publicacion['fecha_inicio_publicacion'] == "" || $publicacion['fecha_fin_publicacion'] == "" ){
                                        echo "Disponible todo el año";
                                    } else {?>
                                        <div style="display:flex;padding-left:10px; justify-content:space-evenly; " >
                                            <div style="display:flex; flex-direction:column; padding:3px; " >
                                                <span style="color:#5F5F5F; font-size:9px;">desde</span>
                                                <span style="color:#5F5F5F; font-size:14px;">
                                                    <?php echo $publicacion['fecha_inicio_publicacion']?>
                                                </span>
                                            </div>
                                            <div style="display:flex; flex-direction:column;  padding:3px; " >
                                                <span style="color:#5F5F5F; font-size:9px;">hasta</span>
                                                <span style="color:#5F5F5F; font-size:14px;">
                                                    <?php echo $publicacion['fecha_fin_publicacion']?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php 
                                    } ?> 
                                </span>
                                </div>                              
                                <a style=" border:none;  
                                    background: radial-gradient(at left top, rgb(84, 190, 3), rgb(1, 81, 27)) !important;" 
                                    href="detallePublicacion.php?id=<?php echo $publicacion['id'] ?> "
                                    class = "btn" id = "btnCard" >Ir a publicaci&oacute;n
                                </a>                     
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>

            <div  id="tabs-2">

                <div class = "container " style = "display : <?php echo $visibleRecomendados?>">
                    <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                        style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                        <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                        <div>
                            <H6><b><?php echo $mensaje ?></H6></b>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div> 
                </div>
                <?php if(isset($_SESSION['id'])) { ?>
                <div  style = "display:flex; justify-content:center; aling-item:center; width:100%;padding:10px; " >
                
                    <div style="display:flex; justify-content:center; flex-wrap:wrap;" id = "card" >

                    <?php while ($publicacionR = $resultadoRecomendados->fetch_array(MYSQLI_ASSOC)) {?>

                        <!--<article>-->
                        <div class="card <?php echo $publicacionR['estilo']?>" style="width: 20rem;  border-radius: 15px;
                                background: linear-gradient(145deg, #e6dde5, #ffffff);
                                box-shadow:  12px 12px 24px #ede4ec,
                                -12px -12px 24px #ffffff; overflow:hidden; margin: 10px;">
                       
                            <?php
                            if($publicacionR['ruta']=="") {
                                echo "<img src=\"../static/imagenes/nofoto.jpg\" style='margin:0px!important;' 
                                class=\"card-img-top\" alt=\"...\" >";
                            }
                            else {
                                echo "<img src=\"../static/imagenes/publicaciones/" . $publicacionR['id']. "/" 
                                . $publicacionR['ruta'] . "\" style='margin:0px!important; width:100%!important; 
                                height:200px; ' alt=\"...\" >";
                            }
                            if($publicacionR['estilo'] === "destacada"){ ?>
                                <div style=" margin-top:-10px; margin-left:3px; border-radius:5px; width:min-content;  
                                    background: radial-gradient(at left top, rgba(3, 109, 190, 0.774), rgba(4, 1, 81, 0.758)) 
                                    !important; position:abasolute; top: 0px!important; " >
                                    <span style="color:white;padding: 2px 7px; font-size:12px; font-weight:bold;" >
                                        destacado
                                    </span>
                                </div>
                                <?php
                            }?>
                            <div class="card-body" style="display:flex; flex-direction:column; height:100%; flex:1; " >                                
                                <div style="display:flex;flex-direction:column; flex:1; padding-bottom:10px; " >                               
                                <span style="color:#5F5F5F; font-size:21px;"> 
                                    $ <?php echo $numeroFormateado = number_format($publicacionR['costo'] , 2, '.', ',') ?> 
                                </span> 
                                <span style="color:#292929; font-weight:bold; margin-top:0px; font-size:21px; display:flex; justify-content:center;" >
                                    <?php echo $publicacionR['titulo'] ?>
                                </span>
                                <p style="color:#5F5F5F; font-size:14px;  display:flex; justify-content:center;"> 
                                    <?php echo $publicacionR['ubicacion'] ?> 
                                </p>
                                <span style="color:#5F5F5F; font-size:17px;"> 
                                    Disponibilidad 
                                    <?php if($publicacionR['fecha_inicio_publicacion'] == "" || $publicacionR['fecha_fin_publicacion'] == "" ){
                                        echo "Disponible todo el año";
                                    } else {?>
                                        <div style="display:flex;padding-left:10px; justify-content:space-evenly; " >
                                            <div style="display:flex; flex-direction:column; padding:3px; " >
                                                <span style="color:#5F5F5F; font-size:9px;">desde</span>
                                                <span style="color:#5F5F5F; font-size:14px;">
                                                    <?php if(isset($publicacionR['fecha_inicio_publicacion']) 
                                                            && $publicacionR['fecha_inicio_publicacion'] != null
                                                            && $publicacionR['fecha_inicio_publicacion'] != ""){
                                                        echo $publicacionR['fecha_inicio_publicacion'];
                                                        } else {
                                                            echo "";
                                                        }?>
                                                </span>
                                            </div>
                                            <div style="display:flex; flex-direction:column;  padding:3px; " >
                                                <span style="color:#5F5F5F; font-size:9px;">hasta</span>
                                                <span style="color:#5F5F5F; font-size:14px;">
                                                <?php if(isset($publicacionR['fecha_fin_publicacion']) 
                                                            && $publicacionR['fecha_fin_publicacion'] != null
                                                            && $publicacionR['fecha_fin_publicacion'] != ""){
                                                        echo $publicacionR['fecha_fin_publicacion'];
                                                        } else {
                                                            echo "";
                                                        }?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php 
                                    } ?> 
                                </span>
                                </div>                              
                                <a style=" border:none;  
                                    background: radial-gradient(at left top, rgb(84, 190, 3), rgb(1, 81, 27)) !important;" 
                                    href="detallePublicacion.php?id=<?php echo $publicacionR['id'] ?> "
                                    class = "btn" id = "btnCard" >Ir a publicaci&oacute;n
                                </a>                     
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <?php  include "bd/cerrar_conexion.php"; ?>
    
        <!--Footer-->
        <footer  >
            <?php include("../static/html/footer.html"); ?>
        </footer>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous">
        </script>
    </body>
</html>