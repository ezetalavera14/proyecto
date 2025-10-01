<?php
session_start();
include("../../conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

// Obtener los productos del carrito
$sql = "SELECT * FROM carrito WHERE usuario_id = $usuario_id";
$resultado = mysqli_query($enlace, $sql);

while ($carrito = mysqli_fetch_assoc($resultado)) {
    $producto_id = $carrito['producto_id'];
    $cantidad = $carrito['cantidad'];

    // Registrar la compra
    $insert = "INSERT INTO compras (usuario_id, producto_id, cantidad, fecha_compra) 
               VALUES ($usuario_id, $producto_id, $cantidad, NOW())";
    mysqli_query($enlace, $insert);

    // Descontar stock
    $update = "UPDATE productos SET stock = stock - $cantidad WHERE id = $producto_id";
    mysqli_query($enlace, $update);
}

// Vaciar carrito
$delete = "DELETE FROM carrito WHERE usuario_id = $usuario_id";
mysqli_query($enlace, $delete);

// Redirigir a página de confirmación
header("Location: ../../confirmacion.php");
exit();
?>
