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

$traineeId = $_SESSION['trainee_id'];
$currentDateTime = date('Y-m-d H:i:s');

$sql = "SELECT ts.id, ts.date, ts.hour, CONCAT(t.name, ' ', t.lastname) AS trainer_name
        FROM TrainingSession ts
        JOIN Trainee_Session tsess ON ts.id = tsess.session_id
        JOIN Trainer t ON ts.trainer_id = t.id
        WHERE tsess.trainee_id = ? AND CONCAT(ts.date, ' ', ts.hour) < ?
        ORDER BY ts.date DESC, ts.hour DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$traineeId, $currentDateTime]);
$sessions = $stmt->fetchAll();

echo json_encode($sessions);
?>
