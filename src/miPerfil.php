<?php 
    require_once ('sessionStart.php'); 

    if(!isset($_SESSION['id'])){
        header("Location: index.php");
        exit;
        var_dump($_SESSION);
    }
    
    $valido = true;
    $dato = array();
    $mensaje = "";
    $directorioDestino = "../static/imagenes/usuarios/";
    $nombreFoto = "";
    $hash = "";
    $actualizarClave = false;
    
            
    try{
        include "bd/conexion.php";
    
    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }

    

    //////////////////////////////////Actualizar información de formulario/////////////////////////////////////////////////
    if(isset($_POST['enviar'])){

        include "../src/inputUsuario.php";
        $esVerificado = 0;

        $fotoEmpty = false;
        extract($_POST);
        
    
        if (file_exists($directorioDestino) && ($_FILES['foto']['size']>0) ) {
                    
            $nombreFoto = $_FILES["foto"]["name"];
            $tipoArchivo = $_FILES["foto"]["type"];
            $tamanoArchivo = $_FILES["foto"]["size"];
            $archivoTmpName = $_FILES["foto"]["tmp_name"];
            $errorArchivo = $_FILES["foto"]["error"];

            $imageFileType = strtolower(pathinfo($nombreFoto, PATHINFO_EXTENSION));

            if ($errorArchivo === UPLOAD_ERR_OK) {
                $check = getimagesize($archivoTmpName);
                if ($check !== false) {
                    $maxFileSize = 3 * 1024 * 1024; // 5 MB
                    if ($tamanoArchivo <= $maxFileSize) {
                        
                    
                        // Mover el archivo temporal al destino deseado   
                        $nombreFoto = uniqid() . "." . $imageFileType;             
                        if (move_uploaded_file($archivoTmpName, $directorioDestino . $nombreFoto)) {
                            $mensaje = $mensaje . ": " . $nombreFoto. "<br>";

                            $consulta = "UPDATE  user 
                            SET  foto = ?
                            WHERE id = ?";

                            $sentencia = $conexion->stmt_init();
            
                            if(!$sentencia->prepare($consulta)) {
                                $mensaje = $mensaje . "fallo la preparacion de la consulta <br>";
                                $valido = false;
                            } else{
                                $sentencia->bind_param("ss", $nombreFoto, $_SESSION['id']);        
                                $sentencia->execute();
                                if($sentencia->affected_rows <= 0) {
                                    $mensaje = $mensaje . "error guardando foto<br>"; 
                                    $valido = false;
                                }
                                $sentencia->close(); 
                            }

                        } else {
                            $mensaje = $mensaje ."Hubo un error al mover el archivo. <br>";
                            $valido = false;
                        }
                    }else {
                        $mensaje = $mensaje ."El archivo es demasiado grande. El tamaño máximo permitido es 3 MB.. <br>";
                            $valido = false;
                    }
                } else {
                    $mensaje = $mensaje . "El archivo no es una imagen válida.";
                    $valido =false;
                }
            } else {
                $mensaje = $mensaje . "Error al subir el archivo. Código de error: " . $errorArchivo . "<br>";
                $valido = false;
            }
        }  else {

        }            
        
        if($valido){
            $consulta = "UPDATE  user 
                        SET nombre = ?, apellido = ?, dni = ?, sexo = ?, fecha_nacimiento = ?, 
                            telefono = ?, cod_pais= ?, bio = ?, es_verificado = ?
                        WHERE id = ?";

            $sentencia = $conexion->stmt_init();

            if(!$sentencia->prepare($consulta)) {
                $mensaje = $mensaje . "fallo la preparacion de la consulta general de los dato" . $conexion->error . " <br>";
                $valido = false;
            } else{
                $sentencia->bind_param("ssssssssss", $nombre, $apellido, $dni, $sexo, $fechaNacimiento,
                                        $telefono, $codPais, $bio, $esVerificado,  $_SESSION['id']);        
                
                if (!$sentencia->execute()) {
                    $mensaje = $mensaje . "Error ejecutando la consulta de los datos: " . $sentencia->error . "<br>";
                    $valido = false;
                } else {

                    if($sentencia->affected_rows <= 0) {
                        $mensaje = $mensaje . "no se realizo ninguna actualización el los datos del usuario <br>"; 
                        $valido = false;
                    }
                    
                }
                $sentencia->close(); 
            }
        }

        ///////////////////////////////////Actualizando clave//////////////////

        if ($valido && $actualizarClave) {
        
            $consulta = "UPDATE user
                        SET clave = ?
                        WHERE id = ?";
        
            $sentencia = $conexion->stmt_init();
        
            if (!$sentencia->prepare($consulta)) {
                $mensaje = $mensaje . "Fallo la preparación de la consulta de la clave: " . $conexion->error . "<br>";
                $valido = false;
            } else {                             
                
                $sentencia->bind_param("ss", $hash, $_SESSION['id']);
                
                if (!$sentencia->execute()) {
                    $mensaje = $mensaje . "Error ejecutando la consulta de la clave: " . $sentencia->error . "<br>";
                    $valido = false;
                } else {
                    if ($sentencia->affected_rows <= 0) {
                        $mensaje = $mensaje . "No se realizó ninguna actualización de la clave en la bd<br>"; 
                        $valido = false;
                    } 
                }
                
                $sentencia->close();  
            }
        }
            
        //////////////////////Conculto para eliminar todos los check existentes
        $consulta = "DELETE  FROM etiqueta_user
                    WHERE id_usuario = ?";

        $sentencia = $conexion->stmt_init();
        if(!$sentencia->prepare($consulta)) {
            $mensaje = $mensaje . "fallo la preparacion de la consulta eliminar etiqueta de la db <br>";
            $valido = false;
        } else{
            $sentencia->bind_param("s",$_SESSION['id']);
            $sentencia->execute();         
            $sentencia->close();   
        }  
        
        ///////////////////////Insertamos las nuevas opciones seleccionadas//////////////
        if(isset($interes)){
            foreach($interes as $id_etiqueta){
                echo $_SESSION['id']. "y " .$id_etiqueta;
                                            
                $consulta = "INSERT  INTO etiqueta_user
                            (id_usuario, id_etiqueta)
                            VALUES (?, ?)"; 

                $sentencia = $conexion->stmt_init();

                if(!$sentencia->prepare($consulta)) {
                    $mensaje = $mensaje ."fallo la preparacion de la consulta para insertar 
                    tiquetas seleccionadas por el usuario <br>";

                    $valido = false;
                } else{
                    $sentencia->bind_param("ss", $_SESSION['id'], $id_etiqueta);

                    $sentencia->execute();
                    
                    if($sentencia->affected_rows <= 0) {
                        $mensaje = $mensaje ."error guardando etiquetas en la bd<br>"; 
                        $valido = false;
                    }
                    $sentencia->close();   
                }                
            }  
        }
    }


        /////////////////////////Consultar para traer los códigos de países///////////////////////////////////
    $consulta = "SELECT *
                FROM codigo_pais";
    $sentencia_codigo = $conexion->stmt_init();

    if(!$sentencia_codigo->prepare($consulta)) {
        $mensaje = $mensaje. " Fallo la preparacion de la consulta codigo pais<br>";
        $valido = false;
    }
    else {
        $sentencia_codigo->execute();
        $resultado_codigo = $sentencia_codigo->get_result();   
        $sentencia_codigo->close();               
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
    
    //////////////////Consultar Para traer los valores que se muestran en el fomulario////////////////////
    $consulta = "SELECT email, clave, nombre, apellido, dni, fecha_nacimiento, telefono, sexo, bio, foto, es_verificado
                FROM user 
                WHERE id = ? ";         
    $sentencia = $conexion->stmt_init();

    if(!$sentencia->prepare($consulta)) {
        $mensaje = $mensaje. " Fallo la preparacion de la consulta de los datos usuario <br>";
        $valido = false;
    } else {
        
        $sentencia->bind_param("s", $_SESSION['id']);
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $sentencia->close();
        
        if($dato = $resultado->fetch_array(MYSQLI_ASSOC)) {
            extract($dato);        
            
        } else {
            $mensaje = $mensaje. " Datos no encontrados. <br>";
            $valido = false;
        
        }
    }
    
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /////////////////////////Consultar para tarer todas los intereses del usuario////////////////////////////////////
    $consulta = "SELECT *
                FROM etiqueta";
            
    $sentencia_etiqueta = $conexion->stmt_init();

    if(!$sentencia_etiqueta->prepare($consulta)){

        $mensaje = $mensaje. " Fallo la preparacion de la consulta para buscar nombre de los intereses <br>";
        $valido = false;
        
    } else {            
        //$sentencia_etiqueta->bind_param("s", $_SESSION['id']);
        $sentencia_etiqueta->execute();
        $resultado_etiqueta = $sentencia_etiqueta->get_result();
        $sentencia_etiqueta->close();
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //////////////////////////////////Traer la relacion entra las etiquetas y el usuario//////////////////////////////
    $consulta = "SELECT id_etiqueta
                    FROM etiqueta_user
                    WHERE id_usuario = ?";

    $sentenciaEtiquetaUsuario = $conexion->stmt_init();
    if(!$sentenciaEtiquetaUsuario->prepare($consulta)){
        $mensaje = $mensaje. " Fallo la preparacion de la consulta para buscar nombre de los intereses <br>";
        $valido = false;
        
    } else {            
        $sentenciaEtiquetaUsuario->bind_param("s", $_SESSION['id']);
        $sentenciaEtiquetaUsuario->execute();
        $resultadoEtiquetaUsuario= $sentenciaEtiquetaUsuario->get_result();
        $sentenciaEtiquetaUsuario->close();
    }
        
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    include "bd/cerrar_conexion.php";                  
            
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Vamos</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <link rel="stylesheet" href="../static/css/style2.css" type="text/css">
    <link rel="stylesheet" href="../static/css/cssNav.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
    <link href="../static/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!--<script type="text/javascript" src="formularioEvento.js"></script>-->
</head>

<body>

    <header>
        <?php include("barraDeNavegacion.php"); ?>
    </header>

    <!--FORMULARIO-->
    <section style="width:95%; margin-left:2.5%;">

        <div class="container">

            <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                <h2> Editar datos </h2>
        
            </div><br>
            <div style="display:flex;width:100%; justify-content:flex-end;padding-bottom:15px; padding-right:10px; ">
                <div>
                    <?php
                                    if($es_verificado==1){
                                        echo"<img src=\"../static/imagenes/redes/cc-squareVerificado.svg\"alt=\"cuenta verificada\" 
                                        title =\"cuenta verificada\" style=\"float: right;\">";
                                    } else {
                                        echo"<img src=\"../static/imagenes/redes/cc-square.svg\"alt=\"cuenta no verificada\" 
                                        title =\"cuenta verificada\" style=\"float: right;\">";
                                    }
                                ?>

                </div>
            </div>
            <form style="display:flex; flex-direction:column;  " id="formulario" method="post" action="miPerfil.php"
                enctype="multipart/form-data">

                <div
                    style=" max-width:100%; display:flex; position:relative; flex-wrap:wrap; aling-items:center; justify-content:center;">

                    <div style="margin: 7px;  display:flex;flex-direction:column; flex:3; min-width:250px; max-width: 350px; ">

                        <div class="card" style="width: 100%;">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-2"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                            <?php
                                if (!isset($foto)) {
                                    echo '<img src="../static/imagenes/usuarios/person-bounding-box.svg" 
                                    class="card-img-top" alt="Imagen no disponible">';
                                } else{
                                    echo "<img src=\"../static/imagenes/usuarios/" . $foto . "\" 
                                    class=\"card-img-top\" alt=\"Imagen no disponible\">";
                                    
                                }
                            ?>
                            <div class="card-body">
                                <input type="file" class="form-control form-control-sm" id="formFileSm" name="foto">
                            </div>
                        </div>

                    </div>

                    <div style="display:flex; flex:7; flex-wrap:wrap; position:relative; aling-items:flex-end; min-width:300px;">


                        <div style=" flex:1; justify-content:flex-end;display:flex; flex-direction:column;min-width:170px;">
                            <div style="padding:7px;">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre"
                                    pattern="[A-Za-z]{2,15}" value="<?php if(isset($nombre)) echo $nombre;?>" required>
                            </div>

                            <div style="padding:7px;">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name="apellido"
                                    value="<?php if(isset($apellido)) echo $apellido;?>" pattern="[A-Za-z]{2,15}"
                                    required>
                            </div>



                        </div>

                        <div style="flex:1;justify-content:flex-end;display:flex; flex-direction:column; min-width:250px;">

                            <div style="padding:7px;">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="number" class="form-control" id="dni" name="dni" min="1000000"
                                    max="99999999" value="<?php if(isset($dni)) echo $dni;?>" required>
                            </div>

                            <div style="padding:7px;">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select id="sexo" name="sexo" class="form-select" required>
                                    <option value="" selected> Seleccione </option>
                                    <option value="f" <?php if(isset($sexo) && $sexo ==='f') echo 'selected'?>>Femenino
                                    </option>
                                    <option value="m" <?php if(isset($sexo) && $sexo ==='m') echo 'selected'?>>Masculino
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                
                <div style="display:flex; flex:1; flex-wrap:wrap; width:100%!important;">
                    <div style="padding:7px; flex:1; min-width:250px; ">
                        <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                        <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento"
                            value="<?php if(isset($fecha_nacimiento)) echo $fecha_nacimiento;?>" min="16" max="150"
                            required>
                    </div>

                    <div style="padding:7px; flex:1;  min-width:250px; ">
                        <label for="codPais" class="form-label">Cod-Pais</label>
                        <select id="codPais" class="form-select" name="codPais" required>
                            <option value="">Seleccione</option>
                            <?php
                                
                                    while($fila = $resultado_codigo->fetch_array(MYSQLI_ASSOC)){
                            
                                        echo "<option value=\"" . $fila['codigo'] . "\"";

                                        if (isset($codPais) && $codPais === $fila['codigo']) {
                                            echo " selected";
                                        }
                                        
                                        echo ">" . $fila['pais'] . "</option>";
                                    }
                                    ?>

                        </select>
                    </div>

                    <div style="padding:7px; flex:1; min-width:250px;  ">
                        <label for="telefono" class="form-label">Tel&eacute;fono</label>
                        <input type="number" class="form-control" id="telefono" name="telefono" min="1000000000"
                            maxlength="9999999999" value="<?php if(isset($telefono)) echo $telefono;?>" required>
                    </div>

                </div>

                <div>

                    <div style=" padding:7px; margin-bottom: 30px">
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" id="bio" name="bio" rows="3"
                                value="<?php if(isset($telefono)) echo $telefono;?>"></textarea>
                        </div>
                    </div>


                    <div style=" padding:10px;">
                        <p>Intereses</p>
                        <div class="row" style="margin-bottom: 30px">

                            <?php      
$etiquetaSele = $resultadoEtiquetaUsuario->fetch_all(MYSQLI_ASSOC);

 $etiquetas = array_column($etiquetaSele, "id_etiqueta");
     $etiquetas = array_combine($etiquetas, $etiquetas);
  while($fila = $resultado_etiqueta->fetch_array(MYSQLI_ASSOC)){                         
  ?>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="interes[]"
                                        id="flexCheckChecked" value=<?php echo $fila['id']; ?>
                                        <?php if(isset($etiquetas[$fila['id']])) echo "checked"; ?>>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        <?php echo $fila['nombre'] ?>
                                    </label>
                                </div>
                            </div>

                            <?php } ?>
                        </div>
                    </div>


                    <div>
                        <!-- intereses y contraseña-->
                        <div class="row" style="margin-bottom: 40px">

                            <div class="col-md-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php if(isset($email)) echo $email;?>" readonly>
                            </div>

                            <div class="col-md-4">
                                <label for="clave" class="form-label">Password</label>
                                <input type="password" id="clave" name="clave" class="form-control"
                                    pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$">
                            </div>

                            <div class="col-md-4">
                                <label for="repetirClave" class="form-label">Repetir Password</label>
                                <input type="password" id="repetirClave" name="repetirClave" class="form-control">
                            </div>

                        </div>

                        <!-- <div class = "row" style="margin-bottom: 30px">-->

                        <!--  <div class="col-4 ">
    <button type="submit" class="btn btn-secondary" id="btn_submit_form_evento" name = "guardar">Guardar</button>
    </div>-->


                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                            data-bs-target="#staticBackdrop" name="guardar">
                            Guardar
                        </button>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Guardar Cambios</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Al guardar los cambios en superfil perdera autom&aacute;ticamente<br>
                                        la certificaci&oacute;n de la cuenta si la posee, y tendr&aacute; que<br>
                                        hacer una nueva solicitud de certificaci&oacute;n</p><br>
                                    <p>Seleccione enviar si desea continuar guarndando los cambios</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" name="enviar">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form><br>

            <?php if(!$valido) { ?>
            <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert"
                style="margin-top: 20px; margin-bottom: 5px;" type="hidedeng">
                <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>
                <div>
                    <H6><b><?php echo $mensaje ?></H6></b>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2" rol="alert"
                    aria-label="Close"></button>
            </div>
            <?php } ?>
        </div>

    </section>


    <!--FOOTER-->
    <footer>
        <?php include("../static/html/footer.html"); ?>
    </footer>

    <!-- SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>