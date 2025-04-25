<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="css/style.css">


<div id="carouselExampleIndicators" class="carousel slide">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
  </div>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="public/banner1.png" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="public/arduino_kids_laptop.jpg" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="public/arduino_kep3.jpg" class="d-block w-100" alt="...">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
</div>







<h1 class="text-center mt-5">Táboraink 2025</h1>
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
