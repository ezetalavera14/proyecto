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
    }

    .navbar {
      background-color: #CBDCEB;
      box-shadow: 0 0 10px rgba(109,148,197,0.4);
    }

    .submenu {
      background: linear-gradient(180deg, #6D94C5 0%, #587aa4 100%);
      border-top: 2px solid #4a6b96;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
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

    .titulo {
      color: #344e87;
      font-weight: 600;
    }

    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      background-color: #fff;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }

    .card img {
      height: 180px;
      object-fit: cover;
      border-radius: 12px 12px 0 0;
    }

    .btn-magenta {
      background: linear-gradient(90deg, #6D94C5 0%, #4A6FA8 100%);
      border: none;
      color: #fff;
      border-radius: 6px;
      transition: all 0.25s ease;
    }

    .btn-magenta:hover {
      transform: translateY(-2px);
      background: linear-gradient(90deg, #587bb0 0%, #3c5f8a 100%);
    }
  </style>
</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar navbar-expand-lg px-4">
    <a class="navbar-brand titulo" href="./index.php">
      <img src="../../marketing/logo-3dmodels.png" alt="3D-Models" style="height: 40px;">
    </a>

    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="./index.php">Inicio</a></li>
        <li class="nav-item"><a class="nav-link" href="../auth/login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="../auth/registro.php">Registro</a></li>
      </ul>
    </div>
  </nav>

  <!-- SUBMENÚ -->
  <div class="submenu py-2 px-4">
    <ul class="nav justify-content-center">
      <li class="nav-item"><a href="#figuras" class="nav-link">Figuras 3D</a></li>
      <li class="nav-item"><a href="#vehiculos" class="nav-link">Vehículos</a></li>
      <li class="nav-item"><a href="#escenarios" class="nav-link">Escenarios</a></li>
      <li class="nav-item"><a href="#accesorios" class="nav-link">Accesorios</a></li>
      <li class="nav-item"><a href="#personajes" class="nav-link">Personajes</a></li>
      <li class="nav-item"><a href="#props" class="nav-link">Props y utilería</a></li>
      <li class="nav-item"><a href="#fantasia" class="nav-link">Fantasía y Sci-Fi</a></li>
      <li class="nav-item"><a href="#arquitectura" class="nav-link">Arquitectura</a></li>
    </ul>
  </div>

  <div class="container py-5">
    <h1 class="titulo text-center mb-5">Explora Categorías</h1>

    <div class="row row-cols-1 row-cols-md-3 g-4">

      <!-- FIGURAS -->
      <div class="col" id="figuras">
        <div class="card h-100">
          <img src="../../marketing/figuras.jpg" alt="Figuras 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Figuras 3D</h5>
            <p>Modelos de personajes, estatuas y héroes listos para imprimir o renderizar.</p>
            <a href="../productos/index.php?categoria=figuras" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- VEHÍCULOS -->
      <div class="col" id="vehiculos">
        <div class="card h-100">
          <img src="../../marketing/vehiculos.jpg" alt="Vehículos 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Vehículos</h5>
            <p>Autos, naves, tanques y motos creados con un nivel de detalle impresionante.</p>
            <a href="../productos/index.php?categoria=vehiculos" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- ESCENARIOS -->
      <div class="col" id="escenarios">
        <div class="card h-100">
          <img src="../../marketing/escenarios.jpg" alt="Escenarios 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Escenarios</h5>
            <p>Ambientes, mapas y fondos para videojuegos, animaciones o renders.</p>
            <a href="../productos/index.php?categoria=escenarios" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- ACCESORIOS -->
      <div class="col" id="accesorios">
        <div class="card h-100">
          <img src="../../marketing/accesorios.jpg" alt="Accesorios 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Accesorios</h5>
            <p>Armas, herramientas, gadgets y complementos para tus proyectos 3D.</p>
            <a href="../productos/index.php?categoria=accesorios" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- PERSONAJES -->
      <div class="col" id="personajes">
        <div class="card h-100">
          <img src="../../marketing/personajes.jpg" alt="Personajes 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Personajes</h5>
            <p>Humanos, criaturas, robots o héroes — da vida a tus historias.</p>
            <a href="../productos/index.php?categoria=personajes" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- PROPS -->
      <div class="col" id="props">
        <div class="card h-100">
          <img src="../../marketing/props.jpg" alt="Props 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Props y utilería</h5>
            <p>Objetos pequeños que completan escenas: muebles, herramientas, decoraciones.</p>
            <a href="../productos/index.php?categoria=props" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- FANTASÍA -->
      <div class="col" id="fantasia">
        <div class="card h-100">
          <img src="../../marketing/fantasia.jpg" alt="Fantasía y Sci-Fi">
          <div class="card-body text-center">
            <h5 class="card-title">Fantasía y Sci-Fi</h5>
            <p>Dragones, armas láser y universos imaginarios: la magia en 3D.</p>
            <a href="../productos/index.php?categoria=fantasia" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

      <!-- ARQUITECTURA -->
      <div class="col" id="arquitectura">
        <div class="card h-100">
          <img src="../../marketing/arquitectura.jpg" alt="Arquitectura 3D">
          <div class="card-body text-center">
            <h5 class="card-title">Arquitectura</h5>
            <p>Casas, edificios, interiores y urbanismo en modelado preciso.</p>
            <a href="../productos/index.php?categoria=arquitectura" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- FOOTER -->
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
