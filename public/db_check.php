<?php
try {
    $host = getenv('DB_HOST') ?: 'db';
    $db   = getenv('DB_DATABASE') ?: 'MOKeys';
    $user = getenv('DB_USERNAME') ?: 'laravel';
    $pass = getenv('DB_PASSWORD') ?: 'laravel';

    echo "Intentando conectar a host: $host, db: $db, user: $user\n";

    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos.\n";

    // Verificar si hay tablas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "La base de datos está vacía (sin tablas).\n";
    } else {
        echo "Tablas encontradas: " . implode(", ", $tables) . "\n";
        
        // Verificar tabla products
        if (in_array('products', $tables)) {
             $stmt = $pdo->query("SELECT count(*) FROM products");
             $count = $stmt->fetchColumn();
             echo "Número de productos: $count\n";
        }
    }

} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage() . "\n";
}
