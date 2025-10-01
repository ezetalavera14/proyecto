<?php
session_start();
include("../../conexion.php"); // Ajusta según la ubicación de conexion.php

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $carrito_id = intval($_GET['id']);
    $usuario_id = $_SESSION['usuario']['id'];

    // Eliminar solo si el carrito pertenece al usuario
    $query = "DELETE FROM carrito WHERE id = $carrito_id AND usuario_id = $usuario_id";
    mysqli_query($enlace, $query);
}

// Redirigir de vuelta al carrito
header("Location: carrito.php");
exit();
?>
