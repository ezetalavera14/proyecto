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
    body { 
      background-color: #F5EFE6; 
      color: #333; 
      font-family: 'Segoe UI', sans-serif;
    }

    .form-container {
      max-width: 400px;
      margin: 60px auto;
      padding: 30px;
      background-color: #CBDCEB; 
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(109,148,197,0.5);
      border: 2px solid #6D94C5; 
    }

    .form-label {
      color: #333;
      font-weight: 500;
    }

    .form-control {
      border: 1px solid #6D94C5;
      background-color: #F5EFE6;
      color: #333;
    }

    .form-control:focus {
      border-color: #6D94C5;
      box-shadow: 0 0 5px rgba(109,148,197,0.5);
      background-color: #fff;
      color: #333;
    }

    .btn-magenta {
      background-color: #6D94C5;
      color: #fff;
      border: none;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn-magenta:hover {
      background-color: #587aa4;
      color: #fff;
    }

    .title {
      color: #6D94C5;
      font-weight: bold;
    }

    .form-text a {
      color: #6D94C5;
      text-decoration: none;
    }

    .form-text a:hover {
      text-decoration: underline;
    }
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

