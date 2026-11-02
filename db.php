<?php
$host = 'db.leceisjbzpeztomdgcyb.supabase.co'; // Host mo
$port = '6543'; // Gamitin ang port 6543 (Transaction/Session pooler) sa halip na 5432
$db   = 'postgres';
$user = 'postgres.leceisjbzpeztomdgcyb'; // Siguraduhing kumpleto ang username mo sa Supabase
$pass = 'olaabdiel123'; // Password mo sa database

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        // Pilitin ang IPv4 resolution kung sinusuportahan ng PDO/driver
    ]);
} catch (\PDOException $e) {
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}
?>
