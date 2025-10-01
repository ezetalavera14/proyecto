<?php
session_start();
include("../../conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = mysqli_real_escape_string($enlace, $_POST['email']);
  $password = $_POST['password'];
  $tipo = $_POST['tipo']; // 'usuario' o 'vendedor'

  // busca datos en tabla según tipo seleccionado
  $tabla = ($tipo === 'vendedor') ? 'vendedores' : 'usuarios';

  $consulta = "SELECT * FROM $tabla WHERE email = '$email'";
  $resultado = mysqli_query($enlace, $consulta);

  if (mysqli_num_rows($resultado) === 1) {
    $usuario = mysqli_fetch_assoc($resultado);

    if (password_verify($password, $usuario['password'])) {
      // Agregamos el tipo de usuario al array 
      $usuario['tipo_usuario'] = $tipo;

      // Guardamos todo junto en la sesión
      $_SESSION['usuario'] = $usuario;

      echo "<script>alert('Inicio de sesión exitoso como $tipo'); window.location.href = '../productos/index.php';</script>";
      exit;  
    } else {
      echo "<script>alert('Contraseña incorrecta'); window.location.href = 'login.php';</script>";
      exit;
    }
  } else {
    echo "<script>alert('Usuario no encontrado'); window.location.href = 'login.php';</script>";
    exit;
  }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #000; color: #fff; }
    .form-container {
      max-width: 400px;
      margin: 60px auto;
      padding: 30px;
      background-color: #111;
      border-radius: 10px;
      box-shadow: 0 0 10px #ff00ff88;
    }
    .form-control, .form-label { color: #fff; }
    .btn-magenta {
      background-color: #ff00ff; color: #000; border: none;
    }
    .btn-magenta:hover { background-color: #cc00cc; }
    .title { color: #ff00ff; }
    .form-text a { color: #005eff; }
    .form-check-label { color: #fff; }
  </style>
</head>
<body>
<div class="form-container">
  <h2 class="text-center title">Iniciar sesión</h2>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label d-block">Tipo de cuenta</label>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="tipo" value="usuario" id="tipoUsuario" checked>
        <label class="form-check-label" for="tipoUsuario">Usuario</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="tipo" value="vendedor" id="tipoVendedor">
        <label class="form-check-label" for="tipoVendedor">Vendedor</label>
      </div>
    </div>
    <button type="submit" class="btn btn-magenta w-100">Ingresar</button>
    <p class="form-text mt-3 text-center">¿No tenés cuenta? <a href="registro.php">Registrate</a></p>
  </form>
</div>
</body>
</html>
