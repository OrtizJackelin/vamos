<?php
    session_start();
    unset($_SESSION['id']);
    unset($_SESSION['nombre']);
    unset($_SESSION['esVerificado']);
    header("Location: index.php");
    exit;
?>