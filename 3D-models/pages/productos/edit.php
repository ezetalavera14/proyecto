<?php
session_start();
include("../../conexion.php");

// Solo vendedores
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = intval($_GET['id']);

// Obtener producto
$sql = "SELECT * FROM productos WHERE id=$id";
$resultado = mysqli_query($enlace, $sql);
if (mysqli_num_rows($resultado) === 0) {
    echo "Producto no encontrado";
    exit();
}
$producto = mysqli_fetch_assoc($resultado);

// Guardar cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = mysqli_real_escape_string($enlace, $_POST['titulo']);
    $descripcion = mysqli_real_escape_string($enlace, $_POST['descripcion']);
    $precio = floatval($_POST['precio']);

    // Actualizar producto
    $sql_update = "UPDATE productos SET titulo='$titulo', descripcion='$descripcion', precio=$precio WHERE id=$id";
    mysqli_query($enlace, $sql_update);

    // Subir nuevas imágenes
    if (!empty($_FILES['imagenes']['tmp_name'][0])) {
        foreach ($_FILES['imagenes']['tmp_name'] as $index => $tmpName) {
            $nombreOriginal = basename($_FILES['imagenes']['name'][$index]);
            $rutaDestino = "../../imagenes/" . uniqid() . "_" . $nombreOriginal;

            if (move_uploaded_file($tmpName, $rutaDestino)) {
                $sqlImagen = "INSERT INTO imagenes (ruta, producto_id) VALUES ('imagenes/" . uniqid() . "_" . $nombreOriginal . "', $id)";
                mysqli_query($enlace, $sqlImagen);
            }
        }
    }

    // Subir archivo FreeCAD
    if (isset($_FILES['archivo']['tmp_name']) && $_FILES['archivo']['tmp_name'] != '') {
        $nombreArchivo = basename($_FILES['archivo']['name']);
        $rutaArchivo = "../../archivos/" . uniqid() . "_" . $nombreArchivo;

        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaArchivo)) {
            // Actualizar la ruta en la base de datos
            $sqlArchivo = "UPDATE productos SET ruta_archivo='$rutaArchivo' WHERE id=$id";
            mysqli_query($enlace, $sqlArchivo);
        }
    }

    echo "<script>alert('Producto actualizado'); window.location.href='index.php';</script>";
    exit();
}

// Obtener imágenes existentes
$sql_imgs = "SELECT * FROM imagenes WHERE producto_id=$id";
$res_imgs = mysqli_query($enlace, $sql_imgs);
$imagenes = mysqli_fetch_all($res_imgs, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Producto</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    background-color: #F5EFE6;
    color: #333;
    font-family: 'Segoe UI', sans-serif;
  }

  .form-container {
    max-width: 500px;
    margin: 60px auto;
    padding: 30px;
    background-color: #CBDCEB;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(109, 148, 197, 0.3);
  }

  .form-label {
    color: #333;
    font-weight: 600;
  }

  .form-control {
    color: #333;
    background-color: #F5EFE6;
    border: 1px solid #6D94C5;
    border-radius: 6px;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
  }

  .form-control:focus {
    background-color: #fff;
    border-color: #5c7fb0;
    box-shadow: 0 0 5px rgba(92, 127, 176, 0.7);
    outline: none;
  }

  .btn-magenta {
    background-color: #6D94C5;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .btn-magenta:hover {
    background-color: #5c7fb0;
  }

  h2 {
    color: #6D94C5;
    text-align: center;
    margin-bottom: 20px;
    font-weight: 700;
  }
</style>

</head>
<body>
<div class="form-container">
<h2>Editar Producto</h2>
<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Título</label>
        <input type="text" name="titulo" class="form-control" value="<?php echo htmlspecialchars($producto['titulo']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="4" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Precio</label>
        <input type="number" name="precio" class="form-control" step="0.01" value="<?php echo $producto['precio']; ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Agregar nuevas imágenes</label>
        <input type="file" name="imagenes[]" class="form-control" multiple accept="image/*">
    </div>

    <div class="mb-3">
        <label class="form-label">Archivo FreeCAD</label>
        <input type="file" name="archivo" class="form-control" accept=".FCStd">
        <?php if ($producto['ruta_archivo']): ?>
            <p>Archivo actual: <?php echo basename($producto['ruta_archivo']); ?></p>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-magenta w-100">Actualizar Producto</button>
</form>
</div>
</body>
</html>
