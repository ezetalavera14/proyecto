<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Categorías - 3D-Models</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  body {
    background-color: #FAFAFA;
    font-family: 'Poppins', sans-serif;
    color: #222;
    margin: 0;
    padding: 0;
  }

  /* 🔹 Navbar */
  .navbar {
    background: linear-gradient(90deg, #CBDCEB 0%, #b6cce3 100%);
    box-shadow: 0 4px 12px rgba(109,148,197,0.35);
    padding: 0.8rem 1.5rem;
  }

  .navbar-brand {
    color: #344e87 !important;
    font-weight: 600;
    font-size: 1.25rem;
    letter-spacing: 0.5px;
    transition: transform 0.2s ease;
  }

  .navbar-brand:hover {
    transform: scale(1.05);
  }

  .navbar .nav-link {
    color: #2e3b55 !important;
    font-weight: 500;
    margin-right: 1rem;
    transition: color 0.3s ease, transform 0.2s ease;
  }

  .navbar .nav-link:hover {
    color: #4a6fa8 !important;
    transform: translateY(-2px);
  }

  /* 🔹 Submenú */
  .submenu {
    background: linear-gradient(180deg, #6D94C5 0%, #587aa4 100%);
    border-top: 2px solid #4a6b96;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
    padding: 0.5rem 0;
  }

  .submenu .nav-link {
    color: #F5EFE6 !important;
    font-weight: 500;
    transition: color 0.3s ease, transform 0.2s ease;
  }

  .submenu .nav-link:hover {
    color: #E8DFCA !important;
    transform: translateY(-2px);
    text-decoration: underline;
  }

  /* 🔹 Títulos */
  .titulo {
    color: #344e87;
    font-weight: 600;
    margin-bottom: 2rem;
    text-shadow: 0 2px 6px rgba(52, 78, 135, 0.2);
  }

  /* 🔹 Cards */
  .card {
    border: none;
    border-radius: 16px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    transition: all 0.35s ease;
    background-color: #fff;
  }

  .card:hover {
    transform: translateY(-8px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
  }

  .card img {
    height: 200px;
    object-fit: cover;
    border-radius: 16px 16px 0 0;
  }

  .card-body {
    padding: 1.2rem;
  }

  .card-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2f3d67;
  }

  .text-muted {
    color: #6b7a99 !important;
  }

  /* 🔹 Botones */
  .btn-magenta {
    background: linear-gradient(90deg, #6D94C5 0%, #4A6FA8 100%);
    border: none;
    color: #fff;
    border-radius: 8px;
    padding: 0.5rem 1.2rem;
    font-weight: 500;
    transition: all 0.3s ease;
  }

  .btn-magenta:hover {
    transform: translateY(-2px);
    background: linear-gradient(90deg, #587bb0 0%, #3c5f8a 100%);
    box-shadow: 0 4px 10px rgba(74, 111, 168, 0.3);
  }

  /* 🔹 Contenedor scrollable (para carruseles horizontales) */
  .scroll-container {
    display: flex;
    overflow-x: auto;
    gap: 1rem;
    scroll-behavior: smooth;
    padding-bottom: 0.5rem;
  }

  .scroll-container::-webkit-scrollbar {
    height: 8px;
  }

  .scroll-container::-webkit-scrollbar-thumb {
    background: #6D94C5;
    border-radius: 10px;
  }

  .scroll-container::-webkit-scrollbar-track {
    background: #e9eef5;
  }

    /* 🔹 Imagen de perfil en navbar */
  .perfil-icono-link {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .perfil-icono {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #4A6FA8;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .perfil-icono:hover {
    transform: scale(1.08);
    box-shadow: 0 0 8px rgba(74, 111, 168, 0.4);
  }

  /* 🔹 Ajuste visual para que no rompa el navbar */
  .navbar .nav-item.d-flex {
    gap: 0.5rem;
  }

  .navbar .nav-link.me-2 {
    margin-right: 0 !important;
    padding-right: 0 !important;
  }

  /* 🔹 Responsivo: avatar centrado en pantallas chicas */
  @media (max-width: 991px) {
    .navbar .nav-item.d-flex {
      justify-content: center;
      margin-top: 0.8rem;
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

  <div class="container py-5">
    <h1 class="titulo text-center mb-5">Explora Categorías</h1>
    <div class="row row-cols-1 row-cols-md-3 g-4">

      <!-- Categorías según BD -->
      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/joda.jpg" alt="Modelos 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Modelos 3D</h5>
            <p>Figuras, estatuas y personajes listos para render o impresión 3D.</p>
            <a href="../productos/index.php?categoria=1" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/industrial.jpg" alt="Diseño Industrial">
          <div class="card-body text-center">
            <h5 class="card-title">Diseño Industrial</h5>
            <p>Productos y prototipos para diseño industrial y manufactura.</p>
            <a href="../productos/index.php?categoria=2" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/arquitectuta.webp" alt="Arquitectura">
          <div class="card-body text-center">
            <h5 class="card-title">Arquitectura</h5>
            <p>Casas, edificios, interiores y urbanismo en modelado preciso.</p>
            <a href="../productos/index.php?categoria=3" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/accesorios.jpg" alt="Accesorios">
          <div class="card-body text-center">
            <h5 class="card-title">Accesorios</h5>
            <p>Armas, herramientas y gadgets para tus proyectos 3D.</p>
            <a href="../productos/index.php?categoria=4" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/fantacia.jpg" alt="Anime">
          <div class="card-body text-center">
            <h5 class="card-title">Anime</h5>
            <p>Personajes y escenas de anime listas para modelado o animación.</p>
            <a href="../productos/index.php?categoria=5" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/video-juegos.jpg" alt="Videojuegos">
          <div class="card-body text-center">
            <h5 class="card-title">Videojuegos</h5>
            <p>Modelos para juegos 3D, mapas y assets listos para Unity o Unreal.</p>
            <a href="../productos/index.php?categoria=6" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/camion.jpg" alt="Vehículos">
          <div class="card-body text-center">
            <h5 class="card-title">Vehículos</h5>
            <p>Autos, naves, motos y otros vehículos detallados en 3D.</p>
            <a href="../productos/index.php?categoria=7" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="card h-100">
          <img src="../../marketing/personajes.webp" alt="Otros">
          <div class="card-body text-center">
            <h5 class="card-title">Otros</h5>
            <p>Todo tipo de modelos que no encajan en las categorías anteriores.</p>
            <a href="../productos/index.php?categoria=8" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

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
