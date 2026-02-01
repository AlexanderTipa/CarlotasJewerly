<?php
$pagina_titulo = "Añadir Nuevo Accesorio"; 
require '../includes/db_conexion.php'; 
require '../includes/header_admin.php';

// ¡SEGURIDAD!
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

// --- Lógica de Manejo ---
$modo_edicion = false;
$accesorio_actual = [
    'id_accesorio' => null, 'id_categoria' => '', 'nombre' => '',
    'descripcion' => '', 'precio' => '', 'stock' => 0, 
    'visible' => 1, 'imagen_url' => '', 'codigo_barras' => '' // <-- NUEVO
];
$error = '';

// --- MODO EDICIÓN (GET) ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $modo_edicion = true;
    $id_accesorio = $_GET['id'];
    $pagina_titulo = "Editar Accesorio";

    $stmt = $pdo->prepare("SELECT * FROM accesorios WHERE id_accesorio = ?");
    $stmt->execute([$id_accesorio]);
    $accesorio_actual = $stmt->fetch();

    if (!$accesorio_actual) {
        $error = "El accesorio no existe.";
        $modo_edicion = false; 
    }
}

// --- PROCESAR FORMULARIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id_categoria = $_POST['id_categoria'];
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = trim($_POST['precio']);
    $stock = (int)$_POST['stock'];
    $visible = isset($_POST['visible']) ? 1 : 0;
    
    // --- NUEVO: Capturar código de barras ---
    $codigo_barras = trim($_POST['codigo_barras']);
    if (empty($codigo_barras)) $codigo_barras = null; // Permitir nulos si no tiene código

    $nombre_imagen_db = $_POST['imagen_actual']; 

    // (Lógica de imagen igual que antes...)
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $archivo_temporal = $_FILES['imagen']['tmp_name'];
        $nombre_archivo_original = basename($_FILES['imagen']['name']);
        $extension = pathinfo($nombre_archivo_original, PATHINFO_EXTENSION);
        $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array(strtolower($extension), $tipos_permitidos)) {
            $nombre_imagen_db = time() . '_' . uniqid() . '.' . $extension;
            $ruta_destino_servidor = '../uploads/' . $nombre_imagen_db;
            if (move_uploaded_file($archivo_temporal, $ruta_destino_servidor)) {
                $imagen_antigua = $_POST['imagen_actual'];
                if (!empty($imagen_antigua) && file_exists('../uploads/' . $imagen_antigua)) {
                    unlink('../uploads/' . $imagen_antigua);
                }
            } else { $error = "Error al mover imagen."; }
        } else { $error = "Tipo de archivo no permitido."; }
    } 
    elseif ($modo_edicion && isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] == '1') {
        $imagen_antigua = $_POST['imagen_actual'];
        if (!empty($imagen_antigua) && file_exists('../uploads/' . $imagen_antigua)) {
            unlink('../uploads/' . $imagen_antigua);
        }
        $nombre_imagen_db = ''; 
    }

    if (empty($error)) {
        try {
            if ($modo_edicion) {
                $id_accesorio_form = $_POST['id_accesorio'];
                // SQL ACTUALIZADO con codigo_barras
                $sql = "UPDATE accesorios SET 
                            id_categoria = ?, nombre = ?, descripcion = ?, 
                            precio = ?, stock = ?, visible = ?, imagen_url = ?, codigo_barras = ?
                        WHERE id_accesorio = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $id_categoria, $nombre, $descripcion, $precio, 
                    $stock, $visible, $nombre_imagen_db, $codigo_barras, $id_accesorio_form
                ]);
                header('Location: accesorios.php?actualizado=exito');
                exit;

            } else {
                // SQL ACTUALIZADO con codigo_barras
                $sql = "INSERT INTO accesorios (id_categoria, nombre, descripcion, precio, stock, visible, imagen_url, codigo_barras) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $id_categoria, $nombre, $descripcion, $precio, 
                    $stock, $visible, $nombre_imagen_db, $codigo_barras
                ]);
                header('Location: accesorios.php?creado=exito');
                exit;
            }
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) { // Error de duplicado
                $error = "El código de barras ya está registrado en otro producto.";
            } else {
                $error = "Error en BD: " . $e->getMessage();
            }
        }
    }
}

// Cargar categorías
try {
    $stmt_cats = $pdo->query("SELECT * FROM categorias ORDER BY nombre");
    $categorias = $stmt_cats->fetchAll();
} catch (PDOException $e) { $error = "Error al cargar categorías."; $categorias = []; }
?>

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

<div class="form-container admin-form">
    <h2><?php echo htmlspecialchars($pagina_titulo); ?></h2>
    <a href="accesorios.php" class="enlace-volver">&larr; Volver a la lista</a>
    <hr>

    <?php if (!empty($error)) echo '<div class="alerta alerta-error">' . $error . '</div>'; ?>

    <form action="manejar_accesorio.php<?php echo $modo_edicion ? '?id='.$accesorio_actual['id_accesorio'] : ''; ?>" method="POST" enctype="multipart/form-data">
        <?php if ($modo_edicion): ?>
            <input type="hidden" name="id_accesorio" value="<?php echo $accesorio_actual['id_accesorio']; ?>">
        <?php endif; ?>
        
        <div class="form-grupo">
            <label for="codigo_barras">Código de Barras:</label>
            <div style="display: flex; gap: 10px;">
                <input type="text" id="codigo_barras" name="codigo_barras" 
                       placeholder="Escanear o escribir..."
                       value="<?php echo htmlspecialchars($accesorio_actual['codigo_barras']); ?>"
                       style="flex-grow: 1;">
                <button type="button" id="btn-escanear" class="btn btn-principal" style="padding: 10px;">
                    📷
                </button>
            </div>
            <div id="reader" style="width: 100%; margin-top: 10px; display:none;"></div>
        </div>
        <div class="form-grupo">
            <label for="id_categoria">Categoría:</label>
            <select name="id_categoria" id="id_categoria" class="form-control-select" required>
                <option value="">-- Selecciona una categoría --</option>
                <?php foreach ($categorias as $cat): ?>
                    <option value="<?php echo $cat['id_categoria']; ?>"
                        <?php if ($cat['id_categoria'] == $accesorio_actual['id_categoria']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($cat['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-grupo">
            <label for="nombre">Nombre del Accesorio:</label>
            <input type="text" id="nombre" name="nombre" required 
                   value="<?php echo htmlspecialchars($accesorio_actual['nombre']); ?>">
        </div>
        
        <div class="form-grupo">
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="3"><?php echo htmlspecialchars($accesorio_actual['descripcion']); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-grupo form-grupo-mitad">
                <label for="precio">Precio (MXN):</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required
                       value="<?php echo htmlspecialchars($accesorio_actual['precio']); ?>">
            </div>
            <div class="form-grupo form-grupo-mitad">
                <label for="stock">Cantidad en Stock:</label>
                <input type="number" id="stock" name="stock" step="1" min="0" required
                       value="<?php echo htmlspecialchars($accesorio_actual['stock']); ?>">
            </div>
        </div>

        <div class="form-grupo">
            <label for="imagen">Imagen:</label>
            <input type="file" id="imagen" name="imagen" class="form-control-file" accept="image/*">
            <input type="hidden" name="imagen_actual" value="<?php echo htmlspecialchars($accesorio_actual['imagen_url']); ?>">
            
            <?php if ($modo_edicion && !empty($accesorio_actual['imagen_url'])): ?>
                <div class="imagen-preview">
                    <img src="../uploads/<?php echo htmlspecialchars($accesorio_actual['imagen_url']); ?>" width="100">
                </div>
                <div class="form-grupo-checkbox">
                    <input type="checkbox" id="eliminar_imagen" name="eliminar_imagen" value="1">
                    <label for="eliminar_imagen">Quitar imagen</label>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-grupo-checkbox">
            <input type="checkbox" id="visible" name="visible" value="1" 
                <?php if ($accesorio_actual['visible'] == 1) echo 'checked'; ?>>
            <label for="visible">Visible en la tienda</label>
        </div>

        <div class="form-grupo">
            <button type="submit" class="btn btn-principal">
                <?php echo $modo_edicion ? 'Guardar Cambios' : 'Añadir Accesorio'; ?>
            </button>
        </div>
    </form>
</div>

<script>
    const btnEscanear = document.getElementById('btn-escanear');
    const readerDiv = document.getElementById('reader');
    const inputCodigo = document.getElementById('codigo_barras');
    let html5QrCode;

    btnEscanear.addEventListener('click', () => {
        // Si el lector ya está abierto, lo cerramos
        if (readerDiv.style.display === 'block') {
            html5QrCode.stop().then(() => {
                readerDiv.style.display = 'none';
                btnEscanear.textContent = '📷';
            }).catch(err => console.log(err));
            return;
        }

        // Abrimos el lector
        readerDiv.style.display = 'block';
        btnEscanear.textContent = '❌ Detener';
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 10, qrbox: { width: 250, height: 150 } };
        
        // Preferimos la cámara trasera ('environment')
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
    });

    function onScanSuccess(decodedText, decodedResult) {
        // Cuando lee un código:
        console.log(`Código escaneado = ${decodedText}`, decodedResult);
        
        // 1. Poner el valor en el input
        inputCodigo.value = decodedText;
        
        // 2. Detener la cámara y cerrar el div
        html5QrCode.stop().then(() => {
            readerDiv.style.display = 'none';
            btnEscanear.textContent = '📷';
            
            // Opcional: Hacer un sonido "beep"
            // var audio = new Audio('../assets/beep.mp3'); audio.play();
        }).catch(err => console.log(err));
    }
</script>

<?php
require '../includes/footer_admin.php';
?>