<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
  header("Location: index.php");
  exit();
}
include("../../conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    background-color: #F5EFE6;
    color: #333;
    font-family: 'Segoe UI', sans-serif;
  }

  .form-container {
    max-width: 600px;
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

  .form-control, .form-select {
    color: #333;
    background-color: #F5EFE6;
    border: 1px solid #6D94C5;
    border-radius: 5px;
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
  }

  .form-control:focus, .form-select:focus {
    background-color: #fff;
    color: #000;
    border-color: #6D94C5;
    box-shadow: 0 0 5px rgba(109, 148, 197, 0.5);
  }

  .btn-magenta {
    background-color: #6D94C5;
    color: #fff;
    border: none;
    transition: background-color 0.3s ease;
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: 500;
  }

  .btn-magenta:hover {
    background-color: #5c7fb0;
  }

  h2 {
    color: #6D94C5;
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
  }
  </style>
</head>
<body>

<div class="form-container">
  <h2>Subir nuevo producto</h2>
  <form action="../../guardar_producto.php" method="POST" enctype="multipart/form-data" onsubmit="return validarFormulario()">
    
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
      <input type="number" class="form-control" name="precio" id="precio" step="0.01" min="0" required>
    </div>

    <div class="mb-3">
      <label for="categoria_id" class="form-label">Categoría</label>
      <select name="categoria_id" id="categoria_id" class="form-select" required>
        <option value="">Seleccionar categoría</option>
        <?php
          $categorias = mysqli_query($enlace, "SELECT * FROM categorias ORDER BY nombre ASC");
          while ($cat = mysqli_fetch_assoc($categorias)) {
            echo "<option value='{$cat['id']}'>{$cat['nombre']}</option>";
          }
        ?>
      </select>
    </div>

    <!-- Imágenes -->
    <div class="mb-3">
      <label for="imagenes" class="form-label">Imágenes del producto (puedes subir varias)</label>
      <input type="file" class="form-control" name="imagenes[]" id="imagenes" accept="image/*" multiple required>
    </div>

    <!-- Videos -->
    <div class="mb-3">
      <label for="videos" class="form-label">Video del producto (opcional, formatos: mp4, mov, avi)</label>
      <input type="file" class="form-control" name="videos[]" id="videos" accept="video/mp4,video/mov,video/avi" multiple>
    </div>

    <!-- Archivos 3D -->
    <div class="mb-3">
      <label for="archivo_3d" class="form-label">Archivo 3D (acepta: .FCStd, .STL, .OBJ, .3MF, .STEP, .IGES)</label>
      <input type="file" class="form-control" name="archivo_3d" id="archivo_3d"
             accept=".FCStd,.stl,.STL,.obj,.OBJ,.3mf,.3MF,.step,.STEP,.iges,.IGES" required>
    </div>

    <button type="submit" class="btn btn-magenta w-100">Subir Producto</button>
  </form>
</div>

<script>
function validarFormulario() {
  const titulo = document.getElementById('titulo').value.trim();
  const descripcion = document.getElementById('descripcion').value.trim();
  const precio = document.getElementById('precio').value;
  const categoria = document.getElementById('categoria_id').value;
  const imagenes = document.getElementById('imagenes').files;
  const archivo3D = document.getElementById('archivo_3d').files[0];

  if (!titulo || !descripcion || !precio || !categoria) {
    alert('Por favor, completa todos los campos obligatorios.');
    return false;
  }

  if (imagenes.length === 0) {
    alert('Debes subir al menos una imagen del producto.');
    return false;
  }

  if (!archivo3D) {
    alert('Debes subir al menos un archivo 3D.');
    return false;
  }

  return true;
}
</script>

</body>
</html>
