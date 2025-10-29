<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo_usuario'] !== 'vendedor') {
  header("Location: index.php");
  exit();
}

include("../../conexion.php");

if (!isset($_GET['id'])) {
  echo "ID de producto no especificado.";
  exit();
}

$id_producto = intval($_GET['id']);
$query = "SELECT * FROM productos WHERE id = $id_producto";
$resultado = mysqli_query($enlace, $query);
if (!$resultado || mysqli_num_rows($resultado) === 0) {
  echo "Producto no encontrado.";
  exit();
}
$producto = mysqli_fetch_assoc($resultado);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0b0c10;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }
    .form-container {
      max-width: 600px;
      margin: 60px auto;
      padding: 30px;
      background-color: #1f2833;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(102, 0, 204, 0.3);
    }
    .form-label {
      color: #c5c6c7;
      font-weight: 500;
    }
    .form-control, .form-select {
      color: #fff;
      background-color: #202833;
      border: 1px solid #45a29e;
      border-radius: 8px;
      transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
      border-color: #66fcf1;
      box-shadow: 0 0 10px rgba(102, 252, 241, 0.5);
    }
    .btn-primary {
      background-color: #9500ff;
      border: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      background-color: #7200cc;
    }
    h2 {
      color: #66fcf1;
      text-align: center;
      margin-bottom: 25px;
      font-weight: bold;
    }
    .preview {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    .preview img, .preview video {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 6px;
      border: 1px solid #45a29e;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Editar Producto</h2>
    <form action="../../actualizar_producto.php" method="POST" enctype="multipart/form-data" id="editarForm">

      <input type="hidden" name="id" value="<?= $producto['id'] ?>">

      <div class="mb-3">
        <label for="titulo" class="form-label">Título</label>
        <input type="text" class="form-control" name="titulo" id="titulo" value="<?= htmlspecialchars($producto['titulo']) ?>" required>
      </div>

      <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" name="descripcion" id="descripcion" rows="4" required><?= htmlspecialchars($producto['descripcion']) ?></textarea>
      </div>

      <div class="mb-3">
        <label for="precio" class="form-label">Precio</label>
        <input type="number" class="form-control" name="precio" id="precio" step="0.01" min="0" value="<?= htmlspecialchars($producto['precio']) ?>" required>
      </div>

      <!-- CATEGORÍA -->
      <div class="mb-3">
        <label for="categoria_id" class="form-label">Categoría</label>
        <select name="categoria_id" id="categoria_id" class="form-select" required>
          <option value="">Seleccionar categoría</option>
          <?php
            $categorias = mysqli_query($enlace, "SELECT * FROM categorias ORDER BY nombre ASC");
            while ($cat = mysqli_fetch_assoc($categorias)) {
              $selected = ($cat['id'] == $producto['categoria_id']) ? 'selected' : '';
              echo "<option value='{$cat['id']}' $selected>{$cat['nombre']}</option>";
            }
          ?>
        </select>
      </div>

      <!-- IMÁGENES -->
      <div class="mb-3">
        <label for="imagenes" class="form-label">Actualizar Imágenes (JPG, PNG, WEBP)</label>
        <input type="file" class="form-control" name="imagenes[]" id="imagenes" accept=".jpg,.jpeg,.png,.webp" multiple>
        <div class="preview">
          <?php
            $imgQuery = mysqli_query($enlace, "SELECT ruta FROM imagenes WHERE producto_id = $id_producto");
            while ($img = mysqli_fetch_assoc($imgQuery)) {
              echo "<img src='../../{$img['ruta']}' alt='Imagen actual'>";
            }
          ?>
        </div>
      </div>

      <!-- VIDEOS -->
      <div class="mb-3">
        <label for="videos" class="form-label">Actualizar Videos (MP4, WEBM, MOV)</label>
        <input type="file" class="form-control" name="videos[]" id="videos" accept=".mp4,.webm,.mov" multiple>
      </div>

      <!-- ARCHIVOS 3D -->
      <div class="mb-3">
        <label for="archivos_3d" class="form-label">Actualizar Archivos 3D (.FCStd, .STL, .OBJ, .3MF, .STEP, .IGES)</label>
        <input type="file" class="form-control" name="archivos_3d[]" id="archivos_3d" accept=".fcstd,.stl,.obj,.3mf,.step,.iges" multiple>
      </div>

      <button type="submit" class="btn btn-primary w-100">Guardar Cambios</button>
    </form>
  </div>

  <script>
    // Validación frontend
    document.getElementById("editarForm").addEventListener("submit", function(e) {
      const titulo = document.getElementById("titulo").value.trim();
      const descripcion = document.getElementById("descripcion").value.trim();
      const precio = parseFloat(document.getElementById("precio").value);
      const categoria = document.getElementById("categoria_id").value;

      if (!titulo || !descripcion || !precio || !categoria) {
        alert("Por favor, completa todos los campos obligatorios.");
        e.preventDefault();
        return;
      }

      const validarArchivos = (input, extensiones) => {
        const archivos = input.files;
        if (archivos.length === 0) return true;
        return [...archivos].every(file => extensiones.test(file.name));
      };

      if (!validarArchivos(document.getElementById("imagenes"), /\.(jpg|jpeg|png|webp)$/i)) {
        alert("Solo se permiten imágenes JPG, PNG o WEBP.");
        e.preventDefault();
      }

      if (!validarArchivos(document.getElementById("videos"), /\.(mp4|webm|mov)$/i)) {
        alert("Solo se permiten videos MP4, WEBM o MOV.");
        e.preventDefault();
      }

      if (!validarArchivos(document.getElementById("archivos_3d"), /\.(fcstd|stl|obj|3mf|step|iges)$/i)) {
        alert("Solo se permiten archivos 3D con extensiones .FCStd, .STL, .OBJ, .3MF, .STEP o .IGES.");
        e.preventDefault();
      }
    });
  </script>
</body>
</html>
