<?php
$version = '1.0';
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
try {
    $pdo = new PDO('mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_NAME') . ';charset=utf8mb4', getenv('DB_USER'), getenv('DB_PASSWORD'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim((string) ($_POST['nombre'] ?? ''));
        if ($nombre === '' || strlen($nombre) > 100) {
            http_response_code(400);
            exit('Introduce un nombre de entre 1 y 100 bytes.');
        }
        $stmt = $pdo->prepare('INSERT INTO mensajes (nombre) VALUES (?)');
        $stmt->execute([$nombre]);
        header('Location: /', true, 303);
        exit;
    }
    $mensajes = $pdo->query('SELECT id, nombre, creado FROM mensajes ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $ex) {
    error_log($ex->getMessage());
    http_response_code(503);
    exit('Base de datos no disponible.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Práctica CI/CD</title>
<style>body{font-family:system-ui;max-width:760px;margin:3rem auto;padding:0 1rem;color:#203040}table{border-collapse:collapse;width:100%}th,td{padding:.6rem;border-bottom:1px solid #ddd;text-align:left}input,button{padding:.6rem}small{color:#536477}</style></head>
<body>
<h1>Aplicación PHP - versión <?= e($version) ?></h1>
<p>Conexión con MySQL establecida.</p>
<form method="post"><label for="nombre">Nombre de prueba:</label> <input id="nombre" name="nombre" maxlength="100" required> <button>Guardar</button></form>
<h2>Registros guardados</h2>
<table><thead><tr><th>ID</th><th>Nombre</th><th>Fecha</th></tr></thead><tbody>
<?php foreach ($mensajes as $m): ?>
<tr><td><?= e((string)$m['id']) ?></td><td><?= e($m['nombre']) ?></td><td><?= e($m['creado']) ?></td></tr>
<?php endforeach; ?>
</tbody></table>
<p><small>Utiliza exclusivamente datos ficticios. Aplicación de laboratorio sin autenticación.</small></p>
</body></html>
