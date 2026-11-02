<?php
$host = 'db.leceisjbzpeztomdgcyb.supabase.co';
$port = '6543';
$db   = 'postgres';
$user = 'postgres.leceisjbzpeztomdgcyb';
$pass = 'olaabdiel123';

$dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10
    ]);
} catch (\PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}
?>
