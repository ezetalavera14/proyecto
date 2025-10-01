<?php
session_start();
session_unset();    // elimina todas las variables de sesión
session_destroy();  // destruye la sesión actual

header("Location: ../productos/index.php"); // redirige al inicio
exit;
?>
