<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['usuario'])) {
  header("Location: ../auth/login.php");
  exit();
}

//  Datos del usuario logueado

if($_SESSION['usuario']['tipo_usuario'] === 'vendedor'){
    $usuario_id = $_GET["id"] ?? $_SESSION['usuario']['id'];
    $queryUsuario = "SELECT nombre, foto FROM vendedores WHERE id = $usuario_id";
    $resUsuario = mysqli_query($enlace, $queryUsuario);
    $usuario = mysqli_fetch_assoc($resUsuario);
    $nombre = $usuario['nombre'];
    $foto = $usuario['foto'] ?? 'assets/img/default-profile.png';
}

if($_SESSION['usuario']['tipo_usuario'] === 'usuario'){
    $usuario_id = $_GET["id"] ?? $_SESSION['usuario']['id'];
    $queryUsuario = "SELECT nombre, foto FROM usuarios WHERE id = $usuario_id";
    $resUsuario = mysqli_query($enlace, $queryUsuario);
    $usuario = mysqli_fetch_assoc($resUsuario);
    $nombre = $usuario['nombre'];
    $foto = $usuario['foto'] ?? 'assets/img/default-profile.png';
}



//  CONTADORES


// Contar seguidores
$querySeguidores = "SELECT COUNT(*) AS total FROM seguidores WHERE seguido_id = $usuario_id";
$resSeguidores = mysqli_query($enlace, $querySeguidores);
$totalSeguidores = mysqli_fetch_assoc($resSeguidores)['total'] ?? 0;

// Contar descargas
$queryDescargas = "SELECT COUNT(*) AS total FROM descargas WHERE usuario_id = $usuario_id";
$resDescargas = mysqli_query($enlace, $queryDescargas);
$totalDescargas = mysqli_fetch_assoc($resDescargas)['total'] ?? 0;

// Verificar si es vendedor (si tiene al menos un producto)
$queryProductos = "SELECT COUNT(*) AS total FROM productos WHERE vendedor_id = $usuario_id";
$resProductos = mysqli_query($enlace, $queryProductos);
$totalProductos = mysqli_fetch_assoc($resProductos)['total'] ?? 0;
$es_vendedor = $totalProductos > 0;


//  Productos con Like

$queryLikes = "
  SELECT p.*, i.ruta AS imagen, v.nombre AS vendedor_nombre
  FROM likes l
  INNER JOIN productos p ON l.producto_id = p.id
  LEFT JOIN imagenes i ON p.id = i.producto_id
  LEFT JOIN vendedores v ON p.vendedor_id = v.id
  WHERE l.usuario_id = $usuario_id
  GROUP BY p.id
  ORDER BY p.id DESC
";
$resLikes = mysqli_query($enlace, $queryLikes);

//  Productos subidos (si es vendedor)

$resSubidos = null;
if ($es_vendedor) {
  $querySubidos = "
    SELECT p.*, i.ruta AS imagen
    FROM productos p
    LEFT JOIN imagenes i ON p.id = i.producto_id
    WHERE p.vendedor_id = $usuario_id
    GROUP BY p.id
    ORDER BY p.id DESC
  ";
  $resSubidos = mysqli_query($enlace, $querySubidos);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mi Perfil - 3D Models</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #F5EFE6; color: #333; font-family: 'Segoe UI', sans-serif; }
.navbar { background-color: #CBDCEB; box-shadow: 0 0 10px rgba(109,148,197,0.4); }
.perfil-container { text-align:center; margin-top:40px; }
.perfil-foto { position:relative; display:inline-block; width:160px; height:160px; border-radius:50%; overflow:hidden; border:4px solid #6D94C5; box-shadow:0 0 10px rgba(109,148,197,0.4); transition:0.3s; }
.perfil-foto:hover { transform:scale(1.05); }
.perfil-foto img { width:100%; height:100%; object-fit:cover; }
.overlay { position:absolute; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.65); color:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center; opacity:0; transition:opacity 0.3s ease; gap:10px; }
.perfil-foto:hover .overlay { opacity:1; }
.overlay button { border:none; background:#6D94C5; color:white; padding:6px 12px; border-radius:6px; cursor:pointer; transition:0.3s; }
.overlay button:hover { background:#587aa4; }

.card { background-color:#CBDCEB; border:1px solid #6D94C5; color:#000; border-radius:8px; transition:transform 0.2s ease, box-shadow 0.2s ease; }
.card:hover { transform:translateY(-5px); box-shadow:0 4px 12px rgba(109,148,197,0.4); }
.card img { height:200px; object-fit:cover; border-bottom:1px solid #6D94C5; }

.btn-magenta { background-color:#6D94C5; color:#fff; border:none; }
.btn-magenta:hover { background-color:#5c7fb0; }

.contador-container { display:flex; justify-content:center; gap:40px; margin-top:25px; flex-wrap:wrap; }
.contador { background-color:#CBDCEB; border:2px solid #6D94C5; border-radius:10px; padding:20px 30px; min-width:150px; box-shadow:0 0 10px rgba(109,148,197,0.4); }
.contador h4 { margin:0; font-size:2rem; color:#6D94C5; }
.contador p { margin:0; color:#333; font-weight:bold; }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg px-4">
  <a class="navbar-brand titulo" href="../productos/index.php">
    <img src="../../marketing/logo-3dmodels.png" alt="3D-Models" style="height:40px;">
  </a>
  <div class="collapse navbar-collapse">
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><span class="nav-link">Hola, <?php echo htmlspecialchars($nombre); ?></span></li>
      <li class="nav-item"><a class="nav-link" href="../auth/cerrar_sesion.php">Cerrar sesión</a></li>
    </ul>
  </div>
</nav>

<!-- PERFIL -->
<div class="perfil-container">
  <div class="perfil-foto">
    <img src="../../<?php echo htmlspecialchars($foto); ?>" alt="Foto de perfil">
    <div class="overlay">
      <form id="formSubirFoto" action="subir_foto.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="foto" accept="image/*" id="inputFoto" hidden>
        <button type="button" onclick="document.getElementById('inputFoto').click()">Cambiar foto</button>
      </form>
      <?php if ($foto !== 'assets/img/default-profile.png'): ?>
        <form action="subir_foto.php" method="POST">
          <button type="submit" name="eliminar_foto">Eliminar</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
  <h2 class="mt-3 titulo"><?php echo htmlspecialchars($nombre); ?></h2>
  <p class="text-muted"><?php if($_SESSION['usuario']['tipo_usuario'] === 'vendedor'){
    echo 'Vendedor';
  }else{
    echo 'Usuario';
  } ?></p>

  <!-- CONTADORES -->
  <div class="contador-container">
    <div class="contador"><h4><?php echo $totalSeguidores; ?></h4><p>Seguidores</p></div>
    <div class="contador"><h4><?php echo $totalProductos; ?></h4><p>Publicacions</p></div>
    <div class="contador"><h4><?php echo $totalDescargas; ?></h4><p>Descargas</p></div>
  </div>
</div>

<!-- PRODUCTOS CON LIKE -->
<div class="container py-5">
  <h3 class="titulo text-center mb-4">Productos que te gustaron</h3>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while ($like = mysqli_fetch_assoc($resLikes)): ?>
      <div class="col">
        <div class="card h-100">
          <img src="../../<?php echo htmlspecialchars($like['imagen'] ?? 'imagenes/default.jpg'); ?>" class="card-img-top" alt="Producto">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($like['titulo']); ?></h5>
            <p class="card-text"><?php echo substr($like['descripcion'],0,60).'...'; ?></p>
            <p class="mb-2">Vendedor: <?php echo htmlspecialchars($like['vendedor_nombre'] ?? 'Desconocido'); ?></p>
            <a href="../productos/show.php?id=<?php echo $like['id']; ?>" class="btn btn-magenta">Ver más</a>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<!-- PRODUCTOS SUBIDOS -->
<?php if ($es_vendedor && $resSubidos && ($_SESSION['usuario']['tipo_usuario'] === 'vendedor')): ?>
<div class="container py-5">
  <h3 class="titulo text-center mb-4">Mis productos subidos</h3>
  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php while ($subido = mysqli_fetch_assoc($resSubidos)): ?>
      <div class="col">
        <div class="card h-100">
          <img src="../../<?php echo htmlspecialchars($subido['imagen'] ?? 'imagenes/default.jpg'); ?>" class="card-img-top" alt="Producto">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($subido['titulo']); ?></h5>
            <p class="card-text"><?php echo substr($subido['descripcion'],0,60).'...'; ?></p>
            <div class="d-flex justify-content-between">
              <a href="../productos/edit.php?id=<?php echo $subido['id']; ?>" class="btn btn-success btn-sm">Editar</a>
              <a href="../productos/delete.php?id=<?php echo $subido['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro querés eliminar este producto?');">Eliminar</a>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php endif; ?>

<script>
document.getElementById('inputFoto').addEventListener('change', function(){
  if(this.files.length>0){ document.getElementById('formSubirFoto').submit(); }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
