<?php
session_start();
include("../../conexion.php");

$busqueda = '';
if (isset($_GET['buscar']) && !empty(trim($_GET['buscar']))) {
  $busqueda = mysqli_real_escape_string($enlace, $_GET['buscar']);
  $consulta = "SELECT * FROM productos WHERE titulo LIKE '%$busqueda%' ORDER BY id DESC";
} else {
  $consulta = "SELECT * FROM productos ORDER BY id DESC";
}

$resultado = mysqli_query($enlace, $consulta);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>MiTienda - Inicio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #000;
      color: #fff;
    }

    .card {
      background-color: #111;
      border: 1px solid #444;
      color: #fff;
    }

    .btn-magenta {
      background-color: #ff00ff;
      color: #000;
    }

    .btn-magenta:hover {
      background-color: #cc00cc;
    }

    .btn-success {
      background-color: #28a745;
      color: #fff;
    }

    .btn-success:hover {
      background-color: #218838;
    }

    .btn-danger {
      background-color: #dc3545;
      color: #fff;
    }

    .btn-danger:hover {
      background-color: #c82333;
    }

    .navbar {
      background-color: #111;
      box-shadow: 0 0 10px #ff00ff55;
    }

    .titulo {
      color: #ff00ff;
    }

    .resaltar {
      background-color: #ff00ff33;
      padding: 2px 4px;
      border-radius: 3px;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg px-4">
    <a class="navbar-brand titulo" href="./index.php">3D-Models</a>

    <!-- Barra de búsqueda -->
    <form class="d-flex mx-3" method="GET" action="">
      <input class="form-control me-2" type="search" name="buscar" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
      <button class="btn btn-magenta" type="submit">Buscar</button>
    </form>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['usuario'])): ?>
          <li class="nav-item">
            <span class="nav-link text-white">Hola, <?php echo $_SESSION['usuario']['nombre']; ?></span>
          </li>
          <?php if ($_SESSION['usuario']['tipo_usuario'] === 'vendedor'): ?>
            <li class="nav-item">
              <a href="../productos/create.php" class="btn btn-success mx-2">Subir producto</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link text-white" href="../auth/cerrar_sesion.php">Cerrar sesión</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-white" href="../auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../auth/registro.php">Registro</a></li>
          <li class="nav-item"><a class="nav-link text-white" href="../auth/registro_vendedor.php">Vendedor</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>

  <div class="container py-5">
    <h1 class="titulo text-center mb-5">Productos disponibles</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">
      <?php while ($prod = mysqli_fetch_assoc($resultado)) {
        $id_producto = $prod['id'];
        $consulta_img = "SELECT ruta FROM imagenes WHERE producto_id = $id_producto LIMIT 1";
        $res_img = mysqli_query($enlace, $consulta_img);
        $img = mysqli_fetch_assoc($res_img);
        $ruta_imagen = $img ? "../../" . $img['ruta'] : '../../imagenes/default.jpg'; // Ruta por defecto

        // Resaltar búsqueda en el título si hay búsqueda
        $titulo = htmlspecialchars($prod['titulo']);
        if (!empty($busqueda)) {
          $titulo = preg_replace("/($busqueda)/i", '<span class="resaltar">$1</span>', $titulo);
        }
      ?>
        <div class="col">
          <div class="card h-100">
            <img src="<?php echo $ruta_imagen; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($prod['titulo']); ?>">
            <div class="card-body">
              <h5 class="card-title"><?php echo $titulo; ?></h5>
              <p class="card-text"><?php echo substr($prod['descripcion'], 0, 60) . '...'; ?></p>
              <a href="show.php?id=<?php echo $prod['id']; ?>" class="btn btn-magenta mb-2">Ver más</a>

              <?php if (isset($_SESSION['usuario'])): ?>                
                <a href="pago.php?id=<?php echo $prod['id']; ?>" class="btn btn-magenta mb-2">Comprar ahora</a>
                <a href="carrito.php?agregar=<?php echo $prod['id']; ?>" class="btn btn-success mb-2">🛒</a>
              <?php else: ?>
                <a href="../auth/login.php" class="btn btn-magenta mb-2">Comprar ahora</a>
                <a href="../auth/login.php" class="btn btn-success mb-2">🛒</a>
              <?php endif; ?>

              <!-- Botones Editar y Eliminar sólo si el usuario es vendedor y dueño del producto -->
              <?php if (
                isset($_SESSION['usuario']) &&
                $_SESSION['usuario']['tipo_usuario'] === 'vendedor' &&
                $_SESSION['usuario']['id'] === $prod['vendedor_id']  
              ): ?>
                <a href="edit.php?id=<?php echo $prod['id']; ?>" class="btn btn-success mb-2">Editar</a>
                <a href="delete.php?id=<?php echo $prod['id']; ?>" class="btn btn-danger mb-2" onclick="return confirm('¿Seguro querés eliminar este producto?');">Eliminar</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>
  </div>
</body>

</html>
