<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

header('Content-Type: application/json'); // Important: Set the content type as JSON

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

$traineeId = $_SESSION['trainee_id']; // Assuming trainee_id is stored in session
$traineeEmail = $_SESSION['trainee_email']; // Assuming trainee_email is stored in session
$traineeName = $_SESSION['trainee_name']; // Assuming trainee_name is stored in session

$sessionId = $_POST['sessionId'];

// Check the number of current trainees in the session
$sql = "SELECT COUNT(*) as current_trainees, num_of_trainees FROM TrainingSession 
        LEFT JOIN Trainee_Session ON TrainingSession.id = Trainee_Session.session_id 
        WHERE TrainingSession.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$sessionId]);
$result = $stmt->fetch();

if (!$result) {
    echo json_encode(['error' => 'Session not found.']);
    exit;
}

if ($result['current_trainees'] >= $result['num_of_trainees']) {
    echo json_encode(['error' => 'The session is full. No more trainees can be registered.']);
    exit;
}

$sql = "INSERT INTO Trainee_Session (trainee_id, session_id) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
try {
    $stmt->execute([$traineeId, $sessionId]);

    // Prepare meeting invitation details
    $sessionDateTime = new DateTime($session['date'] . ' ' . $session['hour']);
    $startDate = $sessionDateTime->format('Ymd\THis');
    $endDate = $sessionDateTime->modify('+1 hour')->format('Ymd\THis'); // Assuming session lasts 1 hour

    $ics = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Your Organization//NONSGML v1.0//EN
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
UID:" . uniqid() . "
DTSTAMP:" . gmdate('Ymd\THis\Z') . "
DTSTART:$startDate
DTEND:$endDate
SUMMARY:Training Session
DESCRIPTION:You have successfully registered for the training session.
LOCATION:Online
STATUS:CONFIRMED
SEQUENCE:0
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR";

    $icsFile = tempnam(sys_get_temp_dir(), 'meeting') . '.ics';
    file_put_contents($icsFile, $ics);

    // Send email to trainee
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'server244.web-hosting.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'trainer@trainingmanagment.lol';
        $mail->Password = 'ug}ljivy6k4h';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('trainer@trainingmanagment.lol', 'Training Manager');
        $mail->addAddress($traineeEmail, $traineeName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Training Session Registration Confirmation';
        $mail->Body    = 'Dear ' . htmlspecialchars($traineeName) . ',<br><br>You have successfully registered for the training session.<br><br>Thank you,<br>Training Management Team';
        $mail->AltBody = 'Dear ' . htmlspecialchars($traineeName) . ',\n\nYou have successfully registered for the training session.\n\nThank you,\nTraining Management Team';

        // Attach the ICS file
        $mail->addStringAttachment($ics, 'invitation.ics', 'base64', 'text/calendar; charset=utf-8; method=REQUEST');

        $mail->send();
        echo json_encode(['success' => 'Message has been sent successfully.']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
    }

    // Clean up temporary file
    unlink($icsFile);

} catch (\PDOException $e) {
    echo json_encode(['error' => 'Error registering for session: ' . $e->getMessage()]);
}
?>
