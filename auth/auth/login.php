<?php
session_start(); // Start the session

// Database configuration
$host = 'localhost';
$dbname = 'traioxys_trainingdb';
$username = 'traioxys_safwan';
$password = 'tS-mz5H#-pZF';

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read data from request
$userType = $_POST['userType'];
$email = $conn->real_escape_string($_POST['email']);
$password = $_POST['password'];

// SQL to check the existence of user
if ($userType == 'trainer') {
    $sql = "SELECT id, email, name, password FROM Trainer WHERE email = '$email'";
} else {
    $sql = "SELECT id, email, name, password FROM Trainee WHERE email = '$email'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($password == $row['password']) { // Direct password comparison
        if ($userType == 'trainer') {
            $_SESSION['trainer_id'] = $row['id'];
            $_SESSION['trainer_email'] = $row['email'];
            $_SESSION['trainer_name'] = $row['name'];
            
            echo 'success_trainer';
        } else {
            $_SESSION['trainee_id'] = $row['id'];
            $_SESSION['trainee_email'] = $row['email'];
            $_SESSION['trainee_name'] = $row['name'];
            
            echo 'success_trainee';
        }
    } else {
        echo 'Invalid password.';
    }
} else {
    echo 'User does not exist.';
}

$conn->close();
?>
