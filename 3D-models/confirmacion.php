<?php
session_start();
include("conexion.php");

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario_id = $_SESSION['usuario']['id'];

// Traer las últimas compras del usuario
$sql = "SELECT c.id, p.titulo, c.cantidad, p.precio, c.fecha_compra, i.ruta
        FROM compras c
        JOIN productos p ON c.producto_id = p.id
        LEFT JOIN imagenes i ON p.id = i.producto_id
        WHERE c.usuario_id = $usuario_id
        ORDER BY c.fecha_compra DESC
        LIMIT 10"; // muestra las últimas 10 compras

$resultado = mysqli_query($enlace, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Compra</title>
</head>
<body>
    <h1>¡Gracias por tu compra!</h1>
    <p>Tu pedido fue procesado correctamente.</p>

    <h2>Resumen de tu compra:</h2>
    <table border="1" cellpadding="10">
        <tr>
            <th>Imagen</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Subtotal</th>
            <th>Fecha</th>
        </tr>
        <?php
        $total = 0;
        while ($fila = mysqli_fetch_assoc($resultado)) {
            $subtotal = $fila['precio'] * $fila['cantidad'];
            $total += $subtotal;
            $imagen = $fila['ruta'] ? $fila['ruta'] : "imagenes/default.png";
            echo "<tr>
                    <td><img src='$imagen' width='80'></td>
                    <td>{$fila['titulo']}</td>
                    <td>{$fila['cantidad']}</td>
                    <td>\${$fila['precio']}</td>
                    <td>\$$subtotal</td>
                    <td>{$fila['fecha_compra']}</td>
                  </tr>";
        }
        ?>
    </table>

    <h2>Total de la compra: $<?php echo $total; ?></h2>

    <a href="index.php">Volver a la tienda</a>
</body>
</html>
