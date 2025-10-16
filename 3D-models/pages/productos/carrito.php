<?php
session_start();
include("../../conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

// Si viene un producto para agregar al carrito
if (isset($_GET['agregar'])) {
    $producto_id = intval($_GET['agregar']);

    // Verificar si ya existe en el carrito
    $check = mysqli_query($enlace, "SELECT * FROM carrito WHERE usuario_id = $usuario_id AND producto_id = $producto_id");
    if (!$check) die("Error al verificar carrito: " . mysqli_error($enlace));

    if (mysqli_num_rows($check) > 0) {
        $update = mysqli_query($enlace, "UPDATE carrito SET cantidad = cantidad + 1 WHERE usuario_id = $usuario_id AND producto_id = $producto_id");
        if (!$update) die("Error al actualizar carrito: " . mysqli_error($enlace));
    } else {
        $insert = mysqli_query($enlace, "INSERT INTO carrito (usuario_id, producto_id, cantidad, agregado_en) VALUES ($usuario_id, $producto_id, 1, NOW())");
        if (!$insert) die("Error al agregar al carrito: " . mysqli_error($enlace));
    }

    // Redirigir para evitar reenvío de formulario
    header("Location: carrito.php");
    exit();
}

// Consulta para obtener los productos del carrito del usuario
$sql = "SELECT c.id AS carrito_id, p.id AS producto_id, p.titulo, p.precio, c.cantidad, i.ruta 
        FROM carrito c
        JOIN productos p ON c.producto_id = p.id
        LEFT JOIN imagenes i ON p.id = i.producto_id
        WHERE c.usuario_id = $usuario_id";

$resultado = mysqli_query($enlace, $sql);
if (!$resultado) die("Error al consultar el carrito: " . mysqli_error($enlace));
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mi Carrito</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    background-color: #F5EFE6;
    color: #333;
    font-family: 'Segoe UI', sans-serif;
  }

  .card {
    background-color: #CBDCEB;
    border: 1px solid #6D94C5;
    color: #333;
    border-radius: 8px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(109, 148, 197, 0.3);
  }

  .btn-magenta {
    background-color: #6D94C5;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
    font-weight: 500;
  }

  .btn-magenta:hover {
    background-color: #5c7fb0;
  }

  .btn-success {
    background-color: #E8DFCA;
    color: #333;
    border: 1px solid #cbbf9d;
    padding: 8px 16px;
    border-radius: 5px;
  }

  .btn-success:hover {
    background-color: #dcd2b5;
  }

  .btn-danger {
    background-color: #dc3545;
    color: #fff;
    border: none;
    padding: 8px 16px;
    border-radius: 5px;
  }

  .btn-danger:hover {
    background-color: #c82333;
  }

  .navbar {
    background-color: #CBDCEB;
    box-shadow: 0 0 10px rgba(109, 148, 197, 0.3);
  }

  .titulo {
    color: #6D94C5;
    font-weight: bold;
    font-size: 1.5rem;
  }

  a.nav-link,
  .navbar-brand {
    color: #333 !important;
  }

  a.nav-link:hover {
    color: #6D94C5 !important;
  }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg px-4">
    <a class="navbar-brand titulo" href="index.php">3D-Models</a>
  </nav>

  <div class="container py-5">
    <h1 class="titulo text-center mb-5">Mi Carrito</h1>

    <?php if (mysqli_num_rows($resultado) == 0): ?>
        <p class="text-center">Tu carrito está vacío. <a href="index.php" class="btn btn-magenta">Volver a la tienda</a></p>
    <?php else: ?>
      <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php
        $total = 0;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $subtotal = $fila['precio'] * $fila['cantidad'];
            $total += $subtotal;
            $imagen = $fila['ruta'] ? "../../" . $fila['ruta'] : '../../imagenes/default.jpg';
        ?>
        <div class="col">
          <div class="card h-100">
            <img src="<?php echo $imagen; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($fila['titulo']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo htmlspecialchars($fila['titulo']); ?></h5>
              <p class="card-text">Precio: $<?php echo $fila['precio']; ?></p>
              <p class="card-text">Cantidad: <?php echo $fila['cantidad']; ?></p>
              <p class="card-text">Subtotal: $<?php echo $subtotal; ?></p>
              <a href="eliminar_carrito.php?id=<?php echo $fila['carrito_id']; ?>" class="btn btn-danger">Eliminar</a>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>

      <div class="mt-4 text-center">
        <h3>Total: $<?php echo $total; ?></h3>
        <a href="pagos.php" class="btn btn-success btn-lg mt-2">Proceder al Pago</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
