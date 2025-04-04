<?php
include '../includes/db.php'; // Database connection

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
        echo "<script>alert('Üzenet sikeresen elküldve!'); window.location.href = '../pages/kapcsolat.php';</script>";
    } else {
        echo "<script>alert('Hiba történt, próbáld újra!'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<script>alert('Érvénytelen kérés!'); window.history.back();</script>";
}
?>
