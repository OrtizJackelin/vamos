<?php
    require_once ('sessionStart.php');

    try{
        include "bd/conexion.php";
        //throw new Exception("Error Processing Request", 1);        
    
    } catch (mysqli_sql_exception $e) {
        $mensajeError = "Error en la conexión a la base de datos: " . $e->getMessage();
        // redirigir al cliente a una página de error personalizada o mostrar un mensaje en la página actual
        header("Location: error.php?mensaje=" . urlencode($mensajeError));
            
    }
    extract($_POST);
    //var_dump($_POST);


    $consulta = "SELECT p.id,p.fecha_inicio_publicacion,p.fecha_fin_publicacion, p.costo, p.ubicacion,
                    IF (u.es_verificado=1,'destacada','') estilo, 
                    (SELECT ruta from imagen i WHERE i.id_publicacion = p.id ORDER BY id LIMIT 1 ) ruta
                FROM publicacion p, user u
                WHERE p.id_usuario = u.id
                AND estado =1 
                and (fecha_fin_publicacion >= NOW() || fecha_inicio_publicacion IS NULL AND fecha_fin_publicacion  IS NULL)
                AND (
                    p.titulo LIKE ?
                    OR p.descripcion LIKE ?
                    OR p.ubicacion LIKE ?
                    OR p.id in (SELECT ep.id_publicacion from etiqueta e, etiqueta_publicacion ep 
                                WHERE e.id=ep.id_etiqueta AND e.nombre LIKE ?)
                )
                ORDER BY U.es_verificado DESC";

    $sentencia = $conexion->stmt_init();
    if (!$sentencia->prepare($consulta)) {
        echo "Fallo la preparación de la consulta <br>";
    } else {
        $buscar = "%" . $buscar . "%";
        $sentencia->bind_param("ssss",$buscar, $buscar, $buscar, $buscar);
        $sentencia->execute();
        $resultado = $sentencia->get_result();
        $sentencia->close();
    }

    $superString = "";

    if($resultado->num_rows == 0){
        echo "<p style = \"color:gray;\"><h4>No se encontrarón coincidencias</h4></p>";
        
    } else {
    
        while ($publicacion = $resultado->fetch_array(MYSQLI_ASSOC)) {

            $superString = $superString. "<div class=\"card bore border-2 border-end\" style=\"width: 20rem; margin: 10px;\">";
                if($publicacion['ruta']=="") {
                    $superString.= "<img src=\"../static/imagenes/nofoto.jpg\" class=\"card-img-top\" alt=\"...\" >";
                }
                else {
                    $superString.= "<img src=\"../static/imagenes/publicaciones/" . $publicacion['id']. "/" . $publicacion['ruta'] . "\" 
                    class=\"card-img-top \" alt=\"...\" >";
                }
        
                $superString.= "<div class=\"card-body\" style=\"background-color: rgb(223, 221, 221);\">";
                $superString.=  "<p class=\"card-text\"> Ubicación:".  $publicacion['ubicacion']. "</p>";
                $superString.=   "<p class=\"card-text\"> Disponible: "; if($publicacion['fecha_inicio_publicacion'] == "" || $publicacion['fecha_fin_publicacion'] == "" ){
                    $superString.= "Sin límite";
                        } else {
                            $superString.= $publicacion['fecha_inicio_publicacion'] . " al " . $publicacion['fecha_fin_publicacion']; 
                        }
                        $superString.= "</p>
                    <p class=\"card-text\"> Costo: ". $publicacion['costo']." </p>
                    <a href= \"detallePublicacion.php?id=".  $publicacion['id']. "\"
                    class = \"btn\" id = \"btnCard\" >Ir a publicaci&oacute;n
                    </a>                     
                </div>
            </div>";

        } 
        echo $superString;
    }
    include "bd/cerrar_conexion.php";
?>