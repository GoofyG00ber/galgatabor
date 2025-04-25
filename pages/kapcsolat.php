<?php include '../includes/header.php'; ?>

<style>
/* Custom border for form inputs and textarea */
.form-control {
    border: 1px solid gray !important; /* Gray border, 1px thick */
}

/* Ensure focus state maintains the same border color */
.form-control:focus {
    border-color: darkred; /* Dark red border on focus */
    box-shadow: 0 0 0 0.2rem rgba(250, 34, 34, 0.13); /* Bootstrap-style focus shadow */
}
</style>

<h2 class="mb-4">Kapcsolat</h2>

<div class="row">
    <div class="col-md-6">
        <h4 class="mb-4">Elérhetőségeink</h4>
        <p><strong>Email:</strong> <a href="mailto:info@galgatabor.hu">info@galgatabor.hu</a></p>
        <p><strong>Telefon:</strong> <a href="tel:+36702613126">+36 70 261 3126</a></p>
        
        <p>Kérdése van a jelentkezéssel vagy a táborokkal kapcsolatban? Írjon nekünk üzenetet!</p>
        <h4>Üzenetküldés</h4>
        <form action="process_message.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Név:</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Üzenet:</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Küldés</button>
        </form>
    </div>
    <div class="col-md-6">
        <img src="/galgatabor/public/arduino_kids_laptop.jpg" alt="Gyerekek laptopon arduinot programoznak" style="width: 100%; height: 100%; object-fit: cover;">
    </div>
</div>

<?php include '../includes/footer.php'; ?>