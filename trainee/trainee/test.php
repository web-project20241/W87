<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host = 'server244.web-hosting.com';  // Change to the SMTP server that matches the SSL certificate
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'trainer@trainingmanagment.lol';        // SMTP username
        $mail->Password   = 'ug}ljivy6k4h';                         // SMTP password
        $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 587;                                    // TCP port to connect to

        // Recipients
        $mail->setFrom('trainer@trainingmanagment.lol', 'Training Manager');

        // Get recipient address from user input
        $recipientEmail = $_POST['recipientEmail']; // Assuming you are using a form with POST method
        $recipientName = $_POST['recipientName'];   // Assuming you have a field for recipient's name

        if (!empty($recipientEmail) && filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            $mail->addAddress($recipientEmail, $recipientName);     // Add a recipient

            // Content
            $mail->isHTML(true);                                    // Set email format to HTML
            $mail->Subject = 'Test Email from Your Training Management System';
            $mail->Body    = 'This is a <strong>test email</strong> sent from your training management system.';
            $mail->AltBody = 'This is a test email sent from your training management system.';

            $mail->send();
            echo 'Message has been sent successfully.';
        } else {
            echo 'Invalid email address provided.';
        }
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
    }
} else {
    // Display the HTML form
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Send Email</title>
    </head>
    <body>
        <form action="" method="post">
            <label for="recipientEmail">Recipient Email:</label>
            <input type="email" id="recipientEmail" name="recipientEmail" required>
            <br>
            <label for="recipientName">Recipient Name:</label>
            <input type="text" id="recipientName" name="recipientName" required>
            <br>
            <button type="submit">Send Email</button>
        </form>
    </body>
    </html>';
}
?>
