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

MercadoPagoConfig::setAccessToken("APP_USR-2169867004278049-102219-065d4b95867c7930e3bf4c07211ec13c-2941945227");

$usuario_id = $_SESSION['usuario']['id'];
/*$payer_email = $_SESSION['usuario']['email'] ?? "comprador@ejemplo.com";

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
}*/

// 🔹 Procesar pago
        $datos = json_decode(file_get_contents('php://input'), true)["data"]; // Pasar de JSON a un AssocArray

        $transaction_amount = $datos['amount'];
        $token = $datos['token'];
        $installments = $datos['installments'];
        $payment_method_id = $datos['paymentMethodId'];
        $issuer_id = $datos['issuerId'];
    
        $email = $datos['cardholderEmail'];
    $client = new OrderClient();

    try {
        $request = [
            "type" => "online",
            "processing_mode" => "automatic",
            "total_amount" => $transaction_amount,
            "external_reference" => "USER_" . $usuario_id,
            "capture_mode" => "automatic_async",
            "payer" => [ "email" => $email ],
            "transactions" => [
                "payments" => [[
                    "amount" => $transaction_amount,
                    "payment_method" => [
                        "id" => $payment_method_id,
                        "type" => "credit_card",
                        "token" => $token,
                        "installments" => intval($installments),
                        "statement_descriptor" => "3D-MODELS"
                    ]
                ]]
            ]
        ];

        $opts = new RequestOptions();
        $opts->setCustomHeaders(["X-Idempotency-Key: ORDER_" . uniqid()]);
        $order = $client->create($request, $opts);

        $data = [
            "status" => "success",
            "message" => "Compra exitosa",
            "data" => [
                "id" => $order->id
            ]
        ];

        // Set the Content-Type header to application/json
        header('Content-Type: application/json');

        // Output the JSON-encoded data
        echo json_encode($data);
    } catch (MPApiException $e) {
        echo "<h2>❌ Error en Mercado Pago</h2><pre>";
        var_dump($e->getApiResponse()->getContent());
        echo "</pre>";
        exit;
    }
?>
