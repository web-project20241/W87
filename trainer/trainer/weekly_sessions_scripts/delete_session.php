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

// Read data from request
$sessionId = $_POST['session_id'];

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Delete related records in trainee_session table
    $sql = "DELETE FROM Trainee_Session WHERE session_id = :session_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':session_id', $sessionId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Delete the session itself
    $sql = "DELETE FROM TrainingSession WHERE id = :session_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':session_id', $sessionId, PDO::PARAM_INT);
    $stmt->execute();
    
    // Commit transaction
    $pdo->commit();
    
    echo 'success';
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    echo 'error: ' . $e->getMessage();
}
?>
