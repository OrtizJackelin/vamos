
<?php
    require_once ('sessionStart.php');
    $valido = true;
    $hash = "";
    $hash_almacenado = "";
    $mensaje = "";


    if (isset($_POST['enviar'])) {                
    
        if (isset($_POST['email'])) {
    
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                //echo "La dirección de correo electrónico es válida.";
            
            } else {
               // echo "La dirección de correo electrónico no es válida.";
                $valido = false;
            }

            if (isset($_POST['clave'])){
    
                $patron = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$/';

                if (preg_match($patron, $_POST['clave'])) {
                    //echo "La contraseña cumple con los requisitos.<br>";
                    //$hash = password_hash($_POST['clave'], PASSWORD_DEFAULT);

                } else {
                   // echo "La contraseña no cumple con los requisitos.<br>";
                    $valido = false;

                }
            } 
        }
    }
    
    if (isset($_POST['enviar']) && $valido) {

        try{
            include "bd/conexion.php";
    
        } catch (mysqli_sql_exception $e) {
            $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
            // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
            header("Location: error.php?mensaje=" . urlencode($mensajeError));
                
        }
    
        $consulta = "SELECT id, clave, nombre, es_verificado, es_administrador
                    FROM user
                    WHERE email=?"; 

        $sentencia = $conexion->stmt_init();
        if(!$sentencia->prepare($consulta)){
            //echo "fallo la preparacion de la consulta <br>";
        }
        else{
        
            $sentencia->bind_param("s", $_POST['email']);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            $sentencia->close();
            //var_dump($resultado);
            if($fila = $resultado->fetch_array(MYSQLI_ASSOC)){
               // var_dump($fila['es_administrador']);                
                // Obtener el hash almacenado en la base de datos para ese usuario
                $hash_almacenado = $fila["clave"];

                // Verificar si la contraseña ingresada es válida
                if (password_verify($_POST['clave'], $hash_almacenado)) {
                    // La contraseña es válida, permitir el acceso

                    $_SESSION['id'] = $fila['id'];
                    $_SESSION['nombre'] = $fila['nombre'];
                   // $_SESSION['esVerificado'] = $fila['es_verificado']; 
                    $_SESSION['esAdministrador'] = $fila['es_administrador']; 

                    
                    // para verificar la fencha de vencimiento de la verificacion de cuenta
                    if($fila['es_verificado'] == 1){
                        $consulta = "SELECT fecha_vencimiento 
                        FROM verificacion_cuenta 
                        WHERE estado = 1 
                        AND id_usuario = ? 
                        AND id = (SELECT MAX(id) 
                                FROM verificacion_cuenta 
                                WHERE id_usuario = ?)";

                        $sentencia = $conexion->stmt_init();
                        if(!$sentencia->prepare($consulta)){
                           // echo "fallo la prepracion de la consuta para traer fecha de vencimiento.<br>";
                        } else {
                            $sentencia->bind_param("ss", $fila['id'], $fila['id']);
                            $sentencia->execute();
                            $resultadoFechaVencimiento = $sentencia->get_result();
                            $sentencia->close();
                            if($fecha = $resultadoFechaVencimiento->fetch_assoc()){
                                if($fecha['fecha_vencimiento']!= NULL){
                                    $fechaActual = date("Y-m-d");
                                    if($fechaActual > $fecha['fecha_vencimiento']){
                                        $consulta = "UPDATE user
                                                    SET es_verificado = 0
                                                    WHERE id = ?";
                                        $sentencia = $conexion->stmt_init();
                                        if(!$sentencia->prepare($consulta)){
                                           // echo "No se preparo la consulta para actualizar el es_verificado del usuario";
                                        } else {
                                            $sentencia->bind_param("s", $fila['id']);
                                            $sentencia->execute();
                                            if($sentencia->affected_rows > 0){
                                                $_SESSION['esVerificado'] = 0;
                                            } else {
                                               // echo "no se realizo cambios es la variables es_verificado";
                                               $_SESSION['esVerificado'] = $fila['es_verificado'];
                                            }
                                            $sentencia->close();
                                        }
                                    } else {
                                        //echo "La fecha actual es menor o igual a la fecha de vencimiento.<br>";
                                    }

                                } else {
                                   // echo"No se establecio una fecha de vencimiento para la verificacion de la cuenta, fecha null.<br>";
                                }
                            } else {
                               // echo "No se encontro resultado de la consulta para traer fecha de vencimiento";
                            }
                        }
                    } else {
                        //echo "Usuario no esta verificado.<br>";
                    }
                    
                    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                    // consultar y revisar el vencimiento de la solicitud de alquiler

                    $consulta = "SELECT alquiler.*
                    FROM alquiler, publicacion 
                    WHERE alquiler.id_usuario = ?
                    and alquiler.id_publicacion = publicacion.id ";                  
                    $sentencia = $conexion->stmt_init();
                    if(!$sentencia->prepare($consulta)){
                        $mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
                       // echo"fallo la preparción". $sentencia->error . "<br>";
                    } else {
                        $sentencia->bind_param("s",$_SESSION['id']);
                        $sentencia->execute();
                        $resultadoAlquiler = $sentencia->get_result();
                        $sentencia->close();
                        while($filaAlquiler = $resultadoAlquiler->fetch_array(MYSQLI_ASSOC)) { 
                           // extract($fila);
                           // var_dump($fila);
                            if($filaAlquiler['aprobado'] == 0){
                                //convertimos la fecha en una fecha con formato de marca de tiempo
                                $fechaDada = strtotime($filaAlquiler['fecha_solicitud']);
                                $fechaActual = time();
                                $diferenciaSegundos = $fechaActual - $fechaDada;
                                $unDiaEnSegundos = 24*60*60;
                                $diferenciaEnDias = floor ($diferenciaSegundos/$unDiaEnSegundos);

                                if($diferenciaEnDias >= 3){
                                    //echo"entro en el if >3";
                                    $consulta = "UPDATE alquiler
                                                SET aprobado = 2
                                                WHERE id_usuario = ?
                                                and id = ?";
                                    $senteciaAlquiler = $conexion->stmt_init();
                                    if(!$senteciaAlquiler ->prepare($consulta)){
                                        $mensaje = $mensaje. "fallo la preparación". $senteciaAlquiler ->error . "<br>";
                                       // echo"fallo la preparacion". $senteciaAlquiler ->error ;
                                    } else {
                                       // echo"ok";
                                        $senteciaAlquiler ->bind_param("ss",$_SESSION['id'], $filaAlquiler['id'] );
                                        $senteciaAlquiler ->execute();
                                        if($senteciaAlquiler ->affected_rows >0){
                                            //echo "Se realizaron actualizaciones en los estados de solicitudes de alquiler". "<br>";
                                            $mensaje = $mensaje. "Se realizaron actualizaciones en los estados de solicitudes de alquiler". "<br>";
                                        } else {
                                            //echo "No hubo actualizaciones en los estados de solicitudes de alquiler". "<br>";
                                            $mensaje = $mensaje. "No hubo actualizaciones en los estados de solicitudes de alquiler". "<br>";
                                        }
                                    }

                                   //echo"paso de largo";
                                }
                                $senteciaAlquiler ->close();
                            }
                        }
                      
                    }
                    ////////////////////////////////////////////////////////////////////////////

                    header("Location: index.php");
                    exit;
            
                } else {
                // La contraseña no es válida, mostrar un mensaje de error
                    echo "contraseña incorrecta <br>";
                }

            }
            else{
                echo "No se encontro resultado para la consulta";
            }
        }   
        include "bd/cerrar_conexion.php";  
    }  
         

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
    </head>

    <body>
        <header>
            <?php include("barraDeNavegacion.php"); ?>
        </header>


        <div class="todoElAlto">

            <div class=" col-md-12 text-center" style=" margin-top: 40px;">
                <h2><p> Iniciar Sesion</p></h2>
            </div>
            
            <div class = "container" style=" margin-top: 40px;">
                <form class="row g-5 p-5 " id="formulario" method="post" action="iniciarSesion.php" >
                
                    <div class="col-md-4 m-5">
                        <label for="email" class="form-label"><b>Email</b></label>
                        <input type="email" class="form-control" id="email" name = "email" required>
                    </div>

                    <div class="col-md-4 m-5">
                        <label for="clave" class="form-label"><b>Password</b></label>
                        <input type="password" id="clave" name="clave"  class="form-control" 
                            pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$"required>
                    </div>         

                    <div class="col-12 m-5">
                        <button type="submit" class="btn btn-success" id="enviar" name = "enviar" style = "width: 100px">Iniciar</button>
                    </div>

                </form>
            </div>
        </div>
 
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