<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camp_id = $_POST['camp_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("INSERT INTO applications (camp_id, name, email) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $camp_id, $name, $email);
    if ($stmt->execute()) {
        echo "<script>alert('Application submitted!'); window.location.href = '/index.php';</script>";
    } else {
        echo "<script>alert('Error submitting application.'); history.back();</script>";
    }
}
?>
