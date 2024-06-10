<?php
session_start();
date_default_timezone_set('Asia/Jerusalem'); // Set the timezone to Israel

$host = 'localhost';
$dbname = 'traiyqgl_trainingdb';
$user = 'traiyqgl_liel';
$pass = 'Alufim123!!!!!';
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
    
    // Retrieve session details
    $sql = "SELECT date, hour FROM TrainingSession WHERE id = :session_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':session_id', $sessionId, PDO::PARAM_INT);
    $stmt->execute();
    $session = $stmt->fetch();

    if ($session) {
        $session_datetime = $session['date'] . ' ' . $session['hour'];
        $session_time = strtotime($session_datetime);
        $current_time = time();
        $three_hours = 3 * 60 * 60;

        // Check if the current time is less than 3 hours before the session start time
        if (($session_time - $current_time) < $three_hours) {
            echo "Cannot delete session less than 3 hours before it starts.";
            $pdo->rollBack();
            exit;
        }
    } else {
        echo "Session not found.";
        $pdo->rollBack();
        exit;
    }
    
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
