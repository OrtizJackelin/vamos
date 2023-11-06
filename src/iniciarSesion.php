
<?php
    require_once ('sessionStart.php');
    $valido = true;
    $hash = "";
    $hash_almacenado = "";
    $mensaje = "";


    if ($_SERVER['REQUEST_METHOD'] === 'POST'){              
    
        if (isset($_POST['email'])) {
    
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $mensaje = $mensaje ."La dirección de correo electrónico no es válida.";
                $valido = false;
            
            } 
            if (isset($_POST['clave'])){
    
                $patron = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$/';
                if (!preg_match($patron, $_POST['clave'])) {    
                    $mensaje = $mensaje . "La contraseña no cumple con los requisitos.<br>";
                    $valido = false;
                } 
            } 
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valido) {

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
                    $_SESSION['esVerificado'] = $fila['es_verificado']; 
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
                        //$mensaje = $mensaje. "fallo la preparción". $sentencia->error . "<br>";
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
                                   $senteciaAlquiler ->close();
                                }
                                
                            }
                        }
                      
                    }
                    ////////////////////////////////////////////////////////////////////////////

                    header("Location: index.php");
                    exit;
            
                } else {
                    // La contraseña no es válida, mostrar un mensaje de error
                    $mensaje = $mensaje ."contraseña incorrecta <br>";
                    $valido = false; 
                }

            }
            else{
                //echo "No se encontro resultado para la consulta";
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
        
        <script>
            var alertPlaceholder = "";

            document.addEventListener("DOMContentLoaded", () => {
                const inputs = document.querySelectorAll("input");
                alertPlaceholder = document.getElementById('liveAlertPlaceholder');

                inputs.forEach(
                    function(myinput){
                        myinput.addEventListener("blur",validarInputs);
                    }
                );

                document.querySelector("#enviar").addEventListener("click", function(event) {
                event.preventDefault(); // Evita el envío predeterminado del formulario
                validarFormulario(event);
                });
            });

            function validarFormulario(event) {
                var formulario = document.querySelector("#formulario");
                if (formulario.checkValidity()) {
                formulario.submit();
                } else {
                formulario.reportValidity();
                }
            }

            function validarInputs(event){
            
                if(event.target.id==='email'){
                    validarEmail(event.target.value);
                }
 
                if(event.target.id==='clave'){
                    validarClave(event.target.value);
                  
                }

            }

            function validarEmail(email) {
                var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,3})$/;
                if (regex.test(email)) {
                    event.target.style.borderColor="#ced4da";                    
                } else {
                    event.target.style.borderColor="crimson";
                    alert('El correo no cumple con un formato válido.', 'warning');                   
                }
            }

            function validarClave(clave){
                var regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[#$%^&+=!])(?!.*\s).{8,}$/;
                if(regex.test(clave)) {
                    event.target.style.borderColor="#ced4da";                  
                } else {
                    event.target.style.borderColor="crimson";
                    alert('Contraseña no cumple con el formato.', 'warning');                   
                }
            }

            function alert(message, type) {
                var wrapper = document.createElement('div');
                wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' +
                '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">' +
                '<path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.980 1.767h13.713c.889 0 1.438-.99.980-1.767L8.982 1.566zM8 5c.535 0 .954.462.900.995l-.350 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>' +
                '</svg>' +
                message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';

                alertPlaceholder.append(wrapper);
            }
        </script>
    </head>   

    <body class="background2">
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
            
            <div id="liveAlertPlaceholder"></div>
            <?php
            if(!$valido){ ?>
                <div class="alert alert-warning d-flex align-items-center alert-dismissible" role="alert"
                    style="margin-top: 20px; margin-bottom: 5px;" type="hidedeng">
                    <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>
                <div>
                    <H6><b><?php echo $mensaje ?></H6></b>
                </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" ></button>                
                </div>
                <?php
            } ?>
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