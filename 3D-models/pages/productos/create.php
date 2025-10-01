<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
  header("Location: index.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Subir Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #000;
      color: #fff;
    }

    .form-container {
      max-width: 500px;
      margin: 60px auto;
      padding: 30px;
      background-color: #111;
      border-radius: 10px;
      box-shadow: 0 0 10px #ff00ff88;
    }

    .form-control,
    .form-label {
      color: #fff;
      background-color: #222;
      border: 1px solid #ff00ff;
    }

    .form-control:focus {
      background-color: #222;
      color: #fff;
      border-color: #cc00cc;
      box-shadow: 0 0 5px #cc00cc;
    }

    textarea.form-control {
      resize: vertical;
    }

    .btn-magenta,
    .btn-primary {
      background-color: #ff00ff;
      color: #000;
      border: none;
      transition: background-color 0.3s ease;
    }

    .btn-magenta:hover,
    .btn-primary:hover {
      background-color: #cc00cc;
      color: #fff;
    }

    h2 {
      color: #ff00ff;
      text-align: center;
      margin-bottom: 20px;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <h2>Subir nuevo producto</h2>
    <form action="../../guardar_producto.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="titulo" class="form-label">Título</label>
        <input type="text" class="form-control" name="titulo" id="titulo" required>
      </div>
      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" name="descripcion" id="descripcion" rows="4" required></textarea>
      </div>
      <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input type="number" class="form-control" name="precio" id="precio" step="0.01" required>
      </div>
      <div class="mb-3">
        <label for="imagenes" class="form-label">Imágenes del producto</label>
        <input type="file" class="form-control" name="imagenes[]" id="imagenes" accept="image/*" multiple required>
      </div>
      <div class="mb-3">
        <label for="archivo_fc" class="form-label">Archivo FreeCAD (.FCStd)</label>
        <input type="file" class="form-control" name="archivo_fc" id="archivo_fc" accept=".FCStd" required>
      </div>


      <button type="submit" class="btn btn-magenta w-100">Subir Producto</button>
    </form>
  </div>
</body>

</html>