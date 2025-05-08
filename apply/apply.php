<?php
include '../includes/header.php';

// Összes tábor lekérdezése a választáshoz, beleértve az árakat
$query = "SELECT id, name, date, date2, date3, location, location2, location3, price, meal_price FROM camps";
$result = $conn->query($query);
$camps = [];
while ($row = $result->fetch_assoc()) {
    $camps[] = $row;
}

// Alapértelmezett tábor kiválasztása, ha a camp_id meg van adva
$camp_id = $_GET['camp_id'] ?? '';
$query = "SELECT id, name, price, meal_price FROM camps WHERE id = ?";
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

// Alapértelmezett árak a kiválasztott tábor alapján
$base_price = $camp['price']; // Alapértelmezett érték, ha nincs az adatbázisban
$meal_price = $camp['meal_price']; // Alapértelmezett érték, ha nincs az adatbázisban
?>

<style>
/* Custom border for form inputs and textarea */
.form-control {
    border: 1px solid gray !important; /* Gray border, 1px thick */
}

/* Ensure focus state maintains the same border color */
.form-control:focus {
    border-color: darkred; /* Dark red border on focus */
    box-shadow: 0 0 0 0.2rem rgba(250, 192, 34, 0.13); /* Bootstrap-style focus shadow */
}

.form-control::placeholder {
    color: lightgray;
}

/* Style for form-select to ensure visible dropdown arrow */
.form-select {
    border: 1px solid gray !important;
    max-width: 300px; /* Limit width on larger screens */
}

/* Ensure focus state for form-select */
.form-select:focus {
    border-color: darkred;
    box-shadow: 0 0 0 0.2rem rgba(250, 34, 34, 0.13);
}

/* Táblázat stílusok */
.table thead {
    background-color: #8B0000; /* Sötétvörös háttér a címsornak */
    color: #FFFFFF; /* Fehér szöveg a kontraszt miatt */
}
.table tbody {
    background-color: #F8F9FA; /* Világosszürke háttér a tartalomnak */
}
.table-bordered th, .table-bordered td {
    border: 1px solid #DEE2E6; /* Szegélyek egységesítése */
}
.table {
    width: auto !important; /* A táblázat csak a tartalomhoz szükséges szélességet foglalja */
    margin: 0 auto; /* Középre igazítás */
}
</style>

<h1>Jelentkezési lap</h1>

<!-- Táborválasztó blokk -->
<h3 class="mt-5">Táboraink</h3>

<form action="process_application.php" method="POST" enctype="multipart/form-data" class="application-form">

<div class="table-responsive d-flex justify-content-center mb-4">
    <table class="table table-bordered">
        <thead>
            <tr class="table-dark">
                <th>Tábor neve</th>
                <th>Időpont</th>
                <th>Helyszín</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($camps as $camp_row): ?>
                <?php
                $dates = array_filter([$camp_row['date'], $camp_row['date2'] ?? '', $camp_row['date3'] ?? ''], function($date) {
                    return !empty($date);
                });
                $locations = array_filter([$camp_row['location'], $camp_row['location2'] ?? '', $camp_row['location3'] ?? ''], function($location) {
                    return !empty($location);
                });
                $maxRows = max(count($dates), count($locations));
                for ($i = 0; $i < $maxRows; $i++):
                    $date = $dates[$i] ?? ($dates[0] ?? '');
                    $location = $locations[$i] ?? ($locations[0] ?? '');
                ?>
                <tr>
                    <td><?= htmlspecialchars($camp_row['name']); ?></td>
                    <td><?= htmlspecialchars($date); ?></td>
                    <td><?= htmlspecialchars($location); ?></td>
                </tr>
                <?php endfor; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <label for="selected_camp_id" class="form-label"><strong>Válassz tábort:</strong></label>
        <select name="selected_camp_id" id="selected_camp_id" class="form-select" onchange="updateCampDetails()" required>
            <option value="">-- Válassz tábort --</option>
            <?php foreach ($camps as $camp_row): ?>
                <option value="<?= $camp_row['id']; ?>" <?= $camp_row['id'] == $camp_id ? 'selected' : ''; ?>>
                    <?= htmlspecialchars($camp_row['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-6">
        <label for="selected_date" class="form-label"><strong>Válassz időpontot:</strong></label>
        <select name="selected_date" id="selected_date" class="form-select" required>
            <option value="">-- Előbb válassz tábort --</option>
        </select>
    </div>
</div>

<h1 id="camp_title">Jelentkezés a(z) <?= htmlspecialchars($camp['name']); ?> táborba</h1>
    <input type="hidden" name="camp_id" id="camp_id_hidden" value="<?= htmlspecialchars($camp_id); ?>">

    <!-- Parent/Guardian Information -->
    <h3 class="mt-5">Szülő/Gondviselő adatai</h3>
    <div class="mb-3">
        <label for="parent_name" class="form-label">Teljes név</label>
        <input type="text" name="parent_name" id="parent_name" class="form-control" placeholder="Gipsz Jakab" required>
        <small>(GDPR Art. 6(1)(b) - Szerződéshez szükséges)</small>
    </div>
    <div class="mb-3">
        <label for="parent_address" class="form-label">Lakcím</label>
        <input type="text" name="parent_address" id="parent_address" class="form-control" placeholder="1234 Budapest, Példa utca 5." required>
        <small>(GDPR Art. 6(1)(f) - Jogos érdek: vészhelyzet)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="parent_phone" class="form-label">Telefonszám</label>
            <input type="tel" name="parent_phone" id="parent_phone" class="form-control" placeholder="+36 30 123 4567" required>
            <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
        </div>
        <div class="col-md-6">
            <label for="parent_email" class="form-label">Email cím</label>
            <input type="email" name="parent_email" id="parent_email" class="form-control" placeholder="pelda@email.com" required>
            <small>(GDPR Art. 6(1)(a) - Hozzájárulás alább)</small>
        </div>
    </div>
    <div class="mb-3">
        <label for="parent_id_number" class="form-label">Személyi azonosító szám (személyi igazolványból)</label>
        <input type="text" name="parent_id_number" id="parent_id_number" class="form-control" style="max-width: 300px;" placeholder="123456AB" required>
        <small>(GDPR Art. 6(1)(b) - Azonosítás szerződéshez)</small>
    </div>

    <!-- Child Information -->
    <h3>Gyermek adatai</h3>
    <div class="mb-3">
        <label for="child_name" class="form-label">Teljes név</label>
        <input type="text" name="child_name" id="child_name" class="form-control" placeholder="Gipsz Janka" required>
        <small>(GDPR Art. 6(1)(b) - Szerződéshez szükséges)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="child_dob" class="form-label">Születési dátum</label>
            <input type="date" name="child_dob" id="child_dob" class="form-control" placeholder="ÉÉÉÉ-HH-NN" required>
            <small>(GDPR Art. 6(1)(b) - Életkor ellenőrzés)</small>
        </div>
        <div class="col-md-6">
            <label for="child_id_number" class="form-label">Személyi azonosító szám vagy születési anyakönyvi kivonat száma</label>
            <input type="text" name="child_id_number" id="child_id_number" class="form-control" placeholder="654321CD" required>
            <small>(GDPR Art. 6(1)(b) - Azonosítás)</small>
        </div>
    </div>

    <!-- Billing Information -->
    <h3>Számlázási adatok</h3>
    <div class="mb-3">
        <label for="billing_type" class="form-label">Számlázási típus</label>
        <select name="billing_type" id="billing_type" class="form-select" onchange="toggleBillingFields()" required>
            <option value="individual">Magánszemély</option>
            <option value="company">Cég</option>
        </select>
        <small>(GDPR Art. 6(1)(b) - Szerződéshez szükséges azonosítás)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="same_as_parent" id="same_as_parent" class="form-check-input" onchange="handleSameAsParent()">
        <label for="same_as_parent" class="form-check-label">A számlázási adatok megegyeznek a szülő/gondviselő adataival (csak magánszemély esetén)</label>
        <small>(GDPR Art. 6(1)(b) - Számlázási adatok egyszerűsítése)</small>
    </div>
    <div id="billing_individual_fields">
        <div class="mb-3">
            <label for="billing_name" class="form-label">Teljes név</label>
            <input type="text" name="billing_name" id="billing_name" class="form-control" placeholder="Gipsz Jakab" required>
            <small>(GDPR Art. 6(1)(b) - Számlázáshoz szükséges)</small>
        </div>
        <div class="mb-3">
            <label for="billing_address" class="form-label">Számlázási cím</label>
            <input type="text" name="billing_address" id="billing_address" class="form-control" placeholder="1234 Budapest, Példa utca 5." required>
            <small>(GDPR Art. 6(1)(b) - Számlázáshoz szükséges)</small>
        </div>
    </div>
    <div id="billing_company_fields" style="display: none;">
        <div class="mb-3">
            <label for="billing_company_name" class="form-label">Cég neve</label>
            <input type="text" name="billing_company_name" id="billing_company_name" class="form-control" placeholder="Példa Kft." required>
            <small>(GDPR Art. 6(1)(b) - Számlázáshoz szükséges)</small>
        </div>
        <div class="mb-3">
            <label for="billing_tax_number" class="form-label">Adószám</label>
            <input type="text" name="billing_tax_number" id="billing_tax_number" class="form-control" placeholder="12345678-1-23" required>
            <small>(GDPR Art. 6(1)(b) - Számlázáshoz szükséges azonosítás)</small>
        </div>
        <div class="mb-3">
            <label for="billing_company_address" class="form-label">Székhely címe</label>
            <input type="text" name="billing_company_address" id="billing_company_address" class="form-control" placeholder="1234 Budapest, Példa utca 5." required>
            <small>(GDPR Art. 6(1)(b) - Számlázáshoz szükséges)</small>
        </div>
    </div>

    <!-- Meal Option -->
    <h3>Étkeztetés</h3>
    <div class="mb-3 form-check">
        <input type="checkbox" name="meal_option" id="meal_option" class="form-check-input" onchange="updateTotalPrice()">
        <label for="meal_option" class="form-check-label">Igényelek napi egyszeri étkeztetést (ebéd), ára <span id="meal_price_display"><?= number_format($meal_price, 0, ',', ' '); ?></span> Ft.</label>
        <small>(GDPR Art. 6(1)(b) - Szerződéses szolgáltatás)</small>
    </div>
    <div style="display:none">
    <!-- Emergency Contact Information -->
    <h3>Vészhelyzeti elérhetőségek</h3>
    <div class="mb-3">
        <label for="emergency_name_1" class="form-label">Név</label>
        <input type="text" name="emergency_name_1" id="emergency_name_1" class="form-control" placeholder="Kovács Béla" >
        <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="emergency_relationship_1" class="form-label">Kapcsolat a gyermekkel</label>
            <input type="text" name="emergency_relationship_1" id="emergency_relationship_1" class="form-control" placeholder="Pl.: nagyszülő, nagybácsi, nagynéni, rokon" value=" ">
            <small>(GDPR Art. 6(1)(d) - Vészhelyzeti azonosítás)</small>
        </div>
        <div class="col-md-6">
            <label for="emergency_phone1" class="form-label">1. Vészhelyzeti telefonszám</label>
            <input type="tel" name="emergency_phone1" id="emergency_phone1" class="form-control" placeholder="+36 70 987 6543" value=" ">
            <small>(GDPR Art. 6(1)(d) - Gyors elérhetőség)</small>
        </div>
    </div>
    <div class="mb-3">
        <label for="emergency_name_2" class="form-label">Név</label>
        <input type="text" name="emergency_name_2" id="emergency_name_2" class="form-control" placeholder="Tóth Anna" value=" ">
        <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="emergency_relationship_2" class="form-label">Kapcsolat a gyermekkel</label>
            <input type="text" name="emergency_relationship_2" id="emergency_relationship_2" class="form-control" placeholder="Pl.: nagyszülő, nagybácsi, nagynéni, rokon" value=" ">
            <small>(GDPR Art. 6(1)(d) - Vészhelyzeti azonosítás)</small>
        </div>
        <div class="col-md-6">
            <label for="emergency_phone2" class="form-label">2. Vészhelyzeti telefonszám</label>
            <input type="tel" name="emergency_phone2" id="emergency_phone2" class="form-control" style="max-width: 300px;" placeholder="+36 20 456 7890" value=" ">
            <small>(GDPR Art. 6(1)(d) - Tartalék elérhetőség)</small>
        </div>
    </div>
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
        <label for="contact_consent" class="form-check-label">Hozzájárulok, hogy a tábor emailben vagy telefonon kapcsolatba lépjen velem a gyermekem részvételével kapcsolatban.</label>
        <small>(GDPR Art. 6(1)(a) - Kommunikációs hozzájárulás)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="photo_consent" id="photo_consent" class="form-check-input">
        <label for="photo_consent" class="form-check-label">Hozzájárulok, hogy a gyermekemről készült fotók/videók a tábor promóciójára (pl. Emlék fotógaléria készítése a weboldalra) felhasználásra kerüljenek.</label>
        <small>(GDPR Art. 6(1)(a) - Opcionális hozzájárulás)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="health_consent" id="health_consent" class="form-check-input" required>
        <label for="health_consent" class="form-check-label">Hozzájárulok a gyermekem egészségügyi adatainak kezelésére a biztonsága érdekében.</label>
        <small>(GDPR Art. 9(2)(a) - Különleges adatkezelés)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="liability_waiver" id="liability_waiver" class="form-check-input" required>
        <label for="liability_waiver" class="form-check-label">Tudomásul veszem, hogy a tábori tevékenységek (pl. sport, kézműves foglalkozások) bizonyos kockázatokkal járhatnak. Hozzájárulok, hogy gyermekem ezekben a tevékenységekben részt vegyen, és elfogadom, hogy a tábor szervezőjét nem terheli felelősség azokért a balesetekért vagy károkért, amelyek a tábor rendeltetésszerű működése során, a szervezők részéről nem felróható okokból következnek be. A tábor szervezője nem mentesül a felelősség alól szándékos vagy súlyos gondatlanságból eredő balesetek esetén.</label>
        <small>(GDPR Art. 6(1)(b) - Szerződéses feltétel)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="aszf_acceptance" id="aszf_acceptance" class="form-check-input" required>
        <label for="aszf_acceptance" class="form-check-label">Elfogadom az <a href="../aszf.pdf" target="_blank"><strong>Általános Szerződési Feltételeket (ÁSZF)</strong></a>.</label>
        <small>(GDPR Art. 6(1)(b) - Szerződés alapja)</small>
    </div>
    <div class="mb-3 form-check">
        <input type="checkbox" name="guardian_confirmation" id="guardian_confirmation" class="form-check-input" required>
        <label for="guardian_confirmation" class="form-check-label">Kijelentem, hogy én vagyok a gyermek törvényes gondviselője, és jogosult vagyok a nevében eljárni. Kijelentem továbbá, hogy az űrlapon megadott összes adat (beleértve a szülői, gyermek- és egészségügyi adatokat) pontos és valós. Tudomásul veszem, hogy a pontatlan vagy hiányos adatok megadása a tábor működését és a gyermek biztonságát veszélyeztetheti, és az ebből eredő következményekért a felelősség engem terhel.</label>
        <small>(GDPR Art. 8 - Gyermek adatkezelés feltétele; Ptk. 4:1. § - Törvényes képviselet)</small>
    </div>

    <!-- Additional Notes -->
    <h3>További megjegyzések</h3>
    <div class="mb-3">
        <label for="additional_notes" class="form-label">Egyéb információk (opcionális)</label>
        <textarea name="additional_notes" id="additional_notes" class="form-control" rows="3" placeholder="Pl. különleges kérések, megjegyzések"></textarea>
        <small>(GDPR Art. 6(1)(a) - Önkéntes adat)</small>
    </div>

    <!-- Total Price -->
    <h3>Fizetendő összeg</h3>
    <div id="price_details">
        <p>A tábor alapára: <span id="base_price_display"><?= number_format($base_price, 0, ',', ' '); ?></span> Ft<br>
           Napi egyszeri étkeztetés: 0 Ft (nem igényelt)<br>
           <strong>Fizetendő összesen: <span id="total_price"><?= number_format($base_price, 0, ',', ' '); ?></span> Ft</strong></p>
    </div>

    <!-- Submission Information -->
    <div class="mb-3">
        <p><strong>Mi történik a jelentkezés leadása után?</strong><br>
           A jelentkezés elküldését követően feldolgozzuk az adatait, és e-mailben küldünk egy proforma számlát a fizetési részletekkel. Kérjük, kövesse az e-mailben található utasításokat a fizetés teljesítéséhez.</p>
    </div>

    <!-- Submit -->
    <button type="submit" class="btn btn-primary">Jelentkezés leadása</button>
</form>

<script>
// Alapértelmezett árak
let BASE_PRICE = <?= json_encode($base_price); ?>;
let MEAL_PRICE = <?= json_encode($meal_price); ?>;

function toggleBillingFields() {
    const billingType = document.getElementById('billing_type').value;
    const individualFields = document.getElementById('billing_individual_fields');
    const companyFields = document.getElementById('billing_company_fields');
    const parentSameCheckbox = document.getElementById('same_as_parent');

    if (billingType === 'individual') {
        individualFields.style.display = 'block';
        companyFields.style.display = 'none';
        parentSameCheckbox.disabled = false; // Enable checkbox for individual
        document.getElementById('billing_name').required = true;
        document.getElementById('billing_address').required = true;
        document.getElementById('billing_company_name').required = false;
        document.getElementById('billing_tax_number').required = false;
        document.getElementById('billing_company_address').required = false;
        handleSameAsParent(); // Update fields based on checkbox state
    } else {
        individualFields.style.display = 'none';
        companyFields.style.display = 'block';
        parentSameCheckbox.disabled = true; // Disable checkbox for company
        parentSameCheckbox.checked = false; // Uncheck for company
        document.getElementById('billing_name').required = false;
        document.getElementById('billing_address').required = false;
        document.getElementById('billing_company_name').required = true;
        document.getElementById('billing_tax_number').required = true;
        document.getElementById('billing_company_address').required = true;
        // Clear individual fields to avoid confusion
        document.getElementById('billing_name').value = '';
        document.getElementById('billing_address').value = '';
        document.getElementById('billing_name').readOnly = false;
        document.getElementById('billing_address').readOnly = false;
    }
}

function handleSameAsParent() {
    const sameAsParent = document.getElementById('same_as_parent').checked;
    const billingType = document.getElementById('billing_type').value;
    const billingName = document.getElementById('billing_name');
    const billingAddress = document.getElementById('billing_address');
    const parentName = document.getElementById('parent_name').value;
    const parentAddress = document.getElementById('parent_address').value;

    if (billingType === 'individual' && sameAsParent) {
        billingName.value = parentName;
        billingAddress.value = parentAddress;
        billingName.readOnly = true;
        billingAddress.readOnly = true;
    } else if (billingType === 'individual') {
        billingName.value = ''; // Clear the field when unchecked
        billingAddress.value = ''; // Clear the field when unchecked
        billingName.readOnly = false;
        billingAddress.readOnly = false;
    }
}

function updateTotalPrice() {
    const mealOption = document.getElementById('meal_option').checked;
    const totalPrice = mealOption ? Number(BASE_PRICE) + Number(MEAL_PRICE) : Number(BASE_PRICE);
    const priceDetails = document.getElementById('price_details');
    const mealPriceDisplay = document.getElementById('meal_price_display');
    const basePriceDisplay = document.getElementById('base_price_display');

    // Update text content for base and meal price
    basePriceDisplay.textContent = BASE_PRICE.toLocaleString('hu-HU');
    mealPriceDisplay.textContent = MEAL_PRICE.toLocaleString('hu-HU');

    // Update price details without redefining base_price_display span
    priceDetails.innerHTML = `
        <p>A tábor alapára: <span id="base_price_display">${BASE_PRICE.toLocaleString('hu-HU')}</span> Ft<br>
           Napi egyszeri étkeztetés: ${mealOption ? `${MEAL_PRICE.toLocaleString('hu-HU')} Ft` : '0 Ft (nem igényelt)'}<br>
           <strong>Fizetendő összesen: <span id="total_price">${totalPrice.toLocaleString('hu-HU')}</span> Ft</strong></p>
    `;
}

function updateCampDetails() {
    const selectedCampId = document.getElementById('selected_camp_id').value;
    const campTitle = document.getElementById('camp_title');
    const campIdHidden = document.getElementById('camp_id_hidden');
    const dateSelect = document.getElementById('selected_date');

    console.log("Selected Camp ID:", selectedCampId); // Hibakeresés

    if (selectedCampId) {
        // Tábor nevének frissítése
        const selectedOption = document.querySelector(`#selected_camp_id option[value="${selectedCampId}"]`);
        const campName = selectedOption.textContent.trim();
        campTitle.textContent = `Jelentkezés a(z) ${campName} táborba`;
        campIdHidden.value = selectedCampId;

        // Tábor adatainak lekérése
        const camps = <?php echo json_encode($camps); ?>;
        const camp = camps.find(c => c.id == selectedCampId);
        console.log("Found Camp:", camp); // Hibakeresés

        if (camp) {
            // Ár frissítése a kiválasztott tábor alapján
            BASE_PRICE = camp.price || 50000; // Alapértelmezett ár, ha nincs megadva
            MEAL_PRICE = camp.meal_price || 6000; // Alapértelmezett étkezési ár, ha nincs megadva

            // Időpontválasztó engedélyezése és feltöltése
            dateSelect.disabled = false;
            dateSelect.innerHTML = '<option value="">-- Válassz időpontot --</option>';

            const dates = [camp.date, camp.date2, camp.date3].filter(date => date && date.trim() !== '');
            console.log("Available Dates:", dates); // Hibakeresés

            if (dates.length > 0) {
                dates.forEach(date => {
                    const option = document.createElement('option');
                    option.value = date; // Raw date string
                    option.textContent = date; // A látható szöveg magyar formátumban
                    dateSelect.appendChild(option);
                    console.log("Added Option - Value:", option.value, "Text:", option.textContent); // Hibakeresés
                });
            } else {
                console.error("No dates available for this camp!");
                dateSelect.innerHTML = '<option value="">-- Nincs elérhető időpont --</option>';
                dateSelect.disabled = true;
                alert('Ehhez a táborhoz jelenleg nincs elérhető időpont. Kérjük, válassz másik tábort!');
            }
        } else {
            console.error("Camp not found!");
            dateSelect.disabled = true;
            dateSelect.innerHTML = '<option value="">-- Nincs elérhető időpont --</option>';
            alert('A kiválasztott tábor nem található. Kérjük, válassz újra!');
            BASE_PRICE = 50000; // Visszaállítjuk az alapértelmezett árat
            MEAL_PRICE = 6000; // Visszaállítjuk az alapértelmezett étkezési árat
        }
    } else {
        campTitle.textContent = 'Jelentkezés táborainkba';
        campIdHidden.value = '';
        dateSelect.disabled = true;
        dateSelect.innerHTML = '<option value="">-- Előbb válassz tábort --</option>';
        BASE_PRICE = 50000; // Visszaállítjuk az alapértelmezett árat
        MEAL_PRICE = 6000; // Visszaállítjuk az alapértelmezett étkezési árat
    }

    // Frissítjük az árat
    updateTotalPrice();
}

// Űrlap elküldés előtti ellenőrzés
document.querySelector('.application-form').addEventListener('submit', function(e) {
    const selectedCampId = document.getElementById('selected_camp_id').value;
    const selectedDate = document.getElementById('selected_date').value;

    console.log("Form Submission - Camp ID:", selectedCampId, "Selected Date:", selectedDate); // Hibakeresés

    if (!selectedCampId) {
        e.preventDefault();
        alert('Kérjük, válassz tábort!');
        return false;
    }

    if (!selectedDate || selectedDate === '') {
        e.preventDefault();
        alert('Kérjük, válassz időpontot!');
        return false;
    }
});

// Eseménykezelők inicializálása
document.addEventListener('DOMContentLoaded', function() {
    toggleBillingFields();
    updateTotalPrice();
    updateCampDetails();
});
</script>

<?php include '../includes/footer.php'; ?>