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
    background-color: #F5EFE6;
    color: #333; /* texto oscuro para contraste */
    font-family: 'Segoe UI', sans-serif;
  }

  .card {
    background-color: #CBDCEB;
    border: 1px solid #6D94C5;
    color: #000;
    border-radius: 8px;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 12px rgba(109, 148, 197, 0.4);
  }

  .btn-magenta {
    background-color: #6D94C5;
    color: #fff;
    border: none;
  }

  .btn-magenta:hover {
    background-color: #5c7fb0;
  }

  .btn-success {
    background-color: #E8DFCA;
    color: #333;
    border: 1px solid #cbbf9d;
  }

  .btn-success:hover {
    background-color: #dcd2b5;
  }

  .btn-danger {
    background-color: #dc3545;
    color: #fff;
    border: none;
  }

  .btn-danger:hover {
    background-color: #c82333;
  }

  .navbar {
    background-color: #CBDCEB;
    box-shadow: 0 0 10px rgba(109, 148, 197, 0.4);
  }

  .titulo {
    color: #6D94C5;
    font-weight: bold;
  }

  .resaltar {
    background-color: #E8DFCA;
    padding: 2px 6px;
    border-radius: 4px;
  }

  a.nav-link, .navbar-brand {
    color: #333 !important;
  }

  a.nav-link:hover {
    color: #6D94C5 !important;
  }

  input.form-control {
    border: 1px solid #6D94C5;
    background-color: #F5EFE6;
    color: #333;
  }

  input.form-control::placeholder {
    color: #aaa;
  }

  input.form-control:focus {
    border-color: #6D94C5;
    box-shadow: 0 0 5px rgba(109, 148, 197, 0.5);
  }

  .carousel-item img {
  object-fit: cover;
  height: 600px; 
}

/* SUBMENÚ */
.submenu {
   background: linear-gradient(180deg, #6D94C5 0%, #587aa4 100%); /* tono más oscuro que el navbar */
  border-top: 2px solid #4a6b96; /* sutil línea separadora */
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2); /* da sensación de capa */
}

.submenu .nav-link {
  color: #F5EFE6 !important; /* texto claro sobre fondo oscuro */
  font-weight: 500;
  transition: color 0.3s, transform 0.2s;
}

.submenu .nav-link:hover {
  color: #E8DFCA !important; /* beige suave al pasar el mouse */
  transform: translateY(-2px);
  text-decoration: underline;
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

  <!-- SUBMENÚ -->
<div class="submenu py-2 px-4">
  <ul class="nav justify-content-center">
    <li class="nav-item"><a href="#" class="nav-link text-white">Categorías</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white">Ofertas</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white">Nuevos modelos</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white">Populares</a></li>
  </ul>
</div>


  <div id="carouselDestacados" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">

    <div class="carousel-item active">
      <img src="../../marketing/naruto20.png" class="d-block w-100" alt="Banner 1">
    </div>
    <div class="carousel-item">
      <img src="../../marketing/kakashi.png" class="d-block w-100" alt="Banner 2">
    </div>
    <div class="carousel-item">
      <img src="../../marketing/5f81d6f1-c719-406b-9c07-59110415e919.png" class="d-block w-100" alt="Banner 3">
    </div>
  </div>

  <!-- Controles (anterior / siguiente) -->
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselDestacados" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Anterior</span>
  </button>

  <button class="carousel-control-next" type="button" data-bs-target="#carouselDestacados" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Siguiente</span>
  </button>

  <!-- Indicadores (puntos debajo del carrusel) -->
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselDestacados" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselDestacados" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselDestacados" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
</div>






  <div class="container py-5">
    <h1 class="titulo text-center mb-5">Productos Disponibles</h1>
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
              <?php 
                $es_vendedor_dueño = $_SESSION['usuario']['tipo_usuario'] === 'vendedor' && 
                $_SESSION['usuario']['id'] === $prod['vendedor_id'];
                if (!$es_vendedor_dueño): 
              ?>
                <a href="pagos.php?id=<?php echo $prod['id']; ?>" class="btn btn-magenta mb-2">Comprar ahora</a>
                <a href="carrito.php?agregar=<?php echo $prod['id']; ?>" class="btn btn-success mb-2">🛒</a>
              <?php endif; ?>
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
