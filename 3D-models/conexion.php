<?php 
$servidor = "localhost";
$usuario = "root";
$pass = "";
$bd = "3D-models";

$enlace = mysqli_connect ($servidor, $usuario, $pass, $bd);

if (!$enlace) {
    die("Error en la conexión: " . mysqli_connect_error());
}


?>