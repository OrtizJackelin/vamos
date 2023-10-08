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
    </head>

    <body>
        <header>
            <?php include("barraDeNavegacion.php"); ?>
        </header>

        <section>

            <?php
             
                $valido = true;
                $hash = "";
                $hash_almacenado = "";

                /*if(!isset($_SESSION['email'])){
                    header("Location: index.php");
                    exit;
                }*/
               
                if (isset($_POST['enviar'])) {                

                    if (isset($_POST['email'])) {
                
                        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                            echo "La dirección de correo electrónico es válida.";
                         
                        } else {
                            echo "La dirección de correo electrónico no es válida.";
                            $valido = false;
                        }

                        if (isset($_POST['clave'])){
                
                            $patron = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$/';
        
                            if (preg_match($patron, $_POST['clave'])) {
                                echo "La contraseña cumple con los requisitos.<br>";
                                //$hash = password_hash($_POST['clave'], PASSWORD_DEFAULT);
              
                            } else {
                                echo "La contraseña no cumple con los requisitos.<br>";
                                $valido = false;
        
                            }
                        }
                    }
                }
                
                if (isset($_POST['enviar']) && $valido) {
                    include "bd/conexion.php";
                    if ($conexion->connect_errno) {
                        echo"error, no se conecto <br>";
                        die("$conexion->connect_errno: $conexion->connect_errno");
                    } else {
                        $consulta = "SELECT id,clave FROM user WHERE email=? "; 
                        $sentencia = $conexion->stmt_init();
                        if(!$sentencia->prepare($consulta)){
                            echo "fallo la preparacion de la consulta <br>";
                        }
                        else{
                           
                            $sentencia->bind_param("s", $_POST['email']);
                            $sentencia->execute();
                            $resultado = $sentencia->get_result();
                            //var_dump($resultado);
                            if($fila = $resultado->fetch_array(MYSQLI_ASSOC)){
                                                         
                                // Obtener el hash almacenado en la base de datos para ese usuario
                                $hash_almacenado = $fila["clave"];
                   
                                // Verificar si la contraseña ingresada es válida
                                if (password_verify($_POST['clave'], $hash_almacenado)) {
                                    $_SESSION['id'] = $fila["id"];
                                    header("Location: index.php");
                                    exit;
                                // La contraseña es válida, permitir el acceso
                                } else {
                                // La contraseña no es válida, mostrar un mensaje de error
                                    echo "contraseña incorrecta <br>";
                                }
                            
                            }
                            else{
                                echo "No se encontro resultado para la consulta";
                               // header("Location: index.php");
                                //exit;
                            }
                      
                        }                           
                        
                    }
                    include "bd/cerrar_conexion.php";                
                }         
          
            ?>

            <div class="container w-75 ">

                <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                    <h2> Iniciar Sesion</h2>
                </div>

                <form class="row g-5 p-5 " id="formulario" method="post" action="iniciarSesion.php" >
              
                    <div class="col-md-4 m-5">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name = "email" required>
                    </div>

                    <div class="col-md-4 m-5">
                        <label for="clave" class="form-label">Password</label>
                        <input type="password" id="clave" name="clave"  class="form-control" 
                            pattern="^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$"required>
                    </div>         
    
                    <div class="col-12 ">
                        <button type="submit" class="btn btn-success" id="validar" name = "enviar">Iniciar</button>
                    </div>

                </form><br>
            </div>
        </section>
        <?php //} ?>

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