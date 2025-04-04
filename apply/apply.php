<?php
include '../includes/header.php';

$camp_id = $_GET['camp_id'] ?? 0;
$query = "SELECT name FROM camps WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $camp_id);
$stmt->execute();
$result = $stmt->get_result();
$camp = $result->fetch_assoc();

if (!$camp) {
    echo "<h2>Invalid camp</h2>";
    include '../includes/footer.php';
    exit;
}
?>

<h1>Apply for <?= $camp['name']; ?></h1>
<form action="process_application.php" method="POST">
    <input type="hidden" name="camp_id" value="<?= $camp_id; ?>">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit Application</button>
</form>

<?php include '../includes/footer.php'; ?>
