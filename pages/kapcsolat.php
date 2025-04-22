<?php include '../includes/header.php'; ?>
<h2>Kapcsolat</h2>

<div class="row">
    <div class="col-md-6">
        <h4>Elérhetőségeink</h4>
        <p><strong>Email:</strong> info@galgatabor.hu</p>
        <p><strong>Telefon:</strong> +36 30 123 4567</p>
        <p><strong>Cím:</strong> 1234 Budapest, Tábor utca 5.</p>
        
        <h4>Üzenetküldés</h4>
        <form class="form-control" action="process_message.php" method="POST">
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
        <h4>Hol találsz minket?</h4>
        <img src="/galgatabor/public/arduino_kid.jpg" alt="gyermek arduinoval" style="width=100% height=350px>">
    </div>
</div>

<?php include '../includes/footer.php'; ?>
