<?php include '../includes/header.php'; ?>
<h2>Kapcsolat</h2>

<div class="row">
    <div class="col-md-6">
        <h4>Elérhetőségeink</h4>
        <p><strong>Email:</strong> info@galgatabor.hu</p>
        <p><strong>Telefon:</strong> +36 30 123 4567</p>
        <p><strong>Cím:</strong> 1234 Budapest, Tábor utca 5.</p>
        
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
        <h4>Hol találsz minket?</h4>
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3813.0125269575037!2d19.05801858668922!3d47.48588040839365!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4741dc502488be69%3A0xc5b7e757fb438129!2sBudapesti%20Corvinus%20Egyetem!5e0!3m2!1shu!2shu!4v1743767793573!5m2!1shu!2shu" 
            width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy">
        </iframe>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
