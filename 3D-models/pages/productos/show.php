<?php
include("../../conexion.php");

if (!isset($_GET['id'])) {
  echo "<script>alert('Producto no especificado'); window.location.href='index.php';</script>";
  exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM productos WHERE id = $id";
$resultado = mysqli_query($enlace, $sql);

if (mysqli_num_rows($resultado) === 0) {
  echo "<script>alert('Producto no encontrado'); window.location.href='index.php';</script>";
  exit;
}

$producto = mysqli_fetch_assoc($resultado);

// Buscar una imagen asociada
$sql_img = "SELECT ruta FROM imagenes WHERE producto_id = $id LIMIT 1";
$res_img = mysqli_query($enlace, $sql_img);
$img = mysqli_fetch_assoc($res_img);

// Si no hay imagen, usar imagen por defecto
$ruta_imagen = $img ? "../../" . $img['ruta'] : '../../imagenes/default.jpg';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($producto['titulo']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #000;
      color: #fff;
    }

    .producto-container {
      max-width: 800px;
      margin: 50px auto;
      padding: 20px;
      background-color: #111;
      border-radius: 10px;
      box-shadow: 0 0 12px #ff00ff88;
    }

    img {
      max-width: 100%;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .btn-back {
      background-color: #ff00ff;
      color: #000;
      border: none;
    }

    .btn-back:hover {
      background-color: #cc00cc;
    }
  </style>
</head>
<body>
  <div class="producto-container text-center">
    <img src="<?php echo $ruta_imagen; ?>" alt="Imagen del producto">
    <h2><?php echo htmlspecialchars($producto['titulo']); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($producto['descripcion'])); ?></p>
    <h4>Precio: $<?php echo number_format($producto['precio'], 2); ?></h4>
    <a href="index.php" class="btn btn-back mt-3">Volver al inicio</a>
  </div>
</body>
</html>
