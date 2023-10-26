<?php  
    require_once('sessionStart.php'); 
        
    if(!isset($_SESSION['email'])){
       header("Location: validarCorreo.php");
       exit;
    }

    $valido = true;
    $mensaje = "";
    $hash = "";
    extract($_POST);

    try{
        include "bd/conexion.php";

    } catch(mysqli_sql_exception $e){
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error 
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
    }


    ///////////////////Consultar para traer los códigos de países/////////
    $consulta = "SELECT *
                FROM codigo_pais";
    $sentencia_codigo = $conexion->stmt_init();

    if(!$sentencia_codigo->prepare($consulta)) {
        $mensaje = $mensaje. " Fallo la preparacion de la consulta <br>";
        $valido = false;
    }
    else {
        $sentencia_codigo->execute();
        $resultado_codigo = $sentencia_codigo->get_result();   
        $sentencia_codigo->close();               
    }
    //////////////////////////////////////////////////////////////////////////////////////

    /////////////////////////Validar inputs en el servidor////////////////////////////////

    if (isset($_POST['enviar'])){

        include  "../src/inputUsuario.php";
        
    }
    ////////////////////////////////////////////////////////////////////////////////////////

    if (isset($_POST['enviar']) && $valido){
        
        $consulta = "INSERT INTO  
        user (nombre, apellido, dni, sexo, fecha_nacimiento, telefono, email, clave, cod_pais)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?) ";

        $sentencia = $conexion->stmt_init();
        if(!$sentencia->prepare($consulta)){
            echo "fallo la preparacion de la consulta <br>";
        }
        else{
            $sentencia->bind_param("sssssssss", $_POST['nombre'], $_POST['apellido'],
                                    $_POST['dni'], $_POST['sexo'], $_POST['fechaNacimiento'], 
                                    $_POST['telefono'], $_SESSION['email'], $hash, $_POST['codPais'],);
            
            try{
                $sentencia->execute();//revisar hacer try catch arrojo error por dni que es clave repetido   
            } catch (Exception $e) {
                
                $mensaje = "Error: " . $e->getMessage();
                $valido = false;
        
            }
            
            if($sentencia->affected_rows > 0){
                unset($_SESSION['email']);
                $_SESSION['id'] = $sentencia->insert_id;
                $_SESSION['nombre'] = $_POST['nombre'];
                $_SESSION['esVerificado'] = 0;
                $sentencia->close();
                header("Location: index.php");
                exit;                           
            }
            else{
                echo"error guardando<br>"; // ver aqi 
            }
        }  
                                    
        include "bd/cerrar_conexion.php";
    } else{
        
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <title>Vamos</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../static/css/style.css" type="text/css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../static/css/bootstrap-icons.css">
    <link href="../static/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script type="text/javascript" src="../static/js/validaciones.js"></script>

</head>

<body>
    <header>
        <?php include("barraDeNavegacion.php"); ?>
    </header>

    <!--FORMULARIO-->
    <section class = "sectionPrincipal">
          
        <div class="container w-75 ">

            <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                <h2> Formulario Registro De Usuario </h2>

            </div><br><br>

            <form class="row g-3 " id="formulario" method="post" action="resgistroDeUsuario.php" >

                <div class="col-md-4">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name = "nombre" 
                    value = "<?php if(isset($nombre)) echo $nombre?>" pattern="[A-Za-z]{2,15}" required>
                </div>

                <div class="col-md-4">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name = "apellido" 
                    value = "<?php if(isset($apellido)) echo $apellido?>" pattern="[A-Za-z]{2,15}" required>
                </div>

                <div class="col-md-4">
                    <label for="dni" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="dni" name = "dni" min="1000000" max="99999999" 
                    value = "<?php if(isset($dni)) echo $dni?>"required>
                </div>

                <div class="col-md-3">
                    <label for="fechaNacimiento" class="form-label">Fecha de nacimiento</label>
                    <input type="date" class="form-control" id="fechaNacimiento" name = "fechaNacimiento" min="16" max="150" 
                    value="<?php if(isset($fecha_nacimiento)) echo $fecha_nacimiento;?>" required>
                </div>

                <div class="col-md-3">                    
                    <label for="codPais" class="form-label">Cod-Pais</label>
                    <select id="codPais" class="form-select" name = "codPais" required>
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

                <div class="col-md-3">
                    <label for="telefono" class="form-label">Tel&eacute;fono</label>
                    <input type="text" class="form-control" id="telefono" name = "telefono" min="1000000000" maxlength="9999999999"
                     value = "<?php if(isset($telefono)) echo $telefono?>" required>
                </div>


                <div class="col-md-3">
                    <label for="sexo" class="form-label">Sexo</label>
                    <select id="sexo" class="form-select" name = "sexo" value = "<?php echo $sexo ?>">
                        <option value="">Seleccione</option>
                        <option value="f"<?php if(isset($sexo) && $sexo ==='f') echo 'selected'?> checked>Femenino</option>
                        <option value="m" <?php if(isset($sexo) && $sexo ==='m') echo 'selected'?> checked>Masculino</option>
                    </select>
                </div>
               
                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name = "email" 
                    value = "<?php if (isset($_SESSION['email']))echo $_SESSION['email']; ?>" readonly>
                </div>

                <div class="col-md-4">
                    <label for="clave" class="form-label">Password</label>
                    <input type="password" id="clave" name="clave"  class="form-control" 
                        pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$" required>
                </div>

                <div class="col-md-4">
                    <label for="repetirClave" class="form-label">Repetir Password</label>
                    <input type="password" id="repetirClave" name="repetirClave"  class="form-control" 
                    required>
                </div>
  
     
                <div class="col-12 ">
                    <button type="submit" class="btn btn-secondary" id="btn_submit_form_evento" name = "enviar">ENVIAR</button>
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
<?php } ?>