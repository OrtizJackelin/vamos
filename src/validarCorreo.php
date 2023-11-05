<?php
    require_once ('sessionStart.php');

    $valido = true;
    $mensaje = "";

    try{
        include "bd/conexion.php";

    } catch(mysqli_sql_exception $e){
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST'){

        //var_dump($_SERVER['REQUEST_METHOD']);
        if (isset($_POST['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $mensaje =  $mensaje." La dirección de correo electrónico tiene un formato inválido.<br>";
                $valido = false;
            } 
        }              
       // echo "entro a enviar";

        $consulta = "SELECT email from user where email = ?";
        $sentencia = $conexion->stmt_init();
        
        if (!$sentencia->prepare($consulta)) {
            //echo "fallo la preparacion de la consulta.<br>";
            //$mensaje =  $mensaje." Fallo la preparacion de la consulta.<br>";
            //$valido = false;
        } else {
            $sentencia->bind_param("s",$_POST['email']);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            //echo"entro en la conslta";
            if ($resultado->num_rows > 0) {
                $mensaje =  $mensaje." Correo ya existe<br>";
                $valido = false;  
            } else {                   
                $_SESSION['email'] = $_POST['email'];
                header("Location: resgistroDeUsuario.php");
                exit;
            }
        }
    }       
    
    include "bd/cerrar_conexion.php"; 
                    
?>
<!DOCTYPE html>
<html style="display:flex; flex-direction:column;height:100%;" lang="es">

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
            const email = document.getElementById("email");
            alertPlaceholder = document.getElementById('liveAlertPlaceholder');

            email.addEventListener("blur", function(event) {
            validarEmail(event);
            });

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

        function validarEmail(event) {
            var regex = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,3})$/;
            if (regex.test(event.target.value)) {
                event.target.style.borderColor = "#ced4da";
            } else {
                alert('El correo no cumple con un formato válido.', 'warning');
                event.target.style.borderColor = "crimson";
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

<body style=" display:flex; flex-direction:column;height:100%;" class="background2">
    <header>
        <?php include("barraDeNavegacion.php"); ?>
    </header>

    <!--FORMULARIO-->
    <div class="todoElAlto">

        <div class="container w-75 ">

            <div class=" col-md-12 text-center" style=" margin-top: 40px;">
                <h2> Validar Correo</h2>
            </div>

            <div class = "container" style=" margin-top: 40px;">
                <form class="row g-3 " id="formulario" method="post" action="validarCorreo.php" >


                    <div class="col-md-4">
                        <label for="email" class="form-label"><b>Email</b></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>


                    <div class="col-12 ">
                        <button type="submit" class="btn btn-secondary" id="enviar" 
                        name="enviar" style = "width: 100px">Validar</button>
                    </div>

                </form><br>
                <div id="liveAlertPlaceholder"></div> 
            </div>
            <?php
            if(!$valido){
                ?>
            <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert"
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
    </div>
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