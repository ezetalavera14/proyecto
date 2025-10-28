<?php
session_start();
include("../../conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pago con tarjeta | 3D-Models</title>
<script src="https://sdk.mercadopago.com/js/v2"></script>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #0b0f1a, #16213e);
  color: #fff;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  height: 100vh;
  margin: 0;
  padding: 40px 0;
  overflow-y: auto;
  scroll-behavior: smooth;
}

/* Contenedor general */
.checkout-container {
  background: rgba(20, 30, 60, 0.9);
  backdrop-filter: blur(8px);
  border-radius: 16px;
  box-shadow: 0 0 25px rgba(0,0,0,0.4);
  padding: 30px 25px 45px 25px;
  width: 95%;
  max-width: 380px;
  text-align: center;
  animation: fadeIn 0.6s ease;
  position: relative;
  margin: 60px auto;
}

/* Tarjeta 3D decorativa */
.credit-card {
  width: 270px;
  height: 160px;
  border-radius: 12px;
  background: linear-gradient(135deg, #004aad, #009EE3);
  box-shadow: 0 10px 25px rgba(0,0,0,0.5);
  position: absolute;
  top: -90px;
  left: 50%;
  transform: translateX(-50%);
  transform-style: preserve-3d;
  transition: transform 0.5s ease;
  perspective: 800px;
}
.credit-card .chip {
  width: 38px;
  height: 28px;
  background: linear-gradient(135deg, #e2e2e2, #c9c9c9);
  border-radius: 5px;
  margin: 15px;
}
.credit-card #visual-number {
  letter-spacing: 2px;
  font-size: 1rem;
  margin-top: 8px;
}
.credit-card #visual-name {
  font-size: 0.8rem;
  text-transform: uppercase;
  margin-top: 10px;
  opacity: 0.9;
}
.credit-card #visual-exp {
  font-size: 0.7rem;
  position: absolute;
  bottom: 18px;
  right: 20px;
  opacity: 0.8;
}

/* Título */
.checkout-container h2 {
  font-size: 1.5rem;
  font-weight: 600;
  margin-top: 80px;
  margin-bottom: 20px;
  background: linear-gradient(90deg, #00b4d8, #0077b6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

/* Formulario */
#form-checkout {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

/* Campos del formulario */
.container {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 6px;
  padding: 3px 4px;       /* 🔹 mucho más compacto */
  height: 28px;           /* 🔹 fija la altura */
  display: flex;
  align-items: center;
}

.container iframe {
  height: 22px !important; /* 🔹 fuerza el iframe del SDK a ser bajo */
  transform: scale(0.85);  /* 🔹 reduce aún más el tamaño interno */
  transform-origin: left center;
}



.container:focus-within {
  border-color: #00b4d8;
  box-shadow: 0 0 6px rgba(0,180,216,0.3);
}

/* Inputs y selects */
input, select {
  width: 100%;
  background: rgba(255,255,255,0.08);
  border: 1px solid transparent;
  border-radius: 6px;
  padding: 6px 8px;
  color: #fff;
  font-size: 13px;
  outline: none;
  transition: background 0.2s ease-in-out, border 0.2s ease-in-out;
}

input::placeholder, select {
  color: rgba(255,255,255,0.6);
}

input:focus, select:focus {
  background: rgba(255,255,255,0.12);
  border-color: #00b4d8;
}

/* Ajustes de separación */
#form-checkout {
  gap: 10px;
}

button {
  margin-top: 12px;
  padding: 10px;
}

button {
  margin-top: 10px;
  width: 100%;
  padding: 10px;
  background: linear-gradient(135deg, #00b4d8, #0077b6);
  border: none;
  border-radius: 10px;
  color: #fff;
  font-weight: 600;
  font-size: 15px;
  cursor: pointer;
  transition: 0.3s ease;
}
button:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,180,216,0.4);
}

.progress-bar {
  width: 100%;
  margin-top: 8px;
  height: 6px;
  border-radius: 4px;
  appearance: none;
}
.progress-bar::-webkit-progress-bar {
  background-color: rgba(255,255,255,0.1);
  border-radius: 4px;
}
.progress-bar::-webkit-progress-value {
  background: linear-gradient(90deg, #00b4d8, #0077b6);
  border-radius: 4px;
}

footer {
  margin-top: 15px;
  font-size: 0.8rem;
  color: #ccc;
  opacity: 0.7;
}

/* Responsive */
@media (max-height: 750px) {
  .checkout-container {
    margin-top: 40px;
    padding-top: 20px;
  }
  .credit-card {
    top: -70px;
  }
  h2 {
    margin-top: 60px;
  }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

</style>
</head>
<body>

<div class="checkout-container">
  <div class="credit-card" id="decorative-card">
    <div class="chip"></div>
    <div id="visual-number">•••• •••• •••• ••••</div>
    <div id="visual-name">NOMBRE TITULAR</div>
    <div id="visual-exp">MM/AA</div>
  </div>

  <h2>💳 Pago seguro</h2>

  <form id="form-checkout">
    <div id="form-checkout__cardNumber" class="container"></div>
    <div id="form-checkout__expirationDate" class="container"></div>
    <div id="form-checkout__securityCode" class="container"></div>
    <input type="text" id="form-checkout__cardholderName" placeholder="Titular de la tarjeta" />
    <select id="form-checkout__issuer"></select>
    <select id="form-checkout__installments"></select>
    <select id="form-checkout__identificationType"></select>
    <input type="text" id="form-checkout__identificationNumber" placeholder="Número de documento" />
    <input type="email" id="form-checkout__cardholderEmail" placeholder="Correo electrónico" />

    <button type="submit" id="form-checkout__submit">Pagar ahora</button>
    <progress value="0" class="progress-bar">Cargando...</progress>
  </form>

  <footer>Procesado de forma segura por Mercado Pago</footer>
</div>

<script>
/* ✅ Animación de tarjeta decorativa */
const card = document.getElementById("decorative-card");
document.addEventListener("mousemove", e => {
  const x = (window.innerWidth / 2 - e.pageX) / 25;
  const y = (window.innerHeight / 2 - e.pageY) / 25;
  card.style.transform = `translateX(-50%) rotateY(${x}deg) rotateX(${y}deg)`;
});

/* Reflejar datos visualmente */
document.getElementById("form-checkout__cardholderName").addEventListener("input", e => {
  document.getElementById("visual-name").textContent = e.target.value.toUpperCase() || "NOMBRE TITULAR";
});
document.getElementById("form-checkout__identificationNumber").addEventListener("focus", () => {
  card.style.transform = "translateX(-50%) rotateY(180deg)";
});
document.getElementById("form-checkout__identificationNumber").addEventListener("blur", () => {
  card.style.transform = "translateX(-50%) rotateY(0deg)";
});

const mp = new MercadoPago("APP_USR-ffa9c8ec-8d8c-4755-8097-024d45c0b53d");

const cardForm = mp.cardForm({
  amount: "100.5",
  iframe: true,
  form: {
    id: "form-checkout",
    cardNumber: { id: "form-checkout__cardNumber", placeholder: "Número de tarjeta" },
    expirationDate: { id: "form-checkout__expirationDate", placeholder: "MM/AA" },
    securityCode: { id: "form-checkout__securityCode", placeholder: "CVV" },
    cardholderName: { id: "form-checkout__cardholderName", placeholder: "Titular" },
    issuer: { id: "form-checkout__issuer" },
    installments: { id: "form-checkout__installments" },
    identificationType: { id: "form-checkout__identificationType" },
    identificationNumber: { id: "form-checkout__identificationNumber" },
    cardholderEmail: { id: "form-checkout__cardholderEmail" },
  },
  callbacks: {
    onFormMounted: error => {
      if (error) return console.warn("Form Mounted handling error: ", error);
    },
    onSubmit: event => {
      event.preventDefault();
      fetch("./process_payment.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ data: cardForm.getCardFormData() })
      })
      .then(response => response.json())
      .then(result => alert(result["message"]))
      .catch(error => alert("Unexpected error\n" + JSON.stringify(error)));
    },
    onFetching: (resource) => {
      const progressBar = document.querySelector(".progress-bar");
      progressBar.removeAttribute("value");
      return () => progressBar.setAttribute("value", "0");
    }
  },
});
</script>

</body>
</html>
