<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/style.css">

<h2 class="text-center mb-4">Táboraink 2025</h2>
<div class="container">
    <?php
    $result = $conn->query("SELECT * FROM camps");
    $index = 0;
    while ($camp = $result->fetch_assoc()) {
        $imageSide = ($index % 2 == 0) ? 'left' : 'right';
        $textSide = ($index % 2 == 0) ? 'left' : 'right';
        ?>
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="row g-0 flex-column flex-md-row">
                        <!-- Image -->
                        <div class="col-md-6 order-md-<?php echo $imageSide == 'left' ? '1' : '2'; ?>">
                            <img src="/galgatabor/public/<?php echo $camp['image']; ?>" class="img-fluid rounded" alt="<?php echo $camp['name']; ?>">
                        </div>
                        <!-- Text Content -->
                        <div class="col-md-6 order-md-<?php echo $textSide == 'left' ? '1' : '2'; ?> d-flex align-items-center">
                            <div class="card-body p-4">
                                <!-- cim -->
                                <h2 class="card-title mb-3"><?php echo $camp['name']; ?></h2>
                                <!-- slogan -->
                                <h5 class="card-title mb-3"><?php echo $camp['slogan']; ?></h5>
                                <p class="card-text mb-2"><strong>Korosztály:</strong> <?php echo $camp['age_group']; ?></p>
                                <p class="card-text mb-4"><?php echo substr($camp['description'], 0, 250); ?>...</p>
                                <a href="/galgatabor/camps/camp.php?id=<?php echo $camp['id']; ?>" class="btn btn-danger">Olvass tovább!</a>
                                <a href="/galgatabor/camps/camp.php?id=<?php echo $camp['id']; ?>" class="btn btn-outline-danger">Helyszínek</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        $index++;
    }
    ?>
</div>
<?php include 'includes/footer.php'; ?>

<style>
.card {
    max-width: 100%;
    margin: 0 auto;
}
.card-img-top {
    object-fit: cover;
    height: 300px;
}
.card-body {
    text-align: justify; /* Sorkizárt szöveg */
}
.card-text, .card-title {
    text-align: left; /* Sorkizárt címek és szövegek */
}
@media (max-width: 767px) {
    .card-body {
        text-align: justify !important; /* Mobilon középre igazítás */
    }
    .card-text, .card-title {
        text-align: left !important; /* Mobilon középre igazítás */
    }
    .card-img-top {
        height: 250px;
        border-radius: 0.25rem 0.25rem 0 0 !important;
    }
}
</style>