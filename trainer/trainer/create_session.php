<?php
// Database connection settings
$host = 'localhost';
$dbname = 'traioxys_trainingdb';
$user = 'traioxys_safwan';
$pass = 'tS-mz5H#-pZF';
$charset = 'utf8mb4';

// Set up DSN
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

// Retrieve data from POST request
$name = $_POST['name'];
$date = $_POST['date'];
$hour = $_POST['hour'];
$num_of_trainees = $_POST['num_of_trainees']; // New field

// Get the trainer ID from the session or authentication mechanism
session_start();
$trainer_id = $_SESSION['trainer_id']; // Assuming the trainer ID is stored in the session

// Prepare SQL statement
$sql = "INSERT INTO TrainingSession (name, date, hour, num_of_trainees, trainer_id) VALUES (?, ?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);

// Execute the statement
if ($stmt->execute([$name, $date, $hour, $num_of_trainees, $trainer_id])) {
    echo "Training session created successfully!";
} else {
    echo "Failed to create training session.";
}
?>
