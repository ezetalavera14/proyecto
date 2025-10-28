<?php
session_start();
include("../../conexion.php");

if (!isset($_GET['id'])) {
    echo "<script>alert('Producto no especificado'); window.location.href='index.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Sumar 1 a las visitas
mysqli_query($enlace, "UPDATE productos SET visitas = visitas + 1 WHERE id = $id");

// Obtener producto
$res = mysqli_query($enlace, "SELECT * FROM productos WHERE id = $id");
if (mysqli_num_rows($res) === 0) {
    echo "<script>alert('Producto no encontrado'); window.location.href='index.php';</script>";
    exit;
}
$prod = mysqli_fetch_assoc($res);

// Obtener imagen principal
$res_img = mysqli_query($enlace, "SELECT ruta FROM imagenes WHERE producto_id = $id LIMIT 1");
$img = mysqli_fetch_assoc($res_img);
$ruta_imagen = $img ? "../../" . $img['ruta'] : '../../imagenes/default.jpg';

// Información del vendedor
$vendedor_id = $prod['vendedor_id'];
$usuario_id = $_SESSION['usuario']['id'] ?? 0;

$res_vendedor = mysqli_query($enlace, "SELECT nombre, foto FROM vendedores WHERE id=$vendedor_id");
$vendedor = mysqli_fetch_assoc($res_vendedor);
$nombre_vendedor = $vendedor['nombre'];
$foto_vendedor = $vendedor['foto'] ? "../../" . $vendedor['foto'] : "../../assets/img/default-profile.png";

// Verificar follow
$yaSigue = false;
if ($usuario_id && $usuario_id != $vendedor_id) {
    $checkFollow = mysqli_query($enlace, "SELECT id FROM seguidores WHERE seguidor_id=$usuario_id AND seguido_id=$vendedor_id");
    $yaSigue = mysqli_num_rows($checkFollow) > 0;
}

// Verificar like
$liked = false;
$totalLikes = 0;
if ($usuario_id) {
    $likeCheck = mysqli_query($enlace, "SELECT id FROM likes WHERE usuario_id=$usuario_id AND producto_id=".$prod['id']);
    $liked = mysqli_num_rows($likeCheck) > 0;

    $likeCountRes = mysqli_query($enlace, "SELECT COUNT(*) as total FROM likes WHERE producto_id=".$prod['id']);
    $totalLikes = mysqli_fetch_assoc($likeCountRes)['total'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($prod['titulo']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Estilos generales y de layout */
body {
    background-color: #FAFAFA;
    font-family: 'Poppins', sans-serif;
    color: #222;
}
.producto-container {
    max-width:1000px;
    margin:50px auto;
    background:#fff;
    border-radius:12px;
    padding:20px;
    box-shadow:0 3px 10px rgba(0,0,0,0.08);
    transition:all 0.3s ease;
}
.producto-container:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}
.producto-img { width:100%; border-radius:12px; object-fit:cover; }
.derecha-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
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

/* Botones */
.btn-magenta {
    background: linear-gradient(90deg, #6D94C5 0%, #4A6FA8 100%);
    border: none;
    color: #fff;
    font-weight: 500;
    border-radius: 6px;
    transition: all 0.25s ease;
    width:100%;
    margin-bottom:5px;
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
    width:100%;
    margin-bottom:5px;
}
.btn-success:hover {
    background-color: #dcd2b5;
}
.btn-like {
    display:flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    background: linear-gradient(90deg, #6D94C5 0%, #4A6FA8 100%);
    color:#fff;
    border:none;
    border-radius:6px;
    width:100%;
    margin-bottom:5px;
    transition: all 0.25s ease;
}
.btn-like.liked { background: #e63946; }
.btn-like:hover { transform: translateY(-2px); }
</style>
</head>
<body>
<div class="producto-container">
    <div class="row">
        <!-- Columna izquierda: imagen del producto -->
        <div class="col-md-5">
            <img src="<?php echo $ruta_imagen; ?>" alt="Producto" class="producto-img">
        </div>

        <!-- Columna derecha: vendedor, botones e info -->
        <div class="col-md-7 d-flex flex-column">
            <!-- Arriba derecha: foto y nombre del vendedor -->
<div class="d-flex align-items-center mb-3" style="gap:10px;">
    <a href=".././usuarios/perfil.php?id=<?php echo $vendedor_id; ?>" class="perfil-icono-link">
        <img src="<?php echo $foto_vendedor; ?>" alt="Foto vendedor" class="perfil-icono">
    </a>
    <strong><?php echo $nombre_vendedor; ?></strong>
</div>


            <!-- Botones: dos filas -->
            <div class="d-grid gap-2 mb-3">
                <?php if ($usuario_id && !($usuario_id == $vendedor_id && $_SESSION['usuario']['tipo_usuario']=='vendedor')): ?>
                    <form method="POST" action="../usuarios/seguir.php">
                        <input type="hidden" name="seguidor_id" value="<?php echo $usuario_id; ?>">
                        <input type="hidden" name="seguido_id" value="<?php echo $vendedor_id; ?>">
                        <button type="submit" name="toggle_follow" class="btn btn-magenta mb-1">
                            <?php echo $yaSigue ? 'Siguiendo' : 'Follow'; ?>
                        </button>
                    </form>

                    <a href="pagos.php?id=<?php echo $prod['id']; ?>" class="btn btn-magenta mb-1">Comprar ahora</a>
                    <a href="carrito.php?agregar=<?php echo $prod['id']; ?>" class="btn btn-success mb-1">🛒 Agregar al carrito</a>
                    
                    <form method="POST">
                        <input type="hidden" name="producto_id" value="<?php echo $prod['id']; ?>">
                        <button type="submit" name="toggle_like" class="btn btn-like <?php echo $liked ? 'liked' : ''; ?>">
                            <span>❤️</span> <?php echo $totalLikes; ?>
                        </button>
                        
                    </form>
                    <a href="index.php" class="btn btn-secondary mt-3">Volver al inicio</a>
                <?php else: ?>
                    <a href="../auth/login.php" class="btn btn-magenta mb-1">Comprar ahora</a>
                    <a href="../auth/login.php" class="btn btn-success mb-1">🛒 Agregar al carrito</a>
                <?php endif; ?>
            </div>

            <!-- Información del producto -->
            <div class="mt-auto">
                <h3><?php echo htmlspecialchars($prod['titulo']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars($prod['descripcion'])); ?></p>
                <h4>Precio: $<?php echo number_format($prod['precio'],2); ?></h4>
            </div>
        </div>
    </div>
    
</div>
</body>
</html>
