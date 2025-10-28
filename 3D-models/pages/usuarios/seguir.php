<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['usuario'])) {
  header("Location: ../auth/login.php");
  exit();
}

if (isset($_POST['seguidor_id'], $_POST['seguido_id'])) {
  $seguidor_id = intval($_POST['seguidor_id']);
  $seguido_id = intval($_POST['seguido_id']);

  // Verificar si ya lo sigue
  $check = mysqli_query($enlace, "SELECT id FROM seguidores WHERE seguidor_id=$seguidor_id AND seguido_id=$seguido_id");
  if (mysqli_num_rows($check) > 0) {
    // Si ya lo sigue, dejar de seguir
    mysqli_query($enlace, "DELETE FROM seguidores WHERE seguidor_id=$seguidor_id AND seguido_id=$seguido_id");
  } else {
    // Si no lo sigue, seguirlo
    mysqli_query($enlace, "INSERT INTO seguidores (seguidor_id, seguido_id) VALUES ($seguidor_id, $seguido_id)");
  }

  // Redirigir de nuevo a la página anterior
  header("Location: " . $_SERVER['HTTP_REFERER']);
  exit();
} else {
  echo "Error: faltan datos.";
}
?>
