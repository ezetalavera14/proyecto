<?php
session_start();
include("../../conexion.php");

// Verificar si el usuario es vendedor
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
    echo "<script>alert('Acceso denegado'); window.location.href='index.php';</script>";
    exit();
}

// Verificar que se envió el ID del producto
if (!isset($_GET['id'])) {
    echo "<script>alert('Producto no especificado'); window.location.href='index.php';</script>";
    exit();
}

$id_producto = intval($_GET['id']);

// Primero eliminamos las imágenes del servidor y la tabla imagenes
$sql_imgs = "SELECT ruta FROM imagenes WHERE producto_id = $id_producto";
$resultado_imgs = mysqli_query($enlace, $sql_imgs);

while ($img = mysqli_fetch_assoc($resultado_imgs)) {
    $ruta = "../../" . $img['ruta']; // Ajustar ruta si es necesario
    if (file_exists($ruta)) {
        unlink($ruta); // Borrar archivo
    }
}

mysqli_query($enlace, "DELETE FROM imagenes WHERE producto_id = $id_producto");

// Finalmente, eliminamos el producto
mysqli_query($enlace, "DELETE FROM productos WHERE id = $id_producto");

echo "<script>alert('Producto eliminado correctamente'); window.location.href='index.php';</script>";
exit();
?>
