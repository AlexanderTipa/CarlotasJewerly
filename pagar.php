<?php
// 1. Iniciar sesión y conexión
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'includes/db_conexion.php';

// 2. ¡SEGURIDAD!
if (!isset($_SESSION['id_usuario'])) {
    header('Location: login.php?origen=pagar'); 
    exit;
}
if (empty($_SESSION['carrito'])) {
    header('Location: carrito.php'); 
    exit;
}

// 3. Calcular el subtotal
$carrito = $_SESSION['carrito'];
$subtotal_general = 0;
foreach ($carrito as $item) {
    $subtotal_general += $item['precio'] * $item['cantidad'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - Carlota's Jewelry</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="auth-page checkout-page">

    <div class="checkout-container">
        
        <div class="checkout-form-container">
            <div class="auth-logo" style="margin-bottom: 20px;">
                <a href="index.php">Carlota's Jewelry</a>
            </div>
            
            <a href="carrito.php" class="enlace-volver" style="margin-bottom: 20px;">&larr; Volver al carrito</a>
            
            <form id="form-pago" action="procesar_pedido.php" method="POST" novalidate>
            
                <fieldset class="checkout-fieldset">
                    <legend>1. Datos de Envío</legend>
                    <div class="form-row">
                        <div class="form-grupo form-grupo-mitad">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" required>
                            <div class="error-texto" id="error-nombre"></div>
                        </div>
                        <div class="form-grupo form-grupo-mitad">
                            <label for="apellido">Apellido:</label>
                            <input type="text" id="apellido" name="apellido" required>
                            <div class="error-texto" id="error-apellido"></div>
                        </div>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="direccion">Dirección (Calle y Número):</label>
                        <input type="text" id="direccion" name="direccion" required>
                        <div class="error-texto" id="error-direccion"></div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-grupo form-grupo-mitad">
                            <label for="colonia">Colonia:</label>
                            <input type="text" id="colonia" name="colonia" required>
                            <div class="error-texto" id="error-colonia"></div>
                        </div>
                        <div class="form-grupo form-grupo-mitad">
                            <label for="cp">Código Postal:</label>
                            <input type="tel" id="cp" name="cp" required>
                            <div class="error-texto" id="error-cp"></div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-grupo form-grupo-mitad">
                            <label for="estado">Estado:</label>
                            <input type="text" id="estado" name="estado" required>
                            <div class="error-texto" id="error-estado"></div>
                        </div>
                        <div class="form-grupo form-grupo-mitad">
                            <label for="municipio">Municipio:</label>
                            <input type="text" id="municipio" name="municipio" required>
                            <div class="error-texto" id="error-municipio"></div>
                        </div>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="telefono">Número de Teléfono:</label>
                        <input type="tel" id="telefono" name="telefono" required>
                        <div class="error-texto" id="error-telefono"></div>
                    </div>

                    <div class="form-row">
                        <div class="form-grupo form-grupo-mitad">
                            <label for="num_interior">Núm. Interior (Opcional):</label>
                            <input type="text" id="num_interior" name="num_interior">
                        </div>
                    </div>
                    
                    <div class="form-grupo">
                        <label for="indicaciones">Indicaciones (Opcional):</label>
                        <textarea id="indicaciones" name="indicaciones" rows="3"></textarea>
                    </div>
                </fieldset>
                
                <fieldset class="checkout-fieldset">
                    <legend>2. Opciones de Envío</legend>
                    <div id="error-envio" class="alerta alerta-error" style="display:none;">Debes seleccionar una opción de envío.</div>
                    
                    <label class="opcion-radio-card">
                        <input type="radio" name="opcion_envio" value="MexPost México 2-3 Semanas" data-costo="75.00" required>
                        <span class="opcion-radio-detalle">
                            <strong>Envío por MexPost México 2-3 Semanas</strong>
                            <span>$75.00 MXN</span>
                        </span>
                    </label>
                    <label class="opcion-radio-card">
                        <input type="radio" name="opcion_envio" value="Fedex México 2 a 5 Días Hábiles" data-costo="280.00" required>
                        <span class="opcion-radio-detalle">
                            <strong>Envío Fedex México 2 a 5 Días Hábiles</strong>
                            <span>$280.00 MXN</span>
                        </span>
                    </label>
                </fieldset>
                
                <fieldset class="checkout-fieldset">
                    <legend>3. Pago</legend>
                    <div id="error-pago" class="alerta alerta-error" style="display:none;">Debes seleccionar un método de pago.</div>

                    <label class="opcion-radio-card">
                        <input type="radio" name="metodo_pago" value="transferencia" required>
                        <span class="opcion-radio-detalle">
                            <strong>Transferencia bancaria, depósito en Oxxo o banco</strong>
                        </span>
                    </label>
                    
                    <div id="mensaje-pago" class="alerta alerta-info" style="display:none; margin-top: 15px; text-align: left;">
                        <p style="margin: 0; font-size: 0.95rem; line-height: 1.6;">
                            Por favor mándanos un Whatsapp al número <b>012 345 6789</b> y con gusto te brindamos el número de cuenta para la transferencia, pago en ventanilla o en un Oxxo. También puedes hacernos una llamada telefónica. ¡Éxito!
                        </p>
                    </div>
                    </fieldset>
                
                <button type="submit" class="btn btn-pagar-movil" form="form-pago">
                    Hacer mi Pedido
                </button>
                
            </form>
        </div>
        
        <div class="checkout-summary" data-subtotal="<?php echo $subtotal_general; ?>">
            <h3 class="resumen-titulo">Resumen del Pedido</h3>
            
            <div class="resumen-items">
                <?php foreach ($carrito as $item): ?>
                    <div class="resumen-item">
                        <img src="uploads/<?php echo htmlspecialchars($item['imagen']); ?>" alt="" class="resumen-item-img">
                        <div class="resumen-item-info">
                            <span><?php echo htmlspecialchars($item['nombre']); ?></span>
                            <span class="resumen-item-qty">Cant: <?php echo $item['cantidad']; ?></span>
                        </div>
                        <span class="resumen-item-precio">$<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="resumen-calculos">
                <div class="total-fila">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-valor">$<?php echo number_format($subtotal_general, 2); ?></span>
                </div>
                <div class="total-fila">
                    <span class="total-label">Envío:</span>
                    <span class="total-valor" id="resumen-envio-costo">--</span>
                </div>
                <div class="total-fila total-general">
                    <span class="total-label">Total:</span>
                    <span class="total-valor" id="resumen-total-costo">$<?php echo number_format($subtotal_general, 2); ?></span>
                </div>
            </div>
            
            <button type="submit" class="btn btn-principal btn-pagar" form="form-pago">
                Hacer mi Pedido
            </button>
            
        </div>
        
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // --- Referencias a elementos ---
    const form = document.getElementById('form-pago');
    const radiosEnvio = document.querySelectorAll('input[name="opcion_envio"]');
    const radioPago = document.querySelector('input[name="metodo_pago"]');
    const mensajePagoDiv = document.getElementById('mensaje-pago');
    
    // Referencias al resumen
    const resumenDiv = document.querySelector('.checkout-summary');
    const subtotalBase = parseFloat(resumenDiv.dataset.subtotal);
    const envioCostoEl = document.getElementById('resumen-envio-costo');
    const totalCostoEl = document.getElementById('resumen-total-costo');
    
    // --- Referencias a campos de texto y errores ---
    const campos = {
        nombre: {
            input: document.getElementById('nombre'),
            error: document.getElementById('error-nombre'),
            regex: /^[a-zA-Z\s\u00C0-\u017F]+$/, 
            errorMsg: "Solo se permiten letras y acentos."
        },
        apellido: {
            input: document.getElementById('apellido'),
            error: document.getElementById('error-apellido'),
            regex: /^[a-zA-Z\s\u00C0-\u017F]+$/,
            errorMsg: "Solo se permiten letras y acentos."
        },
        estado: {
            input: document.getElementById('estado'),
            error: document.getElementById('error-estado'),
            regex: /^[a-zA-Z\s\u00C0-\u017F]+$/,
            errorMsg: "Solo se permiten letras y acentos."
        },
        municipio: {
            input: document.getElementById('municipio'),
            error: document.getElementById('error-municipio'),
            regex: /^[a-zA-Z\s\u00C0-\u017F]+$/,
            errorMsg: "Solo se permiten letras y acentos."
        },
        telefono: {
            input: document.getElementById('telefono'),
            error: document.getElementById('error-telefono'),
            regex: /^\d{10}$/, 
            errorMsg: "No es un número de teléfono válido (debe tener 10 dígitos)."
        },
        cp: {
            input: document.getElementById('cp'),
            error: document.getElementById('error-cp'),
            regex: /^\d{5}$/, 
            errorMsg: "El C.P. debe tener 5 dígitos."
        },
        // (Campos requeridos sin validación de formato)
        direccion: { input: document.getElementById('direccion'), error: document.getElementById('error-direccion') },
        colonia: { input: document.getElementById('colonia'), error: document.getElementById('error-colonia') }
    };

    // --- Funciones Helper de Validación ---
    function mostrarError(el, errorEl, mensaje) {
        el.classList.add('input-error');
        errorEl.textContent = mensaje;
        errorEl.style.display = 'block';
    }

    function limpiarError(el, errorEl) {
        el.classList.remove('input-error');
        errorEl.style.display = 'none';
    }

    // --- Función 1: Actualizar Totales ---
    function actualizarTotales(costoEnvio) {
        const costo = parseFloat(costoEnvio);
        const total = subtotalBase + costo;
        
        envioCostoEl.textContent = `$${costo.toFixed(2)} MXN`;
        totalCostoEl.textContent = `$${total.toFixed(2)} MXN`;
    }
    
    // --- Función 2: Mostrar Mensaje de Pago ---
    function mostrarMensajePago() {
        if (radioPago.checked) {
            mensajePagoDiv.style.display = 'block';
        }
    }
    
    // --- Función 3: Validar Formulario ---
    function validarFormulario(event) {
        let esFormularioValido = true;
        let primerError = null; 

        // 1. Limpiar todos los errores de texto
        Object.keys(campos).forEach(key => {
            limpiarError(campos[key].input, campos[key].error);
        });
        document.getElementById('error-envio').style.display = 'none';
        document.getElementById('error-pago').style.display = 'none';

        // 2. Validar campos de texto requeridos
        ['nombre', 'apellido', 'direccion', 'colonia', 'cp', 'estado', 'municipio', 'telefono'].forEach(key => {
            const campo = campos[key];
            if (campo.input.value.trim() === '') {
                mostrarError(campo.input, campo.error, "Este campo es obligatorio.");
                esFormularioValido = false;
                if (!primerError) primerError = campo.input;
            }
        });

        // 3. Validar formato (Regex) solo si no están vacíos
        ['nombre', 'apellido', 'estado', 'municipio', 'telefono', 'cp'].forEach(key => {
            const campo = campos[key];
            if (campo.input.value.trim() !== '' && !campo.regex.test(campo.input.value.trim())) {
                mostrarError(campo.input, campo.error, campo.errorMsg);
                esFormularioValido = false;
                if (!primerError) primerError = campo.input;
            }
        });

        // 4. Validar opciones de radio (Envío)
        let esEnvioValido = false;
        radiosEnvio.forEach(radio => {
            if (radio.checked) esEnvioValido = true;
        });
        if (!esEnvioValido) {
            document.getElementById('error-envio').style.display = 'block';
            esFormularioValido = false;
            if (!primerError) primerError = document.getElementById('error-envio');
        }
        
        // 5. Validar opciones de radio (Pago)
        if (!radioPago.checked) {
            document.getElementById('error-pago').style.display = 'block';
            esFormularioValido = false;
            if (!primerError) primerError = document.getElementById('error-pago');
        }
        
        // 6. Si hay algún error, detener envío y enfocar
        if (!esFormularioValido) {
            event.preventDefault(); // Detener el envío del formulario
            if (primerError) {
                primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        // Si todo es válido, el formulario se enviará
    }

    // --- Asignar Eventos ---
    
    // 1. Escuchar cambios en las opciones de envío
    radiosEnvio.forEach(radio => {
        radio.addEventListener('change', (e) => {
            const costo = e.target.dataset.costo;
            actualizarTotales(costo);
        });
    });
    
    // 2. Escuchar cambios en la opción de pago
    radioPago.addEventListener('change', mostrarMensajePago);
    
    // 3. Escuchar el envío del formulario para validar
    form.addEventListener('submit', validarFormulario);
    
});
</script>

</body>
</html>