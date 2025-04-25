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
    echo "<div class='container mt-5'><h2>Tábor nem található</h2></div>";
    include '../includes/footer.php';
    exit;
}
?>

<div class="container my-5">
    <div class="row mb-5">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image']); ?>" alt="<?= htmlspecialchars($camp['name']); ?>" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <h1 class="mb-4"><?= htmlspecialchars($camp['name']); ?></h1>
            <p><?= nl2br(htmlspecialchars($camp['description'])); ?></p>

            <ul class="list-group list-group-flush my-4">
                <li class="list-group-item"><strong>Időpont:</strong> <?= htmlspecialchars($camp['date']); ?></li>
                <li class="list-group-item"><strong>Helyszín:</strong> <?= htmlspecialchars($camp['location']); ?></li>
                <li class="list-group-item"><strong>Korosztály:</strong> <?= htmlspecialchars($camp['age_group']); ?></li>
                <li class="list-group-item"><strong>Tábor ára:</strong> <?= htmlspecialchars($camp['price']); ?> Ft</li>
                <li class="list-group-item"><strong>Étkeztetés ára:</strong> <?= htmlspecialchars($camp['meal_price']); ?> Ft</li>
            </ul>

            <a href="/galgatabor/apply/apply.php?camp_id=<?= $camp['id']; ?>" class="btn btn-success btn-lg">Jelentkezés</a>
        </div>
    </div>

    <!-- Content block 1 -->
    <div class="row align-items-center my-5">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image2']); ?>" alt="Kép 2" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <p><?= nl2br(htmlspecialchars($camp['content2'])); ?></p>
        </div>
    </div>

    <!-- Content block 2 (image right) -->
    <div class="row align-items-center my-5 flex-md-row-reverse">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image3']); ?>" alt="Kép 3" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <p><?= nl2br(htmlspecialchars($camp['content3'])); ?></p>
        </div>
    </div>

    <!-- Content block 3 -->
    <div class="row align-items-center my-5">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image4']); ?>" alt="Kép 4" class="img-fluid rounded shadow">
        </div>
        <div class="col-md-6">
            <p><?= nl2br(htmlspecialchars($camp['content4'])); ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
