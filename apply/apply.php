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
    echo "<h2>Érvénytelen tábor</h2>";
    include '../includes/footer.php';
    exit;
}
?>

<h1>Jelentkezés a(z) <?= htmlspecialchars($camp['name']); ?> táborba</h1>
<form action="process_application.php" method="POST" enctype="multipart/form-data" class="application-form">
    <input type="hidden" name="camp_id" value="<?= htmlspecialchars($camp_id); ?>">

    <!-- Parent/Guardian Information -->
    <h3>Szülő/Gondviselő adatai</h3>
    <div class="mb-3">
        <label for="parent_name" class="form-label">Teljes név</label>
        <input type="text" name="parent_name" id="parent_name" class="form-control" required>
        <small>(GDPR Art. 6(1)(b) - Szerződéshez szükséges)</small>
    </div>
    <div class="mb-3">
        <label for="parent_address" class="form-label">Lakcím</label>
        <input type="text" name="parent_address" id="parent_address" class="form-control" required>
        <small>(GDPR Art. 6(1)(f) - Jogos érdek: vészhelyzet)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="parent_phone" class="form-label">Telefonszám</label>
            <input type="tel" name="parent_phone" id="parent_phone" class="form-control" required>
            <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
        </div>
        <div class="col-md-6">
            <label for="parent_email" class="form-label">Email cím</label>
            <input type="email" name="parent_email" id="parent_email" class="form-control" required>
            <small>(GDPR Art. 6(1)(a) - Hozzájárulás alább)</small>
        </div>
    </div>
    <div class="mb-3">
        <label for="parent_id_number" class="form-label">Személyi azonosító szám (személyi igazolványból)</label>
        <input type="text" name="parent_id_number" id="parent_id_number" class="form-control" style="max-width: 300px;" required>
        <small>(GDPR Art. 6(1)(b) - Azonosítás szerződéshez)</small>
    </div>

    <!-- Child Information -->
    <h3>Gyermek adatai</h3>
    <div class="mb-3">
        <label for="child_name" class="form-label">Teljes név</label>
        <input type="text" name="child_name" id="child_name" class="form-control" required>
        <small>(GDPR Art. 6(1)(b) - Szerződéshez szükséges)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="child_dob" class="form-label">Születési dátum</label>
            <input type="date" name="child_dob" id="child_dob" class="form-control" required>
            <small>(GDPR Art. 6(1)(b) - Életkor ellenőrzés)</small>
        </div>
        <div class="col-md-6">
            <label for="child_id_number" class="form-label">Személyi azonosító szám vagy születési anyakönyvi kivonat száma</label>
            <input type="text" name="child_id_number" id="child_id_number" class="form-control" required>
            <small>(GDPR Art. 6(1)(b) - Azonosítás)</small>
        </div>
    </div>

    <!-- Emergency Contact Information -->
    <h3>Vészhelyzeti elérhetőségek</h3>
    <div class="mb-3">
        <label for="emergency_name" class="form-label">Név</label>
        <input type="text" name="emergency_name" id="emergency_name" class="form-control" required>
        <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="emergency_relationship" class="form-label">Kapcsolat a gyermekkel</label>
            <input type="text" name="emergency_relationship" id="emergency_relationship" class="form-control" required>
            <small>(GDPR Art. 6(1)(d) - Vészhelyzeti azonosítás)</small>
        </div>
        <div class="col-md-6">
            <label for="emergency_phone1" class="form-label">1. Vészhelyzeti telefonszám</label>
            <input type="tel" name="emergency_phone1" id="emergency_phone1" class="form-control" required>
            <small>(GDPR Art. 6(1)(d) - Gyors elérhetőség)</small>
        </div>
    </div>
    <div class="mb-3">
        <label for="emergency_phone2" class="form-label">2. Vészhelyzeti telefonszám</label>
        <input type="tel" name="emergency_phone2" id="emergency_phone2" class="form-control" style="max-width: 300px;" required>
        <small>(GDPR Art. 6(1)(d) - Tartalék elérhetőség)</small>
    </div>

    <!-- Health Information -->
    <h3>Egészségügyi információk</h3>
    <div class="mb-3">
        <label for="medical_notes" class="form-label">Egészségügyi megjegyzések (allergiák, gyógyszerek, betegségek)</label>
        <textarea name="medical_notes" id="medical_notes" class="form-control" rows="4" placeholder="Pl. ételallergia, asztma, napi gyógyszer"></textarea>
        <small>(GDPR Art. 9(2)(i) - Egészségvédelem, hozzájárulás alább)</small>
    </div>

    <!-- Consents -->
    <h3>Hozzájárulások</h3>
    <div class="mb-3 form-check">
        <input type="checkbox" name="contact_consent" id="contact_consent" class="form-check-input" required>
        <label for="contact_consent" class="form-check-label">Hozzájárok, hogy a tábor emailben vagy telefonon kapcsolatba lépjen velem a gyermekem részvételével kapcsolatban.</label>
        <small>(GDPR Art. 6(1)(a) - Kommunikációs hozzájárulás)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="photo_consent" id="photo_consent" class="form-check-input">
        <label for="photo_consent" class="form-check-label">Hozzájárok, hogy a gyermekemről készült fotók/videók a tábor promóciójára (pl. weboldal) felhasználásra kerüljenek.</label>
        <small>(GDPR Art. 6(1)(a) - Opcionális hozzájárulás)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="health_consent" id="health_consent" class="form-check-input" required>
        <label for="health_consent" class="form-check-label">Hozzájárok a gyermekem egészségügyi adatainak kezelésére a biztonsága érdekében.</label>
        <small>(GDPR Art. 9(2)(a) - Különleges adatkezelés)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="liability_waiver" id="liability_waiver" class="form-check-input" required>
        <label for="liability_waiver" class="form-check-label">Tudomásul veszem, hogy a tábori tevékenységek kockázatokkal járnak, és a tábort súlyos gondatlanság kivételével mentesítem a felelősség alól.</label>
        <small>(GDPR Art. 6(1)(b) - Szerződéses feltétel)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="aszf_acceptance" id="aszf_acceptance" class="form-check-input" required>
        <label for="aszf_acceptance" class="form-check-label">Elfogadom az <a href="/galgatabor/aszf.pdf" target="_blank">Általános Szerződési Feltételeket (ÁSZF)</a>.</label>
        <small>(GDPR Art. 6(1)(b) - Szerződés alapja)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="guardian_confirmation" id="guardian_confirmation" class="form-check-input" required>
        <label for="guardian_confirmation" class="form-check-label">Kijelentem, hogy én vagyok a gyermek törvényes gondviselője, és az adatok pontosak.</label>
        <small>(GDPR Art. 8 - Gyermek adatkezelés feltétele)</small>
    </div>

    <!-- Additional Notes -->
    <h3>További megjegyzések</h3>
    <div class="mb-3">
        <label for="additional_notes" class="form-label">Egyéb információk (opcionális)</label>
        <textarea name="additional_notes" id="additional_notes" class="form-control" rows="3"></textarea>
        <small>(GDPR Art. 6(1)(a) - Önkéntes adat)</small>
    </div>

    <!-- Privacy Notice -->
    <p class="mb-3">Adatainak célja a gyermek biztonsága és a tábor működése, az GDPR és a 2011. évi CXII. tv. szerint. Biztonságosan tároljuk, és a tábor végén töröljük, hacsak a törvény nem ír elő mást. További részletek: <a href="/privacy-policy">Adatvédelmi szabályzat</a> és <a href="/aszf">ÁSZF</a>.
    <small>(GDPR Art. 13 - Átláthatósági követelmény)</small></p>

    <!-- Submit -->
    <button type="submit" class="btn btn-primary">Jelentkezés elküldése</button>
</form>

<?php include '../includes/footer.php'; ?>