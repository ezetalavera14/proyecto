<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
    header("Location: index.php");
    exit();
}

// Validar campos obligatorios
if (empty($_POST['titulo']) || empty($_POST['descripcion']) || empty($_POST['precio']) || empty($_POST['categoria_id'])) {
    die("Faltan campos obligatorios.");
}

$titulo = mysqli_real_escape_string($enlace, $_POST['titulo']);
$descripcion = mysqli_real_escape_string($enlace, $_POST['descripcion']);
$precio = floatval($_POST['precio']);
$categoria_id = intval($_POST['categoria_id']);
$vendedor_id = $_SESSION['usuario']['id'];

// Guardar producto principal
$query = "INSERT INTO productos (titulo, descripcion, precio, vendedor_id, categoria_id)
          VALUES ('$titulo', '$descripcion', $precio, $vendedor_id, $categoria_id)";

$result = mysqli_query($enlace, $query);

if ($result) {
    $producto_id = mysqli_insert_id($enlace);

    // Crear carpetas si no existen
    if (!file_exists("imagenes")) mkdir("imagenes", 0777, true);
    if (!file_exists("videos")) mkdir("videos", 0777, true);
    if (!file_exists("archivos")) mkdir("archivos", 0777, true);

    // Subir imágenes
    if (!empty($_FILES['imagenes']['tmp_name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $key => $tmp_name) {
            $nombre = basename($_FILES['imagenes']['name'][$key]);
            $extension = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                echo "Formato de imagen no permitido: $nombre<br>";
                continue;
            }

            $ruta = "imagenes/" . time() . "_" . $nombre;
            if (move_uploaded_file($tmp_name, $ruta)) {
                mysqli_query($enlace, "INSERT INTO imagenes (producto_id, ruta) VALUES ($producto_id, '$ruta')");
            }
        }
    }

    // Subir videos (solo guardar ruta en BD)
    if (!empty($_FILES['videos']['tmp_name'][0])) {
        foreach ($_FILES['videos']['tmp_name'] as $index => $tmpName) {
            $nombreVideo = basename($_FILES['videos']['name'][$index]);
            $extensionVideo = strtolower(pathinfo($nombreVideo, PATHINFO_EXTENSION));

            if (!in_array($extensionVideo, ['mp4', 'webm', 'mov', 'avi'])) {
                echo "Formato de video no permitido: $nombreVideo<br>";
                continue;
            }

            $rutaVideo = "videos/" . time() . "_" . $nombreVideo;
            if (move_uploaded_file($tmpName, $rutaVideo)) {
                mysqli_query($enlace, "INSERT INTO videos (producto_id, ruta) VALUES ($producto_id, '$rutaVideo')");
            }
        }
    }

    // Subir archivo 3D (FreeCAD / STL / OBJ / etc.)
    if (isset($_FILES['archivo_fc']) && $_FILES['archivo_fc']['error'] === 0) {
        $nombre_fc = $_FILES['archivo_fc']['name'];
        $extension_fc = strtolower(pathinfo($nombre_fc, PATHINFO_EXTENSION));

        if (in_array($extension_fc, ['fcstd', 'stl', 'obj', '3mf'])) {
            $ruta_fc = "archivos/" . time() . "_" . basename($nombre_fc);
            if (move_uploaded_file($_FILES['archivo_fc']['tmp_name'], $ruta_fc)) {
                mysqli_query($enlace, "UPDATE productos SET archivo_fc = '$ruta_fc' WHERE id = $producto_id");
            } else {
                echo "Error al subir archivo 3D.";
            }
        } else {
            echo "Formato de archivo 3D no permitido.";
        }
    }

    header("Location: pages/productos/index.php?msg=exito");
    exit();

} else {
    echo "Error al guardar el producto: " . mysqli_error($enlace);
}
?>
