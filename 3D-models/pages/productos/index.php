<?php
session_start();
include("../../conexion.php");

// Inicializar variables
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
$titulo_pagina = "Productos Disponibles"; // Título por defecto

// Base de la consulta
$consulta = "
    SELECT 
        p.*, 
        u.nombre AS vendedor_nombre, 
        u.foto AS vendedor_foto, 
        c.nombre AS categoria_nombre
    FROM productos p
    INNER JOIN usuarios u ON p.vendedor_id = u.id
    LEFT JOIN categorias c ON p.categoria_id = c.id
";

// FILTROS
$condiciones = [];

//  Filtrar por búsqueda
if (!empty($busqueda)) {
    $busqueda = mysqli_real_escape_string($enlace, $busqueda);
    $condiciones[] = "p.titulo LIKE '%$busqueda%'";
    $titulo_pagina = "Resultados para: '$busqueda'";
}

//  Filtrar por categoría
if (!empty($categoria_id)) {
    $condiciones[] = "c.id = $categoria_id";

    // Obtener nombre de categoría para el título
    $cat_query = mysqli_query($enlace, "SELECT nombre FROM categorias WHERE id = $categoria_id LIMIT 1");
    if ($cat_query && mysqli_num_rows($cat_query) > 0) {
        $cat_data = mysqli_fetch_assoc($cat_query);
        $titulo_pagina = "Categoría: " . htmlspecialchars($cat_data['nombre']);
    } else {
        $titulo_pagina = "Categoría no encontrada";
    }
}

// Unir condiciones si hay filtros
if (!empty($condiciones)) {
    $consulta .= " WHERE " . implode(" AND ", $condiciones);
}

$consulta .= " ORDER BY p.id DESC";

// Ejecutar consulta
$resultado = mysqli_query($enlace, $consulta);
if (!$resultado) {
    die("Error en la consulta: " . mysqli_error($enlace));
}

// Manejo de likes
if (isset($_POST['toggle_like'])) {
    if (isset($_SESSION['usuario'])) {
        $usuario_id = intval($_SESSION['usuario']['id']);
        $producto_id = intval($_POST['producto_id']);

        $check = mysqli_query($enlace, "SELECT * FROM likes WHERE usuario_id = $usuario_id AND producto_id = $producto_id");
        if (mysqli_num_rows($check) > 0) {
            mysqli_query($enlace, "DELETE FROM likes WHERE usuario_id = $usuario_id AND producto_id = $producto_id");
        } else {
            mysqli_query($enlace, "INSERT INTO likes (usuario_id, producto_id) VALUES ($usuario_id, $producto_id)");
        }
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>MiTienda - Inicio</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
/* ======= CONFIGURACIÓN GENERAL ======= */
body {
  background-color: #FAFAFA;
  font-family: 'Poppins', sans-serif;
  color: #222;
}

h1, h5 {
  font-weight: 600;
  letter-spacing: 0.3px;
}

/* ======= NAVBAR ======= */
.navbar {
  background-color: #CBDCEB;
  box-shadow: 0 0 10px rgba(109, 148, 197, 0.4);
}

.titulo {
  color: #344e87;
}

a.nav-link, .navbar-brand {
  color: #333 !important;
}

a.nav-link:hover {
  color: #6D94C5 !important;
}

/* ======= BOTÓN HAMBURGUESA ======= */
.navbar-toggler {
  border-color: #4A6FA8;
}
.navbar-toggler-icon {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3E%3Cpath stroke='rgba(74,111,168,0.9)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
}

/* ======= INPUTS ======= */
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

/* ======= BOTONES ======= */
.btn-magenta {
  background: linear-gradient(90deg, #6D94C5 0%, #4A6FA8 100%);
  border: none;
  color: #fff;
  font-weight: 500;
  border-radius: 6px;
  transition: all 0.25s ease;
}
.btn-magenta:hover {
  transform: translateY(-2px);
  background: linear-gradient(90deg, #587bb0 0%, #3c5f8a 100%);
}

.btn-success {
  background-color: #E8DFCA;
  border: none;
  color: #333;
  font-weight: 500;
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

/* ======= CARDS ======= */
.card {
  background: #fff;
  border: none;
  border-radius: 12px;
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}
.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}
.card-img-top {
  height: 260px;
  object-fit: cover;
}
.card-body {
  padding: 1.3rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.precio {
  font-size: 1.3rem;
  font-weight: 600;
  color: #2b2d42;
}
.badge-oferta {
  position: absolute;
  top: 10px;
  left: 10px;
  background-color: #e63946;
  color: #fff;
  font-size: 0.8rem;
  font-weight: 500;
  padding: 4px 8px;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(230, 57, 70, 0.3);
}
.card .carousel-item img {
  height: 260px;
  object-fit: cover;
  border-bottom: 1px solid #eee;
}

/* ======= SUBMENÚ ======= */
.submenu {
  background: linear-gradient(180deg, #6D94C5 0%, #587aa4 100%);
  border-top: 2px solid #4a6b96;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
.submenu .nav-link {
  color: #F5EFE6 !important;
  font-weight: 500;
  transition: color 0.3s, transform 0.2s;
}
.submenu .nav-link:hover {
  color: #E8DFCA !important;
  transform: translateY(-2px);
  text-decoration: underline;
}

/* ======= PERFIL USUARIO ======= */
.perfil-icono-link {
  display: inline-block;
  width: 42px;
  height: 42px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #fff;
  transition: 0.3s;
}
.perfil-icono-link:hover {
  transform: scale(1.1);
}
.perfil-icono {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* ======= RESPONSIVIDAD ======= */
@media (max-width: 992px) {
  /* Navbar */
  .navbar-collapse {
    background-color: #CBDCEB;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  }
  .navbar-nav .nav-item {
    margin-bottom: 0.5rem;
  }
  .perfil-icono-link {
    width: 50px;
    height: 50px;
  }

  /* Búsqueda centrada en móvil */
  form.d-flex {
    flex-direction: column;
    align-items: stretch;
    gap: 0.5rem;
    width: 100%;
  }
  .btn-magenta {
    width: 100%;
  }
}

@media (max-width: 768px) {
  /* Cards */
  .card {
    width: 100%;
  }
  .carousel-item img {
    height: 300px;
  }

  /* Submenú con scroll horizontal */
  .submenu .nav {
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: none;
  }
  .submenu .nav::-webkit-scrollbar {
    display: none;
  }
}
</style>

</head>

<body>
  <nav class="navbar navbar-expand-lg px-4">
  <div class="container-fluid">
    <!-- Logo -->
    <a class="navbar-brand titulo" href="./index.php">
      <img src="../.././marketing/logo-3dmodels.png" alt="3D-Models" style="height: 40px;">
    </a>

    <!-- Botón hamburguesa -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido" aria-controls="navbarContenido" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Contenido del navbar -->
    <div class="collapse navbar-collapse" id="navbarContenido">

      <!-- Barra de búsqueda -->
      <form class="d-flex mx-auto my-3 my-lg-0" method="GET" action="">
        <input class="form-control me-2" type="search" name="buscar" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
        <button class="btn btn-magenta" type="submit">Buscar</button>
      </form>

      <!-- Opciones de usuario -->
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <?php if (isset($_SESSION['usuario'])): ?>
          
          <?php if ($_SESSION['usuario']['tipo_usuario'] === 'vendedor'): ?>
            <li class="nav-item">
              <a href="../productos/create.php" class="btn btn-success mx-2 my-2 my-lg-0 w-100 w-lg-auto">Subir producto</a>
            </li>
          <?php endif; ?>

          <li class="nav-item">
            <a class="nav-link text-white text-center" href="../auth/cerrar_sesion.php">Cerrar sesión</a>
          </li>

          <li class="nav-item d-flex align-items-center justify-content-center mt-3 mt-lg-0">
            <span class="nav-link text-white me-2 text-center">Hola, <?php echo $_SESSION['usuario']['nombre']; ?></span>

            <!-- Icono de perfil -->
            <a href="../usuarios/perfil.php" class="perfil-icono-link">
              <img src="<?php echo isset($_SESSION['usuario']['foto']) 
                ? '../../' . $_SESSION['usuario']['foto'] 
                : '../../assets/img/default-profile.png'; ?>" 
                alt="Perfil" class="perfil-icono">
            </a>
          </li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-white text-center" href="../auth/login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link text-white text-center" href="../auth/registro.php">Registro</a></li>
          <li class="nav-item"><a class="nav-link text-white text-center" href="../auth/registro_vendedor.php">Vendedor</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>


  <!-- SUBMENÚ -->
<div class="submenu py-2 px-4">
  <ul class="nav justify-content-center">
    <li class="nav-item"><a href="categorias.php" class="nav-link text-white">Categorías</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white">Ofertas</a></li>
    <li class="nav-item"><a href="#" class="nav-link text-white">Nuevos modelos</a></li>
    <li class="nav-item"><a href="populares.php" class="nav-link text-white">Populares</a></li>
  </ul>
</div>


  <?php if (empty($busqueda)): // Solo mostrar carrusel si NO hay búsqueda ?>
<div id="carouselDestacados" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="../../marketing/3D-CULTS_PROMO_-_ENG.webp" class="d-block w-100" alt="Banner 1">
    </div>
    <div class="carousel-item">
      <img src="../../marketing/kakashi.png" class="d-block w-100" alt="Banner 2">
    </div>
    <div class="carousel-item">
      <img src="../../marketing/5f81d6f1-c719-406b-9c07-59110415e919.png" class="d-block w-100" alt="Banner 3">
    </div>
  </div>

  <button class="carousel-control-prev" type="button" data-bs-target="#carouselDestacados" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Anterior</span>
  </button>

  <button class="carousel-control-next" type="button" data-bs-target="#carouselDestacados" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Siguiente</span>
  </button>

  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselDestacados" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselDestacados" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselDestacados" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
</div>
<?php endif; ?>


<div class="container py-5">
  <h1 class="titulo text-center mb-5"><?php echo $titulo_pagina; ?></h1>
  <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">


    <?php while ($prod = mysqli_fetch_assoc($resultado)): 
  $id_producto = $prod['id'];

  // Obtener todas las imágenes del producto
$consulta_img = "SELECT ruta FROM imagenes WHERE producto_id = $id_producto";
$res_img = mysqli_query($enlace, $consulta_img);

$imagenes = [];
while ($img = mysqli_fetch_assoc($res_img)) {
  $imagenes[] = "../../" . $img['ruta'];
}

// Si no hay imágenes, usar una por defecto
if (empty($imagenes)) {
  $imagenes[] = "../../imagenes/default.jpg";
}


  //  Datos del vendedor (ya vienen del JOIN)
  $nombre_vendedor = htmlspecialchars($prod['vendedor_nombre'] ?? 'Vendedor desconocido');
  $foto_vendedor = !empty($prod['vendedor_foto']) ? "../../" . $prod['vendedor_foto'] : "../../assets/img/default-profile.png";
  $vendedor_id = intval($prod['vendedor_id']);

  //  Resaltar búsqueda en el título
  $titulo = htmlspecialchars($prod['titulo']);
  if (!empty($busqueda)) {
    $titulo = preg_replace("/(" . preg_quote($busqueda, '/') . ")/i", '<span class="resaltar">$1</span>', $titulo);
  }
?>

    
      <div class="col">
        <div class="card h-100">
          <div id="carousel-<?php echo $id_producto; ?>" class="carousel slide" data-bs-ride="carousel">
  <div class="carousel-inner">
    <?php foreach ($imagenes as $index => $imgRuta): ?>
      <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
        <img src="<?php echo $imgRuta; ?>" class="d-block w-100 card-img-top" alt="Imagen del producto <?php echo htmlspecialchars($prod['titulo']); ?>">
      </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($imagenes) > 1): ?>
    <button class="carousel-control-prev" type="button" data-bs-target="#carousel-<?php echo $id_producto; ?>" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carousel-<?php echo $id_producto; ?>" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Siguiente</span>
    </button>
  <?php endif; ?>
</div>



          <div class="card-body">

            <!--  Información del vendedor -->
            <div class="d-flex align-items-center mb-3" style="gap:10px;">
              <a href="../usuarios/perfil.php?id=<?php echo $vendedor_id; ?>" class="perfil-icono-link">
                <img src="<?php echo $foto_vendedor; ?>" alt="Foto vendedor" class="rounded-circle" width="40" height="40" style="object-fit:cover; border:2px solid #ccc;">
              </a>
              <div>
                <strong><?php echo $nombre_vendedor; ?></strong><br>

                <?php if (isset($_SESSION['usuario']) && $_SESSION['usuario']['id'] != $vendedor_id): 
                  $seguidor_id = $_SESSION['usuario']['id'];
                  $checkFollow = mysqli_query($enlace, "SELECT id FROM seguidores WHERE seguidor_id=$seguidor_id AND seguido_id=$vendedor_id");
                  $yaSigue = mysqli_num_rows($checkFollow) > 0;
                ?>
                  <form method="POST" action=".././usuarios/seguir.php" style="display:inline;">
                    <input type="hidden" name="seguidor_id" value="<?php echo $seguidor_id; ?>">
                    <input type="hidden" name="seguido_id" value="<?php echo $vendedor_id; ?>">
                    <button type="submit" name="toggle_follow" class="btn btn-sm <?php echo $yaSigue ? 'btn-secondary' : 'btn-magenta'; ?>">
                      <?php echo $yaSigue ? 'Siguiendo' : 'Follow'; ?>
                    </button>
                  </form>
                <?php endif; ?>
              </div>
            </div>

            <!--  Información del producto -->
            <h5 class="card-title"><?php echo $titulo; ?></h5>
            <p class="card-text"><?php echo substr($prod['descripcion'], 0, 60) . '...'; ?></p>

            <a href="show.php?id=<?php echo $prod['id']; ?>" class="btn btn-magenta mb-2">Ver más</a>

            <?php if (isset($_SESSION['usuario'])):  
              $es_vendedor_dueño = $_SESSION['usuario']['tipo_usuario'] === 'vendedor' && 
                                  $_SESSION['usuario']['id'] === $prod['vendedor_id'];
              if (!$es_vendedor_dueño): 
            ?>
              <a href="pagos.php?id=<?php echo $prod['id']; ?>" class="btn btn-magenta mb-2">Comprar ahora</a>
              <a href="carrito.php?agregar=<?php echo $prod['id']; ?>" class="btn btn-success mb-2">🛒</a>

              <!-- Botón Like  -->
              <?php 
                $usuario_id = $_SESSION['usuario']['id'];
                $likeCheck = mysqli_query($enlace, "SELECT id FROM likes WHERE usuario_id=$usuario_id AND producto_id=".$prod['id']);
                $liked = mysqli_num_rows($likeCheck) > 0;

                $likeCountRes = mysqli_query($enlace, "SELECT COUNT(*) as total FROM likes WHERE producto_id=".$prod['id']);
                $totalLikes = mysqli_fetch_assoc($likeCountRes)['total'];
              ?>
              <form method="POST" style="display:inline;">
                <input type="hidden" name="producto_id" value="<?php echo $prod['id']; ?>">
                <button type="submit" name="toggle_like" class="btn btn-magenta" style="display:flex; align-items:center; gap:5px;">
                  <span style="color: <?php echo $liked ? '#e63946' : '#fff'; ?>;">❤️</span>
                  <span><?php echo $totalLikes; ?></span>
                </button>
              </form>
            <?php endif; ?>
            <?php else: ?>
              <a href="../auth/login.php" class="btn btn-magenta mb-2">Comprar ahora</a>
              <a href="../auth/login.php" class="btn btn-success mb-2">🛒</a>
            <?php endif; ?>

            <!--  Botones Editar / Eliminar (solo del dueño) -->
            <?php if (isset($_SESSION['usuario']) &&
                      $_SESSION['usuario']['tipo_usuario'] === 'vendedor' &&
                      $_SESSION['usuario']['id'] === $prod['vendedor_id']): ?>
              <a href="edit.php?id=<?php echo $prod['id']; ?>" class="btn btn-success mb-2">Editar</a>
              <a href="delete.php?id=<?php echo $prod['id']; ?>" class="btn btn-danger mb-2" onclick="return confirm('¿Seguro querés eliminar este producto?');">Eliminar</a>
            <?php endif; ?>

          </div>
        </div>
      </div>
    <?php endwhile; ?>

  </div>
</div>



<!-- FOOTER -->
<footer class="footer py-5 mt-5">
  <div class="container">
    <hr style="border-color: #6D94C5;">
    <div class="row">

      <!-- Información de la empresa -->
      <div class="col-md-3 mb-4">
        <h5>3D-Models</h5>
        <p>Tu tienda de modelos 3D favoritos. Encuentra figuras de acción, accesorios y mucho más.</p>
      </div>

      <!-- Contacto -->
      <div class="col-md-3 mb-4">
        <h5>Contacto</h5>
        <ul class="list-unstyled">
          <li>📧 info@3d-models.com</li>
          <li>📞 +54 11 3616-7707</li>
          <li>🏢 Calle 19 & Calle 111, Buenos Aires</li>
        </ul>
      </div>

      <!-- Enlaces útiles -->
      <div class="col-md-3 mb-4">
        <h5>Enlaces</h5>
        <ul class="list-unstyled">
          <li><a href="index.php">Inicio</a></li>
          <li><a href=".././auth/login.php">login</a></li>
          <li><a href="categorias.php">Categorías</a></li>
          <li><a href="#">Contacto</a></li>
        </ul>
      </div>

      <!-- Redes sociales -->
      <div class="col-md-3 mb-4">
        <h5>Síguenos</h5>
        <div class="d-flex gap-3">
          <a href="#"><img src="../../marketing/facebook-logo.png" alt="Facebook" width="30"></a>
          <a href="#"><img src="../../marketing/instagram.jpg" alt="Instagram" width="30"></a>
          <a href="#"><img src="../../marketing/twiter.jpg" alt="Twitter" width="30"></a>
          <a href="#"><img src="../../marketing/whatsapp.png" alt="WhatsApp" width="30"></a>
        </div>
      </div>

    </div>

    <hr style="border-color: #6D94C5;">

    <div class="text-center mt-3">
      <small>&copy; 2025 3D-Models. Todos los derechos reservados.</small>
    </div>
  </div>
</footer>

                    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
