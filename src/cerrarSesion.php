<?php
    session_start();
    unset($_SESSION['id']);
    unset($_SESSION['nombre']);
    unset($_SESSION['esVerificado']);
    unset($_SESSION['esAdministrador']);
    session_destroy();
    header("Location: index.php");
    exit;
?>