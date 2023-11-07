<?php

    //////////////////////////////////////////////Validaciones Servidor//////////////////////////////////////////////////////
    
    if(isset($_POST['clave']) && !empty($_POST['clave'])) {
    
        if (isset($_POST['clave'])) {

            $patron = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@#$%^&+=!])(?!.*\s).{8,}$/';

            if (!preg_match($patron, $_POST['clave'])) {

                $mensaje = $mensaje." La contraseña no cumple con los requisitos.<br>";
                $valido = false;
                
            }
          
        } 

    } else {
        $mensaje = $mensaje." Debe ingresar clave <br>";
       
    }

    if (isset($_POST['repetirClave']) && !empty($_POST['repetirClave'])){

        if($_POST['clave'] === $_POST['repetirClave']){
        // Generar un hash seguro de la contraseña
            $hash = password_hash($_POST['clave'], PASSWORD_DEFAULT); 
            $actualizarClave = true;
           
        } else {
            $mensaje = $mensaje." Las claves no coinciden <br>";
            $valido = false;  
            
        }    

    } else {
        $mensaje = $mensaje." Debe repetir la misma clave <br>";
      
    }


    

    if (isset($_POST['nombre']) && !empty($_POST['nombre'])){

        $patron = "/^[A-Za-z\s]+$/";

        // Utiliza la función preg_match para verificar si el valor cumple con el patrón
        if (!preg_match($patron, $_POST['nombre'])) {
            $mensaje = $mensaje. " El campo Nombre no es válido. Debe contener solo letras y espacios<br>";
            $valido = false;
        }                     
    }

    if (isset($_POST['apellido']) && !empty($_POST['apellido'])){

        $patron = "/^[A-Za-z\s]+$/";

        // Utiliza la función preg_match para verificar si el valor cumple con el patrón
        if (!preg_match($patron, $_POST['apellido'])) {
            $mensaje = $mensaje. " El campo Apellido no es válido. Debe contener solo letras y espacios<br>";
            $valido = false;
        }

    }

    if (isset($_POST['fechaNacimiento']) && !empty($_POST['fechaNacimiento'])){

        $fechaActual = new DateTime();
        $fechaNacimiento = $_POST['fechaNacimiento'];                    
        $fechaNacimiento = new DateTime($fechaNacimiento);
        $edadMinima = 18; // Edad mínima requerida
        $diferencia = $fechaNacimiento->diff($fechaActual);

        if ($fechaNacimiento > $fechaActual) {
            $mensaje = $mensaje." La fecha ingresada no puede ser superior a la fecha actual. <br>";
            $valido = false;
        } elseif ($diferencia->y < $edadMinima) {
            $mensaje = $mensaje." Debes tener al menos 18 años para registrarte.<br>";
            $valido = false;
        } else  {               
            $fechaFormateada = $fechaNacimiento->format('Y-m-d');
        }                     

    }

    if (isset($_POST['fechaN']) && !empty($_POST['fechaN'])){

        $fechaActual = new DateTime();
        $fechaNacimiento = $_POST['fechaN'];                    
        $fechaNacimiento = new DateTime($fechaNacimiento);
        $edadMinima = 18; // Edad mínima requerida
        $diferencia = $fechaNacimiento->diff($fechaActual);

        if ($fechaNacimiento > $fechaActual) {
            $mensaje = $mensaje." La fecha ingresada no puede ser superior a la fecha actual. <br>";
            $valido = false;
        } elseif ($diferencia->y < $edadMinima) {
            $mensaje = $mensaje." Debes tener al menos 18 años para registrarte.<br>";
            $valido = false;
        } else  {               
            $fechaFormateada = $fechaNacimiento->format('Y-m-d');
        }                     

    }

    if (!isset($_POST['codPais'])){

        $valido = false;
        $mensaje = $mensaje." Debe seleccionar el codigo de pa&iacute;s.<br>";

    }
    if (!isset($_POST['telefono']) || empty($_POST['telefono'])){

        $valido = false;
        $mensaje = $mensaje." Debe ingresar un n&uacute;mero de tel&eacute;fono valido.<br>";

    }
    if (!isset($_POST['dni']) || empty($_POST['dni'])){

        $valido = false;
        $mensaje = $mensaje." Debe ingresar un dni valido.<br>";

    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////////
?>