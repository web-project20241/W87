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

$sessionId = $_GET['session_id'];

$sql = "SELECT t.id, t.name, t.last_name, ts.attendance
        FROM Trainee t
        JOIN Trainee_Session ts ON t.id = ts.trainee_id
        WHERE ts.session_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$sessionId]);
$trainees = $stmt->fetchAll();

echo json_encode($trainees);
?>
