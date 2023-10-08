<!DOCTYPE html>
<html lang="es">

<head>
    <title>Vamos</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <link rel="stylesheet" href="../static/css/style2.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!--<script type="text/javascript" src="formularioEvento.js"></script>-->
</head>

<body>
    <header>
        <?php include("barraDeNavegacion.php"); ?>
    </header>

    <!--FORMULARIO-->
    <section>

        <?php  
            if(!isset($_SESSION['id'])){
                header("Location: index.php");
                exit;
                var_dump($_SESSION);
            }
            
            $valido = true;
            $dato = array();
            $mensaje = "";
                    
            try{
                include "bd/conexion.php";
          
            } catch (mysqli_sql_exception $e) {
                $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
                // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
                header("Location: error.php?mensaje=" . urlencode($mensajeError));
                    
            }
            
            $consulta = "SELECT email, clave, nombre, apellido, dni, fecha_nacimiento, telefono, sexo, bio, foto
                        FROM user 
                        WHERE id = ? ";         
            $sentencia = $conexion->stmt_init();

            if(!$sentencia->prepare($consulta)) {
                $mensaje = $mensaje. " Fallo la preparacion de la consulta <br>";
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
                          
                $consulta = "SELECT nombre
                            FROM etiqueta_user, etiqueta
                            WHERE etiqueta_user.id_etiqueta = etiqueta.id 
                            and etiqueta_user.id_usuario = ?";     

                $sentencia_etiqueta = $conexion->stmt_init();

                if(!$sentencia_etiqueta->prepare($consulta)){

                    $mensaje = $mensaje. " Fallo la preparacion de la consulta para buscar nombre de los intereses <br>";
                    $valido = false;
                    
                }
                else{
                    
                    $sentencia_etiqueta->bind_param("s", $_SESSION['id']);
                    $sentencia_etiqueta->execute();
                    $resultado_etiqueta = $sentencia_etiqueta->get_result();
                    $sentencia_etiqueta->close();
                }

                $consultaFoto = "SELECT foto
                                FROM user
                                WHERE id = ?";
                $sentenciaFoto = $conexion->stmt_init();

                if (!$sentenciaFoto->prepare($consultaFoto)) {

                    $mensaje = $mensaje. " Fallo la preparacion de la consulta para buscar foto <br>";
                    $valido = false;

                } else {
                    $sentenciaFoto->bind_param("s", $publicacion['id']);
                    $sentenciaFoto->execute();
                    $resultadoFoto = $sentenciaFoto->get_result();
                    $sentenciaFoto->close();
                }

            }    
            include "bd/cerrar_conexion.php";                  
                 
        ?>

        <div class="container w-75 ">

            <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                <h2> Editar datos </h2>
                <H4 id="h_nombre_evento"></H4>
                <p> </p>
            </div><br><br>

            <form class="row g-3 g-sm-1 " id="formulario" method="post" action="miPerfil.php" enctype = "multipart/form-data" >

                <div class = "row g-4" style="margin-bottom: 40px">
                    <div class = "col-md-4">
                        <div class = "container">
                                <div class="card" style="width: 15rem;">
                                    
                                    <?php
                                     $first = true; // Variable para controlar la clase "active" en el primer elemento
                                     if ($resultadoFoto->num_rows == 0) {
                                        echo '<img src="../static/imagenes/usuarios/person-bounding-box.svg" 
                                        class="card-img-top" alt="Imagen no disponible">';
                                     } else{
                                        echo"../static/imagenes/usuarios/".$_SESSION['id'];
                                     }
                                    ?>                              
                                <div class="card-body">
                                    <input class="form-control form-control-sm" id="formFileSm" type="file">
                                </div>
                            </div>                  
                        </div>
                    </div>

                    <div class = "col-md-8" >
                        <div class = "row" style="margin-bottom: 60px">
                            <div class="col-md-12" >
                                <img src="../static/imagenes/redes/cc-square.svg"alt="cuenta verificada" title ="cuenta verificada" style="float: right;">
                            </div>
                        </div>

                       <div class = "row" style="margin-bottom: 30px">
                            <div class="col-md-4">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" pattern="[A-Za-z]{2,15}" 
                                value="<?php if(isset($nombre)) echo $nombre;?>" required>
                            </div>

                            <div class="col-md-4">
                                <label for="apellido" class="form-label">Apellido</label>
                                <input type="text" class="form-control" id="apellido" name= "apellido" 
                                value="<?php if(isset($apellido)) echo $apellido;?>" pattern="[A-Za-z]{2,15}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="dni" class="form-label">DNI</label>
                                <input type="number" class="form-control" id="dni" name= "dni" min="1000000" max="99999999" 
                                value="<?php if(isset($dni)) echo $dni;?>" required>
                            </div>

                        </div>
                        <div class = "row">

                            <div class="col-md-4">
                                <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name= "fechaNacimiento" 
                                value="<?php if(isset($fecha_nacimiento)) echo $fecha_nacimiento;?>" min="16" max="150" required>
                            </div>

                            <div class="col-md-4">
                                <label for="telefono" class="form-label">Tel&eacute;fono</label>
                                <input type="number" class="form-control" id="telefono" name = "telefono" 
                                min="1000000000" maxlength="9999999999" 
                                value="<?php if(isset($telefono)) echo $telefono;?>" required>
                            </div>


                            <div class="col-md-4">
                                <label for="sexo" class="form-label">Sexo</label>
                                <select id="sexo" name = "sexo" class="form-select" required>
                                    <option  value="" selected> Seleccione </option>
                                    <option value="f" <?php if(isset($sexo) && $sexo ==='f') echo 'selected'?>>Femenino</option>
                                    <option value="m" <?php if(isset($sexo) && $sexo ==='m') echo 'selected'?>>Masculino</option>
                                </select>
                            </div>
                        </div>
                    </div>
                <div>
        
                <div class = "row" style="margin-bottom: 30px">
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name= "bio" rows="3" 
                        value="<?php if(isset($telefono)) echo $telefono;?>"></textarea>
                    </div>
                </div>

          
                <p>Intereses</p>

                <?php

                while($fila = $resultado_etiqueta->fetch_array(MYSQLI_ASSOC)){

                    echo "<div class=\"row g-3\" style=\"margin-bottom: 30px\">
                        <div class=\"col-md-2\">
                            <div class=\"form-check\">
                                <input class=\"form-check-input\" type=\"checkbox\" name = \"interes[]\" id=\"flexCheckChecked\" 
                                value = " . $fila['nombre'] . " checked>
                                <label class=\"form-check-label\" for=\"flexCheckChecked\">"
                                    .$fila['nombre'].
                                "</label>
                            </div> 
                        </div>                       
                    </div>";
                }

                ?>            
                
                <div class = "row" style="margin-bottom: 40px">
                
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name = "email" 
                        value="<?php if(isset($email)) echo $email;?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="clave" class="form-label">Password</label>
                        <input type="password" id="clave" name="clave"  class="form-control" 
                            pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$" 
                            value="<?php if(isset($clave)) echo $clave;?>" required>
                    </div>

                    <div class="col-md-4">
                        <label for="repetirClave" class="form-label">Repetir Password</label>
                        <input type="password" id="repetirClave" name="repetirClave"  class="form-control" 
                        required>
                    </div>

                </div>
                
              <!-- <div class = "row" style="margin-bottom: 30px">-->
     
                    <!--<div class="col-4 ">
                        <button type="submit" class="btn btn-secondary" id="btn_submit_form_evento" name = "guardar">Guardar</button>
                    </div>-->

                    
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" name = "guardar">
                                Guardar
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">Guardar Cambios</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p>Al guardar los cambios en superfil perdera autom&aacute;ticamente<br>
                                     la certificaci&oacute;n de la cuenta si la posee, y tendr&aacute; que<br>
                                     hacer una nueva solicitud de certificaci&oacute;n</p><br>
                                     <p>Seleccione enviar si desea continuar guarndando los cambios</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary" name = "enviar" >Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                </div>

            </form><br>
            <?php
                if(!$valido){
                    ?>
                <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" 
                style = "margin-top: 20px; margin-bottom: 5px;" type = "hidedeng">
                    <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                    <div>
                        <H6><b><?php echo $mensaje ?></H6></b>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div> 
                <?php
                }
            ?>
        </div>
       
    </section>


    <!--FOOTER-->
    <footer>
        <?php include("../static/html/footer.html"); ?>
    </footer>

    <!-- SCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>