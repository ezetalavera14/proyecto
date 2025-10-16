<?php
session_start();
include("../../conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit();
}

require '../../../vendor/autoload.php';
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Order\OrderClient;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Exceptions\MPApiException;

MercadoPagoConfig::setAccessToken("APP_USR-1806609405757683-101519-0a3c1d64880c5f29826289479341f782-1049032489");

$usuario_id = $_SESSION['usuario']['id'];
$payer_email = $_SESSION['usuario']['email'] ?? "comprador@ejemplo.com";

// Obtener carrito
$sql = "SELECT * FROM carrito WHERE usuario_id = $usuario_id";
$resultado = mysqli_query($enlace, $sql);
$items = [];
$total = 0;
while ($carrito = mysqli_fetch_assoc($resultado)) {
    $producto_id = $carrito['producto_id'];
    $cantidad = $carrito['cantidad'];
    $q = mysqli_query($enlace, "SELECT * FROM productos WHERE id = $producto_id");
    $producto = mysqli_fetch_assoc($q);
    $total += $producto['precio'] * $cantidad;
    $items[] = $producto;
}

// Si carrito vacío
if (empty($items)) {
    header("Location: ../../carrito.php");
    exit();
}

// 🔹 Procesar pago
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['card_token'])) {
    $token = $_POST['card_token'];
    $client = new OrderClient();

    try {
        $request = [
            "type" => "online",
            "processing_mode" => "automatic",
            "total_amount" => number_format($total, 2, '.', ''),
            "external_reference" => "USER_" . $usuario_id,
            "capture_mode" => "automatic_async",
            "payer" => [ "email" => $payer_email ],
            "transactions" => [
                "payments" => [[
                    "amount" => number_format($total, 2, '.', ''),
                    "payment_method" => [
                        "id" => "visa",
                        "type" => "credit_card",
                        "token" => $token,
                        "installments" => 1,
                        "statement_descriptor" => "3D-MODELS"
                    ]
                ]]
            ]
        ];

        $opts = new RequestOptions();
        $opts->setCustomHeaders(["X-Idempotency-Key: ORDER_" . uniqid()]);
        $order = $client->create($request, $opts);

        echo "<div style='text-align:center; font-family:sans-serif'>";
        echo "<h2>✅ Pago aprobado</h2>";
        echo "<p>Monto total: <b>$" . number_format($total, 2, ',', '.') . "</b></p>";
        echo "<p>ID de la orden: " . $order->id . "</p>";
        echo "<a href='../../index.php' style='padding:10px 20px; background:#009EE3; color:#fff; border-radius:6px; text-decoration:none;'>Volver al inicio</a>";
        echo "</div>";
        exit;

    } catch (MPApiException $e) {
        echo "<h2>❌ Error en Mercado Pago</h2><pre>";
        var_dump($e->getApiResponse()->getContent());
        echo "</pre>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago con tarjeta | 3D-Models</title>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #0b0f1a, #16213e);
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  color: #fff;
}
.card-wrapper {
  perspective: 1000px;
}
.credit-card {
  width: 380px;
  height: 220px;
  background: linear-gradient(135deg, #009EE3, #004aad);
  border-radius: 15px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.3);
  padding: 20px;
  position: relative;
  transform-style: preserve-3d;
  transition: transform 0.8s ease;
}
.credit-card.flip {
  transform: rotateY(180deg);
}
.card-front, .card-back {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
}
.card-back {
  transform: rotateY(180deg);
  background: linear-gradient(135deg, #004aad, #009EE3);
}
.chip {
  width: 50px;
  height: 35px;
  background: #d4d4d4;
  border-radius: 6px;
  margin-bottom: 20px;
}
#card-number {
  letter-spacing: 2px;
  font-size: 1.2rem;
}
.form-container {
  margin-top: 30px;
  width: 400px;
  text-align: center;
}
form input {
  width: 100%;
  padding: 10px;
  border: none;
  border-radius: 6px;
  margin: 5px 0;
  font-size: 15px;
}
button {
  margin-top: 10px;
  width: 100%;
  padding: 10px;
  background: #00b4d8;
  border: none;
  border-radius: 6px;
  color: #fff;
  font-size: 16px;
  cursor: pointer;
  transition: 0.3s;
}
button:hover { background: #007bbf; }
</style>
</head>
<body>

<div class="form-container">
  <div class="card-wrapper">
    <div class="credit-card" id="credit-card">
      <div class="card-front">
        <div class="chip"></div>
        <div id="card-number">•••• •••• •••• ••••</div>
        <div style="margin-top:40px; display:flex; justify-content:space-between; font-size:14px;">
          <div id="card-name">NOMBRE TITULAR</div>
          <div id="card-exp">MM/AA</div>
        </div>
      </div>
      <div class="card-back">
        <div style="background:#000; height:40px; margin-top:30px;"></div>
        <div style="text-align:right; padding:10px;">
          <span id="card-cvv" style="background:#fff; color:#000; padding:3px 6px; border-radius:3px;">•••</span>
        </div>
      </div>
    </div>
  </div>

  <h3 style="margin-top:20px;">💳 Pago con tarjeta</h3>
  <form id="payment-form">
    <input type="text" id="form-cardNumber" placeholder="Número de tarjeta" maxlength="19" />
    <input type="text" id="form-cardName" placeholder="Nombre del titular" />
    <input type="text" id="form-cardExpiry" placeholder="MM/AA" maxlength="5" />
    <input type="text" id="form-cardCvv" placeholder="CVV" maxlength="4" />
    <input type="email" id="form-cardEmail" placeholder="Correo electrónico" />
    <button type="submit">Pagar</button>
  </form>
</div>

<script>
const card = document.getElementById('credit-card');
const numberField = document.getElementById('form-cardNumber');
const nameField = document.getElementById('form-cardName');
const expiryField = document.getElementById('form-cardExpiry');
const cvv = document.getElementById('form-cardCvv');

// ✅ Formateo visual de vencimiento tipo billetera (auto “/”)
expiryField.addEventListener('input', e => {
  let value = e.target.value.replace(/\D/g, ''); // solo números
  if (value.length >= 3) value = value.substring(0, 2) + '/' + value.substring(2, 4);
  e.target.value = value;
  document.getElementById('card-exp').textContent = value || 'MM/AA';
});

// Reflejar nombre y número
numberField.addEventListener('input', () => {
  const v = numberField.value.replace(/(\d{4})(?=\d)/g, '$1 ').trim();
  document.getElementById('card-number').textContent = v || '•••• •••• •••• ••••';
});
nameField.addEventListener('input', () => {
  document.getElementById('card-name').textContent = nameField.value.toUpperCase() || 'NOMBRE TITULAR';
});
cvv.addEventListener('focus', () => card.classList.add('flip'));
cvv.addEventListener('blur', () => card.classList.remove('flip'));
cvv.addEventListener('input', () => {
  document.getElementById('card-cvv').textContent = cvv.value || '•••';
});
</script>
</body>
</html>
