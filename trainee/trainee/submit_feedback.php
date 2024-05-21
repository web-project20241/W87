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
$sessionId = $_POST['sessionId'];
$feedbackText = $_POST['feedbackText'];

$sql = "INSERT INTO Feedback (session_id, trainee_id, feedbackText) VALUES (?, ?, ?)";
$stmt = $pdo->prepare($sql);
if ($stmt->execute([$sessionId, $traineeId, $feedbackText])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to submit feedback']);
}
?>
