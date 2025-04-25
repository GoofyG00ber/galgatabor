<?php
include '../includes/header.php';
?>
<h2>Táboraink 2025</h2>
<div class="row">
    <?php
    $result = $conn->query("SELECT * FROM camps");
    while ($camp = $result->fetch_assoc()) {
        echo '<div class="col-md-4 mb-3">';
        echo '<div class="card">';
        echo '<img src="../public/' . $camp['image'] . '" class="card-img-top" alt="' . $camp['name'] . '">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title">' . $camp['name'] . '</h5>';
        echo '<p class="card-text">' . substr($camp['description'], 0, 100) . '...</p>';
        echo '<a href="camp.php?id=' . $camp['id'] . '" class="btn btn-primary">Részletek</a>';
        echo '</div></div></div>';
    }
    ?>
</div>
<?php include '../includes/footer.php'; ?>
