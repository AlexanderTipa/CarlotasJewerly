<?php
// 1. ¡IMPORTANTE! session_start() ahora debe ir aquí.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 2. Incluimos la conexión (esto no cambia)
require 'includes/db_conexion.php';

// 3. Redirección si el usuario YA está logueado
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['tipo_usuario'] == 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

// 4. Variable para manejar errores
$error = '';

// 5. Comprobar si el formulario fue enviado (Lógica de login)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    if (empty($correo) || empty($contrasena)) {
        $error = "Por favor, ingresa tu correo y contraseña.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE correo = ?");
            $stmt->execute([$correo]);
            $usuario = $stmt->fetch();

            if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
                session_regenerate_id(true);
                
                $_SESSION['id_usuario'] = $usuario['id_usuario'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

                if ($usuario['tipo_usuario'] == 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error = "Correo o contraseña incorrectos.";
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
    <title>Iniciar Sesión - Carlota's Jewelry</title>
    
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
        <h2>Iniciar Sesión</h2>
        <p>Accede a tu cuenta para ver tus pedidos.</p>
        <hr>

        <?php
        if (!empty($error)) {
            echo '<div class="alerta alerta-error">' . $error . '</div>';
        }
        if (isset($_GET['registro']) && $_GET['registro'] == 'exitoso') {
            echo '<div class="alerta alerta-exito">¡Registro exitoso! Ya puedes iniciar sesión.</div>';
        }
        ?>

        <form action="login.php" method="POST">
            
            <div class="form-grupo">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" name="correo" required 
                       value="<?php echo isset($correo) ? htmlspecialchars($correo) : ''; ?>">
            </div>

            <div class="form-grupo">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>
            
            <div class="form-grupo">
                <button type="submit" class="btn btn-principal">Entrar</button>
            </div>
        </form>

        <p class="enlace-alternativo">
            ¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>
    </div>

</body>
</html>