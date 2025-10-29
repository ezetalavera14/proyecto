<?php
session_start();
include("conexion.php");

// Solo vendedores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
    header("Location: pages/auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_producto   = intval($_POST['id']);
    $titulo        = mysqli_real_escape_string($enlace, $_POST['titulo']);
    $descripcion   = mysqli_real_escape_string($enlace, $_POST['descripcion']);
    $precio        = floatval($_POST['precio']);
    $categoria_id  = intval($_POST['categoria_id']);

    // Carpetas
    $carpeta_imagenes = "imagenes/";
    $carpeta_videos   = "videos/";
    $carpeta_3d       = "archivos/";

    if (!file_exists($carpeta_imagenes)) mkdir($carpeta_imagenes, 0777, true);
    if (!file_exists($carpeta_videos)) mkdir($carpeta_videos, 0777, true);
    if (!file_exists($carpeta_3d)) mkdir($carpeta_3d, 0777, true);

    // Actualizar datos principales del producto
    $query_update = "UPDATE productos SET titulo='$titulo', descripcion='$descripcion', precio=$precio, categoria_id=$categoria_id WHERE id=$id_producto";
    mysqli_query($enlace, $query_update);

    // Función para validar extensiones
    function validar_extension($nombre, $ext_permitidas) {
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        return in_array($ext, $ext_permitidas);
    }

    // =================== IMÁGENES ===================
    $ext_img_permitidas = ['jpg','jpeg','png','webp'];
    if (!empty($_FILES['imagenes']['name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            $nombre = basename($_FILES['imagenes']['name'][$key]);
            if (!validar_extension($nombre, $ext_img_permitidas)) continue;
            $ruta = $carpeta_imagenes . uniqid() . "_" . $nombre;
            if (move_uploaded_file($tmp_name, $ruta)) {
                mysqli_query($enlace, "INSERT INTO imagenes (producto_id, ruta) VALUES ($id_producto, '$ruta')");
            }
        }
    }

    // =================== VIDEOS ===================
    $ext_vid_permitidas = ['mp4','webm','mov'];
    if (!empty($_FILES['videos']['name'][0])) {
        foreach ($_FILES['videos']['tmp_name'] as $key => $tmp_name) {
            $nombre = basename($_FILES['videos']['name'][$key]);
            if (!validar_extension($nombre, $ext_vid_permitidas)) continue;
            $ruta = $carpeta_videos . uniqid() . "_" . $nombre;
            if (move_uploaded_file($tmp_name, $ruta)) {
                mysqli_query($enlace, "INSERT INTO videos (producto_id, ruta) VALUES ($id_producto, '$ruta')");
            }
        }
    }

    // =================== ARCHIVO 3D ===================
    $ext_3d_permitidas = ['fcstd','stl','obj','3mf','step','iges'];
    if (!empty($_FILES['archivos_3d']['name'][0])) {
        foreach ($_FILES['archivos_3d']['tmp_name'] as $key => $tmp_name) {
            $nombre = basename($_FILES['archivos_3d']['name'][$key]);
            if (!validar_extension($nombre, $ext_3d_permitidas)) continue;
            $ruta = $carpeta_3d . uniqid() . "_" . $nombre;
            if (move_uploaded_file($tmp_name, $ruta)) {
                // Guardar ruta en la columna archivo_3d de productos
                mysqli_query($enlace, "UPDATE productos SET archivo_3d='$ruta' WHERE id=$id_producto");
            }
        }
    }

    header("Location: pages/productos/index.php?mensaje=actualizado");
    exit();

} else {
    echo "Método no permitido.";
}
?>
