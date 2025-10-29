<?php
session_start();
include("../../conexion.php");

// Inicializar variable de búsqueda (para el input del navbar)
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';

// Consulta de productos más vistos (top 5) y traer su primera imagen
$consulta_mas_buscados = "
  SELECT p.*, 
         (SELECT ruta FROM imagenes WHERE producto_id = p.id LIMIT 1) AS imagen_ruta
  FROM productos p
  ORDER BY p.visitas DESC
  LIMIT 5
";

$resultado_mas_buscados = mysqli_query($enlace, $consulta_mas_buscados);
if (!$resultado_mas_buscados) {
    die("Error en la consulta: " . mysqli_error($enlace));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Populares - 3D-Models</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
/* ======= CONFIGURACIÓN GENERAL ======= */
body {
  background-color: #FAFAFA;
  font-family: 'Poppins', sans-serif;
  color: #222;
}

h1, h2, h5 {
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

/* ======= ICONO DE PERFIL ======= */
.perfil-icono-link {
  display: inline-block;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid #4A6FA8;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.perfil-icono-link:hover {
  transform: scale(1.05);
  box-shadow: 0 0 8px rgba(74, 111, 168, 0.4);
}

.perfil-icono {
  width: 100%;
  height: 100%;
  object-fit: cover;
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
.card img {
  height: 180px;
  object-fit: cover;
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

  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg px-4">
    <div class="container-fluid">
      <a class="navbar-brand titulo" href="./index.php">
        <img src="../../marketing/logo-3dmodels.png" alt="3D-Models" style="height: 40px;">
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContenido">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarContenido">

        <form class="d-flex mx-auto my-3 my-lg-0" method="GET" action="">
          <input class="form-control me-2" type="search" name="buscar" placeholder="Buscar producto..." value="<?php echo htmlspecialchars($busqueda); ?>">
          <button class="btn btn-magenta" type="submit">Buscar</button>
        </form>

        <ul class="navbar-nav ms-auto align-items-lg-center">
          <?php if (isset($_SESSION['usuario'])): ?>
            <?php if ($_SESSION['usuario']['tipo_usuario'] === 'vendedor'): ?>
              <li class="nav-item">
                <a href="../productos/create.php" class="btn btn-success mx-2 my-2 my-lg-0">Subir producto</a>
              </li>
            <?php endif; ?>

            <li class="nav-item">
              <a class="nav-link" href="../auth/cerrar_sesion.php">Cerrar sesión</a>
            </li>

            <li class="nav-item d-flex align-items-center justify-content-center mt-3 mt-lg-0">
              <span class="nav-link me-2">Hola, <?php echo $_SESSION['usuario']['nombre']; ?></span>
              <a href="../usuarios/perfil.php" class="perfil-icono-link">
                <img src="<?php echo isset($_SESSION['usuario']['foto']) 
                  ? '../../' . $_SESSION['usuario']['foto'] 
                  : '../../assets/img/default-profile.png'; ?>" 
                  alt="Perfil" class="perfil-icono">
              </a>
            </li>

          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="../auth/login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="../auth/registro.php">Registro</a></li>
            <li class="nav-item"><a class="nav-link" href="../auth/registro_vendedor.php">Vendedor</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- SUBMENÚ -->
  <div class="submenu py-2 px-4">
    <ul class="nav justify-content-center">
      <li class="nav-item"><a href="categorias.php" class="nav-link">Categorías</a></li>
      <li class="nav-item"><a href="#" class="nav-link">Ofertas</a></li>
      <li class="nav-item"><a href="#" class="nav-link">Nuevos modelos</a></li>
      <li class="nav-item"><a href="populares.php" class="nav-link">Populares</a></li>
    </ul>
  </div>

  <!-- CONTENIDO -->
  <div class="container py-5">
    <h2 class="titulo text-center mb-5">Más Buscados</h2>

    <div class="d-flex overflow-auto mb-5" style="gap: 1rem;">
      <?php while ($p = mysqli_fetch_assoc($resultado_mas_buscados)): 
        $ruta = $p['imagen_ruta'] ? "../../" . $p['imagen_ruta'] : "../../imagenes/default.jpg";
      ?>
        <div class="card" style="min-width: 250px;">
          <img src="<?php echo htmlspecialchars($ruta); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($p['titulo']); ?>">
          <div class="card-body text-center">
            <h5 class="card-title"><?php echo htmlspecialchars($p['titulo']); ?></h5>
            <p class="text-muted small">Visitas: <?php echo intval($p['visitas']); ?></p>
            <a href="show.php?id=<?php echo $p['id']; ?>" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>

    <div class="text-center mb-4">
      <a href="./index.php" class="btn btn-success">Volver al inicio</a>
    </div>
  </div>

    <footer class="footer py-5 mt-5">
    <div class="container">
      <hr style="border-color: #6D94C5;">
      <div class="text-center">
        <small>&copy; 2025 3D-Models. Todos los derechos reservados.</small>
      </div>
    </div>
  </footer>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
