<?php
session_start();
include("../../conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../auth/login.php");
    exit();
}

$usuario_id = intval($_SESSION['usuario']['id']);
$tipo_usuario = $_SESSION['usuario']['tipo_usuario']; // Necesario para distinguir vendedor o usuario
$carpeta_destino = __DIR__ . "/../../imagenes_perfil/";


// ELIMINAR FOTO

if (isset($_POST['eliminar_foto'])) {
    $foto_actual = $_SESSION['usuario']['foto'] ?? null;

    if ($foto_actual && file_exists(__DIR__ . "/../../" . $foto_actual)) {
        unlink(__DIR__ . "/../../" . $foto_actual);
    }

    // Determinar la tabla correcta
    $tabla = ($tipo_usuario === 'vendedor') ? 'vendedores' : 'usuarios';
    $query = "UPDATE $tabla SET foto = NULL WHERE id = $usuario_id";
    mysqli_query($enlace, $query);

    $_SESSION['usuario']['foto'] = null;
    header("Location: perfil.php?eliminada=1");
    exit();
}


//  SUBIR NUEVA FOTO

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {

    if (!is_dir($carpeta_destino)) {
        mkdir($carpeta_destino, 0755, true);
    }

    $nombre_original = $_FILES['foto']['name'];
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
    $formatos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($extension, $formatos_permitidos)) {
        header("Location: perfil.php?error=formato");
        exit();
    }

    $nombre_unico = uniqid('perfil_', true) . '.' . $extension;
    $ruta_destino = $carpeta_destino . $nombre_unico;
    $ruta_relativa = "imagenes_perfil/" . $nombre_unico;

    // Eliminar la foto anterior si existe
    if (!empty($_SESSION['usuario']['foto']) && file_exists(__DIR__ . "/../../" . $_SESSION['usuario']['foto'])) {
        unlink(__DIR__ . "/../../" . $_SESSION['usuario']['foto']);
    }

    // Mover archivo al destino final
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {

        // Determinar la tabla correcta según el tipo de usuario
        $tabla = ($tipo_usuario === 'vendedor') ? 'vendedores' : 'usuarios';

        // Actualizar base de datos
        $query = "UPDATE $tabla SET foto = '$ruta_relativa' WHERE id = $usuario_id";
        mysqli_query($enlace, $query);

        // Actualizar sesión
        $_SESSION['usuario']['foto'] = $ruta_relativa;

        header("Location: perfil.php?success=1");
        exit();
    } else {
        header("Location: perfil.php?error=movimiento");
        exit();
    }
}

// Si no hay acción válida
header("Location: perfil.php?error=subida");
exit();
?>
