<?php
$pagina_titulo = "Punto de Venta (POS)";
require '../includes/db_conexion.php';
require '../includes/header_admin.php';

// Seguridad Admin
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /login.php');
    exit;
}

// Inicializar carrito de POS
if (!isset($_SESSION['pos_carrito'])) {
    $_SESSION['pos_carrito'] = [];
}

// --- LÓGICA 1: AGREGAR PRODUCTO POR CÓDIGO (ESCÁNER) ---
if (isset($_GET['codigo'])) {
    $codigo = trim($_GET['codigo']);
    
    $stmt = $pdo->prepare("SELECT * FROM accesorios WHERE codigo_barras = ? AND visible = 1");
    $stmt->execute([$codigo]);
    $producto = $stmt->fetch();

    if ($producto) {
        $id = $producto['id_accesorio'];
        
        if ($producto['stock'] > 0) {
            if (isset($_SESSION['pos_carrito'][$id])) {
                // Verificar stock antes de sumar
                if ($_SESSION['pos_carrito'][$id]['cantidad'] < $producto['stock']) {
                    $_SESSION['pos_carrito'][$id]['cantidad']++;
                } else {
                    $error = "Stock máximo alcanzado para este producto.";
                }
            } else {
                $_SESSION['pos_carrito'][$id] = [
                    'id' => $producto['id_accesorio'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => 1,
                    'max_stock' => $producto['stock']
                ];
            }
        } else {
            $error = "Producto agotado: " . $producto['nombre'];
        }
    } else {
        $error = "Producto no encontrado con código: " . $codigo;
    }
    
    // Limpiar URL
    echo "<script>window.history.replaceState(null, null, window.location.pathname);</script>";
}

// --- LÓGICA 2: ACCIONES (SUMAR / RESTAR / ELIMINAR / LIMPIAR) ---
if (isset($_GET['accion'])) {
    $accion = $_GET['accion'];
    
    // Acciones que requieren ID
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        if (isset($_SESSION['pos_carrito'][$id])) {
            
            // SUMAR
            if ($accion == 'sumar') {
                if ($_SESSION['pos_carrito'][$id]['cantidad'] < $_SESSION['pos_carrito'][$id]['max_stock']) {
                    $_SESSION['pos_carrito'][$id]['cantidad']++;
                }
            }
            
            // RESTAR
            if ($accion == 'restar') {
                $_SESSION['pos_carrito'][$id]['cantidad']--;
                // Si llega a 0, eliminar
                if ($_SESSION['pos_carrito'][$id]['cantidad'] <= 0) {
                    unset($_SESSION['pos_carrito'][$id]);
                }
            }
            
            // ELIMINAR
            if ($accion == 'eliminar') {
                unset($_SESSION['pos_carrito'][$id]);
            }
        }
    }
    
    // LIMPIAR TODO
    if ($accion == 'limpiar') {
        $_SESSION['pos_carrito'] = [];
    }
    
    header('Location: punto_venta.php'); // Recargar para ver cambios
    exit;
}

// Calcular Total
$total_pos = 0;
foreach ($_SESSION['pos_carrito'] as $item) {
    $total_pos += $item['precio'] * $item['cantidad'];
}
?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div class="pos-container">
    
    <div class="pos-scanner-section">
        <div class="admin-header">
            <h1 class="admin-titulo">Caja Registradora</h1>
        </div>

        <div class="form-container" style="margin: 0; max-width: 100%;">
            <form action="punto_venta.php" method="GET" class="pos-form-manual">
                <div class="form-grupo" style="display:flex; gap:10px;">
                    <input type="text" name="codigo" id="input-codigo" placeholder="Escanear o escribir código..." autofocus autocomplete="off">
                    <button type="submit" class="btn btn-principal">Agregar</button>
                </div>
            </form>

            <hr>
            <button id="btn-iniciar-camara" class="btn btn-principal" style="width:100%; margin-bottom:10px;">Activar Cámara</button>
            <div id="reader" style="width: 100%; display:none;"></div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alerta alerta-error" style="margin-top: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="pos-ticket-section">
        <div class="ticket-header">
            <h3>Ticket de Venta</h3>
            <a href="punto_venta.php?accion=limpiar" style="color: #e74c3c; font-size: 0.9rem;">Vaciar</a>
        </div>

        <div class="ticket-items">
            <?php if (empty($_SESSION['pos_carrito'])): ?>
                <div style="text-align:center; padding:40px; color:#ccc;">
                    <p style="font-size: 3rem; margin:0;">🛒</p>
                    <p>Carrito vacío</p>
                </div>
            <?php else: ?>
                <table class="tabla-ticket">
                    <?php foreach ($_SESSION['pos_carrito'] as $id => $item): ?>
                    <tr>
                        <td>
                            <strong style="font-size: 0.95rem;"><?php echo htmlspecialchars($item['nombre']); ?></strong>
                            
                            <div class="control-cantidad" style="margin-top: 5px; justify-content: flex-start;">
                                <a href="punto_venta.php?accion=restar&id=<?php echo $id; ?>" class="btn-cantidad" style="width:25px; height:25px; line-height:23px; font-size:1rem;">-</a>
                                
                                <span class="cantidad-numero" style="padding: 0 10px; font-size: 0.9rem;"><?php echo $item['cantidad']; ?></span>
                                
                                <a href="punto_venta.php?accion=sumar&id=<?php echo $id; ?>" class="btn-cantidad" style="width:25px; height:25px; line-height:23px; font-size:1rem;">+</a>
                            </div>
                        </td>
                        
                        <td style="text-align:right; vertical-align: middle;">
                            <div style="font-weight: bold;">
                                $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?>
                            </div>
                            <a href="punto_venta.php?accion=eliminar&id=<?php echo $id; ?>" style="color:#e74c3c; text-decoration:none; font-size:1.2rem; display:block; margin-top:5px;">&times;</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>

<div class="ticket-total">
            <span>Total a Cobrar:</span>
            <span>$<?php echo number_format($total_pos, 2); ?></span>
        </div>

        <?php if (!empty($_SESSION['pos_carrito'])): ?>
            <form action="procesar_venta_mostrador.php" method="POST">
                
                <div class="client-info-card">
                    <h4 class="client-info-title">Datos del Cliente (Opcional)</h4>
                    
                    <div class="client-info-grid">
                        <div class="input-icon-wrapper">
                            <input type="text" name="cliente_nombre" placeholder="Nombre" class="pos-input">
                        </div>
                        <div class="input-icon-wrapper">
                            <input type="text" name="cliente_apellido" placeholder="Apellido" class="pos-input">
                        </div>
                    </div>
                    
                    <div class="input-icon-wrapper" style="margin-top: 10px;">
                        <span class="input-icon"></span>
                        <input type="tel" name="cliente_telefono" placeholder="Número de Celular (10 dígitos)" class="pos-input with-icon">
                    </div>
                </div>
                <input type="hidden" name="total" value="<?php echo $total_pos; ?>">
                
                <button type="submit" class="btn btn-principal btn-cobrar">
                    <span></span> COBRAR $<?php echo number_format($total_pos, 2); ?>
                </button>
            </form>
        <?php endif; ?>
    </div>

</div>

<style>
.pos-container {
    display: grid;
    grid-template-columns: 1fr 350px; 
    gap: 30px;
    margin-top: 20px;
    align-items: start;
}
.pos-ticket-section {
    background: var(--color-blanco);
    border: 1px solid var(--color-borde);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    position: sticky;
    top: 100px; 
}
.ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 2px dashed var(--color-borde);
    padding-bottom: 15px;
    margin-bottom: 15px;
}
.ticket-header h3 { margin: 0; color: var(--color-texto-principal); }
.ticket-items {
    max-height: 400px;
    overflow-y: auto; 
    margin-bottom: 20px;
    min-height: 150px; 
}
.tabla-ticket { width: 100%; border-collapse: collapse; }
.tabla-ticket td { padding: 10px 0; border-bottom: 1px solid #f0f0f0; }

.ticket-total {
    font-size: 1.5rem;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    border-top: 2px dashed var(--color-texto-principal);
    padding-top: 15px;
    color: var(--color-rosa-acento);
}

@media (max-width: 900px) {
    .pos-container { 
        grid-template-columns: 1fr; 
    }
    .pos-ticket-section {
        position: static; 
        order: -1; 
    }
}
    
/*formulario punto venta*/
    .client-info-card {
        background-color: #fff;
        border: 1px solid var(--color-borde);
        border-left: 5px solid var(--color-rosa-principal); /* Acento de marca */
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
    }

    .client-info-title {
        margin: 0 0 15px 0;
        font-size: 0.95rem;
        color: var(--color-texto-principal);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .client-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Dos columnas */
        gap: 10px;
    }

    .pos-input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background-color: #fcfcfc;
        box-sizing: border-box; /* Vital para que no se desborde */
    }

    .pos-input:focus {
        border-color: var(--color-rosa-principal);
        background-color: #fff;
        outline: none;
        box-shadow: 0 0 0 3px rgba(231, 84, 128, 0.1);
    }

    /* Estilo para el input con icono */
    .input-icon-wrapper {
        position: relative;
    }
    
    .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1rem;
        pointer-events: none; /* El clic pasa a través */
    }
    
    .pos-input.with-icon {
        padding-left: 35px; /* Espacio para el icono */
    }

    .btn-cobrar {
        width: 100%;
        margin-top: 20px;
        padding: 15px;
        font-size: 1.2rem;
        background-color: #2ecc71; /* Verde Venta */
        border-color: #2ecc71;
        color: white;
        display: flex;
        justify-content: center;
        gap: 10px;
        align-items: center;
        box-shadow: 0 4px 6px rgba(46, 204, 113, 0.2);
    }
    
    .btn-cobrar:hover {
        background-color: #27ae60;
        transform: translateY(-2px);
    }
</style>

<script>
    const btnCamara = document.getElementById('btn-iniciar-camara');
    const readerDiv = document.getElementById('reader');
    let html5QrCode;
    
    // Variable de control (EL FRENO)
    let escaneando = false; 

    function onScanSuccess(decodedText, decodedResult) {
        // 1. Si ya estamos procesando un escaneo, ¡DETENERSE!
        if (escaneando) {
            return; 
        }

        // 2. Activamos el freno inmediatamente
        escaneando = true;
        
        // Opcional: Sonido de "Beep" para confirmar lectura
        // var audio = new Audio('/assets/beep.mp3'); audio.play();

        // 3. Detenemos la cámara para evitar lecturas fantasma mientras redirige
        if (html5QrCode) {
            html5QrCode.stop().then(() => {
                // 4. Redirigimos una vez que la cámara se ha detenido
                window.location.href = "punto_venta.php?codigo=" + decodedText;
            }).catch(err => {
                // Si falla al detener, redirigimos de todos modos
                window.location.href = "punto_venta.php?codigo=" + decodedText;
            });
        } else {
            window.location.href = "punto_venta.php?codigo=" + decodedText;
        }
    }

    btnCamara.addEventListener('click', () => {
        // Reiniciamos el freno al abrir la cámara
        escaneando = false;

        if (readerDiv.style.display === 'none' || readerDiv.style.display === '') {
            readerDiv.style.display = 'block';
            btnCamara.textContent = 'Detener Cámara';
            
            html5QrCode = new Html5Qrcode("reader");
            
            // Reducimos un poco los FPS para que no sea tan agresivo (de 10 a 5)
            const config = { fps: 5, qrbox: { width: 250, height: 150 } };
            
            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                alert("Error al iniciar cámara: " + err);
                // Si falla, ocultamos todo
                readerDiv.style.display = 'none';
                btnCamara.textContent = 'Activar Cámara';
            });
        } else {
            // Lógica para detener manualmente
            if (html5QrCode) {
                html5QrCode.stop().then(() => {
                    readerDiv.style.display = 'none';
                    btnCamara.textContent = 'Activar Cámara';
                });
            }
        }
    });
    
    // Enfocar siempre el input manual
    if(document.getElementById('input-codigo')) {
        document.getElementById('input-codigo').focus();
    }
</script>

<?php require '../includes/footer_admin.php'; ?>