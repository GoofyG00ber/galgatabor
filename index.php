<?php include 'includes/header.php'; ?>
<h1 class="text-center">Available Camps</h1>
<div class="row">
    <?php
    $query = "SELECT * FROM camps";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?= $row['name']; ?></h5>
                    <p class="card-text"><?= substr($row['description'], 0, 100); ?>...</p>
                    <a href="/galgatabor/camps/camp.php?id=<?= $row['id']; ?>" class="btn btn-primary">View Camp</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<?php include 'includes/footer.php'; ?>
