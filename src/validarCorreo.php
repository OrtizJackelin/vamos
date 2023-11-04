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

    if (isset($_POST['enviar'])) {
        if (isset($_POST['email'])) {
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $mensaje =  $mensaje." La dirección de correo electrónico tiene un formato inválido.<br>";
                $valido = false;
            } 
        }              
        echo "entro a enviar";

        $consulta = "SELECT email from user where email = ?";
        $sentencia = $conexion->stmt_init();
        
        if (!$sentencia->prepare($consulta)) {
            echo "fallo la preparacion de la consulta.<br>";
            $mensaje =  $mensaje." Fallo la preparacion de la consulta.<br>";
            $valido = false;
        } else {
            $sentencia->bind_param("s",$_POST['email']);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            echo"entro en la conslta";
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
    <!--<script type="text/javascript" src="formularioEvento.js"></script>-->
</head>

<body style=" display:flex; flex-direction:column;height:100%;">
    <header>
        <?php include("barraDeNavegacion.php"); ?>
    </header>

    <!--FORMULARIO-->
    <section class = "sectionPrincipal">

        <div class="container w-75 ">

            <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                <h2> Validar Correo</h2>
            </div>

            <form class="row g-3 " id="formulario" method="post" action="validarCorreo.php">


                <div class="col-md-4">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>


                <div class="col-12 ">
                    <button type="submit" class="btn btn-secondary" id="validar" name="enviar">Validar</button>
                </div>

            </form><br>
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