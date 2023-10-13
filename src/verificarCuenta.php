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
             
            ?>

            <div class="container w-75 ">

                <div class=" col-md-12 text-center" style=" margin-top: 20px;">
                    <h2> Verificaci&oacute;n De Cuenta</h2>
                </div>

                <form class="row g-5 p-5 " id="formulario" method="post" action="iniciarSesion.php" >


                    <div class="col-md-12 mb-3">
                        <label for="comentario" class="form-label">Comentarios</label>
                        <textarea class="form-control" id="comentario" name= "comentario" rows="3" ></textarea>
                    </div>  
                    
                                  
                    <div class="col-md-4 mb-3" >
                        <label for="formFile" class="form-label">Subir Documento</label>
                        <input class="form-control" type="file" id="formFile"  name="documento" multiple accept="image/*">
                    </div>
      
    
                    <div class="col-12 ">
                        <button type="submit" class="btn btn-success" id="validar" name = "enviar">Solicitar</button>
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