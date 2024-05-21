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

$trainerId = $_SESSION['trainer_id'];

$sql = "SELECT ts.date, ts.hour, CONCAT(t.name, ' ', t.last_name) AS trainee_name, f.feedbackText
        FROM Feedback f
        JOIN TrainingSession ts ON f.session_id = ts.id
        JOIN Trainee t ON f.trainee_id = t.id
        WHERE ts.trainer_id = ?
        ORDER BY ts.date DESC, ts.hour DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$trainerId]);
$feedback = $stmt->fetchAll();

echo json_encode($feedback);
?>
