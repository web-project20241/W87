<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'traioxys_trainingdb';
$user = 'traioxys_safwan';
$pass = 'tS-mz5H#-pZF';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

$sql = "SELECT ts.id, ts.date, ts.hour, CONCAT(t.name, ' ', t.lastname) AS trainer_name
        FROM TrainingSession ts
        JOIN Trainer t ON ts.trainer_id = t.id
        ORDER BY ts.date, ts.hour";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$sessions = $stmt->fetchAll();

echo json_encode($sessions);
?>
