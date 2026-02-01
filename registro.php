<?php
// 1. ¡IMPORTANTE! session_start() ahora debe ir aquí.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Incluimos la conexión (esto no cambia)
require 'includes/db_conexion.php';

// 3. Redirección si el usuario YA está logueado
if (isset($_SESSION['id_usuario'])) {
    header('Location: index.php');
    exit;
}

// 4. Variables para manejar los mensajes
$error = '';
$exito = '';

// 5. Comprobar si el formulario fue enviado (Lógica de registro)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $correo = trim($_POST['correo']);
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    
    if (empty($nombre) || empty($apellido) || empty($correo) || empty($contrasena)) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } elseif (strlen($contrasena) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif ($contrasena !== $confirmar_contrasena) {
        $error = "Las contraseñas no coinciden.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario_existente = $stmt->fetch();

            if ($usuario_existente) {
                $error = "Este correo electrónico ya está registrado. <a href='login.php'>Inicia sesión</a>.";
            } else {
                $contrasena_hasheada = password_hash($contrasena, PASSWORD_DEFAULT);

                $sql = "INSERT INTO usuarios (nombre, apellido, correo, contrasena, tipo_usuario) 
                        VALUES (?, ?, ?, ?, 'cliente')";
                
                $stmt_insert = $pdo->prepare($sql);
                $stmt_insert->execute([$nombre, $apellido, $correo, $contrasena_hasheada]);

                $exito = "¡Cuenta creada con éxito! Ya puedes <a href='login.php'>iniciar sesión</a>.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Carlota's Jewelry</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body class="auth-page">

    <div class="auth-logo">
        <a href="index.php">Carlota's Jewelry</a>
    </div>

    <div class="form-container">
        <h2>Crear una Cuenta</h2>
        <p>Únete para una experiencia de compra más rápida.</p>

        <?php
        if (!empty($error)) {
            echo '<div class="alerta alerta-error">' . $error . '</div>';
        }
        if (!empty($exito)) {
            echo '<div class="alerta alerta-exito">' . $exito . '</div>';
        }
        ?>

        <?php if (empty($exito)): ?>
        <form action="registro.php" method="POST" class="form-registro">
            
            <div class="form-grupo">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>">
            </div>
            
            <div class="form-grupo">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required
                       value="<?php echo isset($apellido) ? htmlspecialchars($apellido) : ''; ?>">
            </div>

            <div class="form-grupo">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required
                       value="<?php echo isset($correo) ? htmlspecialchars($correo) : ''; ?>">
            </div>

            <div class="form-grupo">
                <label for="contrasena">Contraseña (mín. 8 caracteres):</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>

            <div class="form-grupo">
                <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
            </div>
            
            <div class="form-grupo">
                <button type="submit" class="btn btn-principal">Crear Cuenta</button>
            </div>
        </form>
        <?php endif; ?>

        <p class="enlace-alternativo">
            ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>
    </div>

</body>
</html>