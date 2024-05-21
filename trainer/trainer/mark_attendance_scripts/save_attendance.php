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

$data = json_decode(file_get_contents('php://input'), true);
$sessionId = $data['session_id'];
$attendance = $data['attendance'];

foreach ($attendance as $record) {
    $traineeId = $record['trainee_id'];
    $attended = $record['attended'] ? 1 : 0;

    $sql = "UPDATE Trainee_Session SET attendance = ? WHERE session_id = ? AND trainee_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$attended, $sessionId, $traineeId]);
}

echo json_encode(['success' => true]);
?>
