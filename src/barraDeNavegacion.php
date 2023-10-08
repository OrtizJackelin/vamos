<?php
session_start();

if($_SERVER['PHP_SELF']!="/Practicos/ProyectoProgramacionIII/src/resgistroDeUsuario.php"){
    unset($_SESSION['email']);
}

if (isset($_SESSION['id'])) {
echo "Hola " . $_SESSION['id'];
}
?>
<nav style=" padding: 2px;" class=" navbar navbar-expand-lg navbar-dark sticky-top border-bottom">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="../static/imagenes/imagenesIndex/VamosGrande.png" alt="logo" width="400" height="auto"
            class="d-inline-block align-text-top">
        </a>

        <div class="btn-group " style = "margin-right:60px;">
            <button type="button" class="btn btn-success" >
                <img src="../static/imagenes/redes/person-circle.svg" alt="person-circle">
            </button>

            <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
            data-bs-toggle="dropdown" aria-expanded="false" data-bs-display="static">
                <span class="visually-hidden">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-end">
            <?php
                if (isset($_SESSION['id'])) {
                ?>
                <li><a class="dropdown-item" href="../src/miPerfil.php">Mi perfil</a></li>
                <li><a class="dropdown-item" href="../src/verificarCuenta.php">Verificar Cuenta</a></li>
                <li><a class="dropdown-item" href="../src/publicar.php">Publicar</a></li>
                <li><a class="dropdown-item" href="../src/cerrarSesion.php">Cerrar sesión</a></li>
            <?php } else { ?>
                <li><a class="dropdown-item" href="../src/iniciarSesion.php">Iniciar Sesion</a></li>
                <li><a class="dropdown-item" href="../src/iniciarSesion.php">Publicar Inmueble</a></li>
                <li><a class="dropdown-item" href="../src/resgistroDeUsuario.php">Registrarse</a></li>
            <?php }?>
            </ul>
        </div>
    </div>
</nav>

