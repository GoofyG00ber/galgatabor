<?php
include '../includes/header.php';

$id = $_GET['id'] ?? 0;
$query = "SELECT * FROM camps WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$camp = $result->fetch_assoc();

if (!$camp) {
    echo "<h2>Camp not found</h2>";
    include '../includes/footer.php';
    exit;
}
?>
<h1><?= $camp['name']; ?></h1>
<p><?= $camp['description']; ?></p>
<a href="/galgatabor/apply/apply.php?camp_id=<?= $camp['id']; ?>" class="btn btn-success">Apply Now</a>
<?php include '../includes/footer.php'; ?>
