<?php
$pagina_titulo = "Añadir Nuevo Admin"; // Título por defecto
require '../includes/db_conexion.php'; 
require '../includes/header_admin.php';

// ¡SEGURIDAD!
if (!isset($_SESSION['id_usuario']) || $_SESSION['tipo_usuario'] != 'admin') {
    header('Location: /carlotas_jewelry/login.php');
    exit;
}

// --- Lógica de Manejo ---
$modo_edicion = false;
$admin_actual = ['id_usuario' => null, 'nombre' => '', 'apellido' => '', 'correo' => ''];
$error = '';
$exito = '';

// --- MODO EDICIÓN (GET) ---
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $modo_edicion = true;
    $id_admin = $_GET['id'];
    $pagina_titulo = "Editar Administrador";

    $stmt = $pdo->prepare("SELECT id_usuario, nombre, apellido, correo FROM usuarios WHERE id_usuario = ? AND tipo_usuario = 'admin'");
    $stmt->execute([$id_admin]);
    $admin_actual = $stmt->fetch();

    if (!$admin_actual) {
        $error = "El administrador no existe.";
        $modo_edicion = false; 
    }
}

// --- PROCESAR FORMULARIO (POST) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena']; // Contraseña en texto plano

    try {
        if ($modo_edicion) {
            // --- MODO EDICIÓN (UPDATE) ---
            $id_admin_form = $_POST['id_admin'];
            
            // --- Lógica de Contraseña ---
            if (!empty($contrasena)) {
                // Si el campo de contraseña NO está vacío, hashear la nueva
                $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ?, contrasena = ? 
                        WHERE id_usuario = ? AND tipo_usuario = 'admin'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $apellido, $correo, $contrasena_hasheada, $id_admin_form]);
            } else {
                // Si el campo de contraseña ESTÁ vacío, no actualizarla
                $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, correo = ? 
                        WHERE id_usuario = ? AND tipo_usuario = 'admin'";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $apellido, $correo, $id_admin_form]);
            }
            
            header('Location: gestionar_admins.php?actualizado=exito');
            exit;

        } else {
            // --- MODO AÑADIR (INSERT) ---
            if (empty($contrasena)) {
                $error = "La contraseña es obligatoria para crear un nuevo administrador.";
            } else {
                $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (nombre, apellido, correo, contrasena, tipo_usuario) 
                        VALUES (?, ?, ?, ?, 'admin')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nombre, $apellido, $correo, $contrasena_hasheada]);
                
                header('Location: gestionar_admins.php?creado=exito');
                exit;
            }
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // Error de 'Entrada duplicada' (correo)
            $error = "Error: El correo electrónico '" . $correo . "' ya está en uso.";
        } else {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<div class="form-container admin-form">
    <h2><?php echo htmlspecialchars($pagina_titulo); ?></h2>
    
    <a href="gestionar_admins.php" class="enlace-volver">&larr; Volver a la lista</a>
    <hr>

    <?php
    if (!empty($error)) echo '<div class="alerta alerta-error">' . $error . '</div>';
    ?>

    <form action="manejar_admin.php<?php echo $modo_edicion ? '?id='.$admin_actual['id_usuario'] : ''; ?>" method="POST">
        
        <?php if ($modo_edicion): ?>
            <input type="hidden" name="id_admin" value="<?php echo $admin_actual['id_usuario']; ?>">
        <?php endif; ?>
        
        <div class="form-row">
            <div class="form-grupo form-grupo-mitad">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo htmlspecialchars($admin_actual['nombre']); ?>">
            </div>
            <div class="form-grupo form-grupo-mitad">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required
                       value="<?php echo htmlspecialchars($admin_actual['apellido']); ?>">
            </div>
        </div>
        
        <div class="form-grupo">
            <label for="correo">Correo Electrónico:</label>
            <input type="email" id="correo" name="correo" required
                   value="<?php echo htmlspecialchars($admin_actual['correo']); ?>">
        </div>
        
        <div class="form-grupo">
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" 
                   <?php echo !$modo_edicion ? 'required' : ''; ?>>
            <p class="form-helper-text">
                <?php echo $modo_edicion 
                    ? 'Déjalo en blanco si no quieres cambiar la contraseña.' 
                    : 'La contraseña es obligatoria.'; 
                ?>
            </p>
        </div>
        
        <div class="form-grupo">
            <button type="submit" class="btn btn-principal">
                <?php echo $modo_edicion ? 'Guardar Cambios' : 'Crear Administrador'; ?>
            </button>
        </div>
    </form>
</div>

<?php
require '../includes/footer_admin.php';
?>