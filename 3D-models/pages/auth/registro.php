<?php
include("../../conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nombre = mysqli_real_escape_string($enlace, $_POST['nombre']);
  $email = mysqli_real_escape_string($enlace, $_POST['email']);
  $passwordHash = password_hash($_POST['password'], PASSWORD_BCRYPT);

  $verificar = mysqli_query($enlace, "SELECT * FROM usuarios WHERE email = '$email'");
  if (mysqli_num_rows($verificar) > 0) {
    echo "<script>alert('El correo ya está registrado'); window.location.href='registro.php';</script>";
  } else {
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre', '$email', '$passwordHash')";
    if (mysqli_query($enlace, $sql)) {
      echo "<script>alert('Registro exitoso'); window.location.href='login.php';</script>";
    } else {
      echo "<script>alert('Error en el registro');</script>";
    }
  }
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
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
  </style>
</head>
<body>
<div class="form-container">
  <h2 class="text-center title">Crear cuenta</h2>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Nombre completo</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Correo electrónico</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Contraseña</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-magenta w-100">Registrarme</button>
    <p class="form-text mt-3 text-center">¿Ya tenés cuenta? <a href="login.php">Iniciar sesión</a></p>
  </form>
</div>
</body>
</html>
