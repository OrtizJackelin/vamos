<?php
require_once('sessionStart.php');

if($_SERVER['PHP_SELF']!="/Practicos/ProyectoProgramacionIII/src/resgistroDeUsuario.php"){
    unset($_SESSION['email']);
   /* unset($_SESSION['nombre']);
    unset($_SESSION['esVerificado']);
    unset($_SESSION['id']);*/
}


?>
<nav style=" padding: 2px; margin-left:0px!important; background:white; " class=" navbar navbar-expand-lg navbar-dark sticky-top border-bottom background ">
    <div class="container-fluid">
        <div>
            <a class="navbar-brand" href="index.php">
            <img src="../static/imagenes/imagenesIndex/VamosGrande.png" alt="logo" width="400" height="auto"
                class="d-inline-block align-text-top">
            </a>
            <p style = " font-weight: bold; font-size:12px; color:#7e7e7e;padding:0px; 
            margin:0!important;"><?php if (isset($_SESSION['id'])) echo "Hola " . $_SESSION['nombre'];?></p>
        </div>

        <div class="btn-group " style="margin-right:60px; z-index:1111;">
            <?php if (isset($_SESSION['id'])) {  ?>
                <a style="text-decoration:none; padding:0px; border-radius:0px; " class="dropdown-item"
                    href="../src/miPerfil.php">
                    <button type="button" class="btn btn-success" style="height:100%;"> <img
                            src="../static/imagenes/redes/person-circle.svg" alt="person-circle">
                    </button>
                </a>
            
            <?php } else { ?>
                <a style="text-decoration:none; padding:0px; border-radius:0px; " class="dropdown-item"
                href="../src/iniciarSesion.php">
                    <button type="button" class="btn btn-success" style="height:100%;"> <img
                            src="../static/imagenes/redes/person-circle.svg" alt="person-circle">
                    </button>
                </a>
          
            <?php }?>
           

            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-end">
                <?php
                if (isset($_SESSION['id'])) {
                    if(isset($_SESSION['esAdministrador']) && $_SESSION['esAdministrador'] == 1 ){
                    ?>
                        <li><a class="dropdown-item" href="../src/miPerfil.php">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="../src/misActividades.php">Mis Actividades</a></li>
                        <li><a class="dropdown-item" href="../src/solicitarVerificarCuenta.php">Verificar Cuenta</a></li>
                        <li><a class="dropdown-item" href="../src/publicar.php">Publicar</a></li>
                        <li><a class="dropdown-item" href="../src/administrador.php">Solicitudes</a></li>
                        <li><a class="dropdown-item" href="../src/cerrarSesion.php">Cerrar sesión</a></li>
                    <?php
                    } else {
                    ?>
                        <li><a class="dropdown-item" href="../src/miPerfil.php">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="../src/misActividades.php">Mis Actividades</a></li>
                        <li><a class="dropdown-item" href="../src/solicitarVerificarCuenta.php">Verificar Cuenta</a></li>
                        <li><a class="dropdown-item" href="../src/publicar.php">Publicar</a></li>
                        <li><a class="dropdown-item" href="../src/cerrarSesion.php">Cerrar sesión</a></li>

                    <?php 
                    }        
                } else { ?>
                <li><a class="dropdown-item" href="../src/iniciarSesion.php">Iniciar Sesion</a></li>
                <li><a class="dropdown-item" href="../src/iniciarSesion.php">Publicar Inmueble</a></li>
                <li><a class="dropdown-item" href="../src/resgistroDeUsuario.php">Registrarse</a></li>
                <?php }?>
            </ul>
        </div>
    </div>
</nav>