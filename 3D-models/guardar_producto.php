<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
    header("Location: index.php");
    exit();
}

// Validar y guardar el producto
$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$precio = floatval($_POST['precio']);
$vendedor_id = $_SESSION['usuario']['id'];

// Guardar producto
$query = "INSERT INTO productos (titulo, descripcion, precio, vendedor_id) 
          VALUES ('$titulo', '$descripcion', $precio, $vendedor_id)";
$result = mysqli_query($enlace, $query);

if ($result) {
    $producto_id = mysqli_insert_id($enlace);

  // Subir imágenes
foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
    $nombre = $_FILES['imagenes']['name'][$key];
    $ruta = "imagenes/" . time() . "_" . basename($nombre); //  guardar en carpeta images
    if (move_uploaded_file($tmp_name, $ruta)) {
        mysqli_query($enlace, "INSERT INTO imagenes (producto_id, ruta) VALUES ($producto_id, '$ruta')");
    } else {
        echo "Error al subir imagen: " . $nombre;
    }
}

// Subir archivo FreeCAD
if (isset($_FILES['archivo_fc']) && $_FILES['archivo_fc']['error'] === 0) {
    $nombre_fc = $_FILES['archivo_fc']['name'];
    $ruta_fc = "archivos/" . time() . "_" . basename($nombre_fc); // guardar en carpeta archivos
    if (move_uploaded_file($_FILES['archivo_fc']['tmp_name'], $ruta_fc)) {
        mysqli_query($enlace, "UPDATE productos SET archivo_fc = '$ruta_fc' WHERE id = $producto_id");
    } else {
        echo "Error al subir archivo FreeCAD.";
    }
}

    header("Location: pages/productos/index.php?msg=exito");
    exit();
} else {
    echo "Error al guardar el producto: " . mysqli_error($enlace);
}
