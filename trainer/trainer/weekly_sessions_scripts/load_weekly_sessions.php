<?php
session_start();
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
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$sql = "SELECT ts.id, ts.name, DATE(ts.date) AS session_date, TIME_FORMAT(ts.hour, '%H:%i') AS session_time, t.name AS trainer_name
        FROM TrainingSession ts
        JOIN Trainer t ON ts.trainer_id = t.id
        WHERE YEARWEEK(ts.date, 1) = YEARWEEK(CURDATE(), 1)
        ORDER BY ts.date, ts.hour";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$sessions = $stmt->fetchAll();

$weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
$schedule = [];

foreach ($sessions as $session) {
    $dayOfWeek = date('l', strtotime($session['session_date']));
    $time = $session['session_time'];
    $schedule[$time][$dayOfWeek][] = [
        'id' => $session['id'],
        'name' => $session['name'],
        'trainer_name' => $session['trainer_name']
    ];
}

header('Content-Type: application/json');
echo json_encode($schedule);
?>
