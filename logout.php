<?php
// 1. Iniciar la sesión
// Es necesario para poder acceder a la sesión actual y destruirla.
session_start();

// 2. Vaciar todas las variables de sesión
// Esto borra $_SESSION['id_usuario'], $_SESSION['nombre'], $_SESSION['tipo_usuario'], etc.
$_SESSION = array();

// 3. Destruir la cookie de sesión (Recomendado por seguridad)
// Esto le dice al navegador que "olvide" la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finalmente, destruir la sesión en el servidor.
// Esto borra el archivo de sesión del servidor.
session_destroy();

// 5. Redirigir al usuario a la página de login.
// (index.php también sería una buena opción, pero login.php es más claro)
header("Location: ./login.php");
exit; // ¡Importante! Detener la ejecución del script aquí.
?>