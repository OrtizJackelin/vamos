<html>
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
        <?php

            if (isset($_POST['volver'])){

                if (isset($_SERVER['HTTP_REFERER'])) {
                    // Obtiene la URL de la página anterior y la almacena en una variable de sesión
                    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
                } else {
                    // Si no hay URL de página anterior, puedes proporcionar una URL de destino predeterminada
                    $_SESSION['previous_page'] = 'index.php'; // Cambia 'index.php' por la URL que desees
                }
                
                // Luego, puedes redireccionar a la página anterior usando header()
                header("Location: " . $_SESSION['previous_page']);
                exit();
            }
    
            if(isset($_GET['mensaje'])){
                $mensajeError = urldecode($_GET['mensaje']);
          
            }
        ?>
        <section class = "sectionPrincipal">
            <div class = "container">
                
                <section>
                <form class="row g-3 " id="formulario" method="post" action="error.php" >

                    <div class="alert alert-primary d-flex align-items-center alert-dismissible" role="alert" style = "margin-top: 20px; margin-bottom: 5px; width:100%;">
                        <?php include "../static/imagenes/redes/exclamation-triangle.svg" ?>                
                        <div>
                            <H6><b><?php echo $mensajeError ?></H6></b>
                        </div>
                        <button type="submit" class="" data-bs-dismiss="alert" aria-label="" name = "volver"></button>
                    </div>         
                </form>
                </section>

                <section>
                    <div style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; ">
                        <img src="../static/imagenes/losiento1.png" class="img-fluid" alt="" style = "height: 500px">
                    </div>
                </section>
        
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