<?php
// 1. Configuración de la Base de Datos
// Estos son los datos para conectarte a tu base de datos MySQL.
// Asegúrate de que coincidan con tu configuración local o de tu hosting.

$host = 'sql100.infinityfree.com'; // O 'localhost'
$db   = 'if0_40337993_carlotas'; // El nombre de la BD que creamos
$user = 'if0_40337993'; // Tu usuario de MySQL (por defecto suele ser 'root')
$pass = 'Pn6AR944Bzr1Qz'; // Tu contraseña de MySQL (por defecto suele estar vacía)
$charset = 'utf8mb4'; // El set de caracteres para soportar acentos y emojis

// 2. Creación del DSN (Data Source Name)
// Esta es la "dirección" completa de la base de datos.
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. Opciones de PDO (Configuración de cómo se manejará la conexión)
$options = [
    // Queremos que PDO nos informe de errores graves (Excepciones)
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    
    // Queremos que los resultados vengan como arrays asociativos (ej. $fila['nombre'])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    
    // Desactivamos la emulación de sentencias preparadas (para seguridad extra)
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Intento de Conexión
try {
     // Aquí se crea el objeto de conexión PDO
     $pdo = new PDO($dsn, $user, $pass, $options);
     
} catch (\PDOException $e) {
     // Si la conexión falla, el script se detendrá y mostrará un error.
     // (En un sitio en producción, deberías registrar este error en lugar de mostrarlo)
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// ¡Listo! Si el script llega hasta aquí sin errores, 
// la variable $pdo está lista para ser usada en otros archivos.
?>