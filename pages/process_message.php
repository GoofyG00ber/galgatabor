<?php
include '../includes/db.php'; // Database connection
require '../vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        echo "<script>alert('Minden mezőt ki kell tölteni!'); window.history.back();</script>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Érvénytelen email cím!'); window.history.back();</script>";
        exit;
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        // Send email notification
        $mail = new PHPMailer(true);
        try {
            // Set character set to UTF-8
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64'; // Optional: ensures proper encoding of special characters

            // SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Email details
            $mail->setFrom(SMTP_FROM, 'Galgatábor');
            $mail->addAddress(SMTP_TO);
            $mail->Subject = 'Új Üzenet Érkezett';
            $mail->Body = "Új üzenet érkezett:\n\nNév: $name\nEmail: $email\nÜzenet:\n$message";

            
            // Send email
            $mail->send();
        } catch (Exception $e) {
            // Log error (don't interrupt user flow)
            error_log("Email sending failed: {$mail->ErrorInfo}");
        }

        echo "<script>alert('Üzenet sikeresen elküldve! Dolgozunk kérdése feldolgozásán, amint tehetjük válaszolunk önnek.'); window.location.href = '/pages/kapcsolat.php';</script>";
    } else {
        echo "<script>alert('Hiba történt, próbáld újra!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Érvénytelen kérés!'); window.history.back();</script>";
}
?>