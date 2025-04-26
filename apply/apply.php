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
</style>

<h1>Jelentkezés a(z) <?= htmlspecialchars($camp['name']); ?> táborba</h1>
<form action="process_application.php" method="POST" enctype="multipart/form-data" class="application-form">
    <input type="hidden" name="camp_id" value="<?= htmlspecialchars($camp_id); ?>">

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
        <label for="meal_option" class="form-check-label">Igényelek napi egyszeri étkeztetést (ebéd), ára 6000 Ft.</label>
        <small>(GDPR Art. 6(1)(b) - Szerződéses szolgáltatás)</small>
    </div>

    <!-- Emergency Contact Information -->
    <h3>Vészhelyzeti elérhetőségek</h3>
    <div class="mb-3">
        <label for="emergency_name_1" class="form-label">Név</label>
        <input type="text" name="emergency_name_1" id="emergency_name_1" class="form-control" placeholder="Kovács Béla" required>
        <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="emergency_relationship_1" class="form-label">Kapcsolat a gyermekkel</label>
            <input type="text" name="emergency_relationship_1" id="emergency_relationship_1" class="form-control" placeholder="Pl.: nagyszülő, nagybácsi, nagynéni, rokon" required>
            <small>(GDPR Art. 6(1)(d) - Vészhelyzeti azonosítás)</small>
        </div>
        <div class="col-md-6">
            <label for="emergency_phone1" class="form-label">1. Vészhelyzeti telefonszám</label>
            <input type="tel" name="emergency_phone1" id="emergency_phone1" class="form-control" placeholder="+36 70 987 6543" required>
            <small>(GDPR Art. 6(1)(d) - Gyors elérhetőség)</small>
        </div>
    </div>
    <div class="mb-3">
        <label for="emergency_name_2" class="form-label">Név</label>
        <input type="text" name="emergency_name_2" id="emergency_name_2" class="form-control" placeholder="Tóth Anna" required>
        <small>(GDPR Art. 6(1)(d) - Létfontosságú érdek)</small>
    </div>
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="emergency_relationship_2" class="form-label">Kapcsolat a gyermekkel</label>
            <input type="text" name="emergency_relationship_2" id="emergency_relationship_2" class="form-control" placeholder="Pl.: nagyszülő, nagybácsi, nagynéni, rokon" required>
            <small>(GDPR Art. 6(1)(d) - Vészhelyzeti azonosítás)</small>
        </div>
        <div class="col-md-6">
            <label for="emergency_phone2" class="form-label">2. Vészhelyzeti telefonszám</label>
            <input type="tel" name="emergency_phone2" id="emergency_phone2" class="form-control" style="max-width: 300px;" placeholder="+36 20 456 7890" required>
            <small>(GDPR Art. 6(1)(d) - Tartalék elérhetőség)</small>
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
        <label for="aszf_acceptance" class="form-check-label">Elfogadom az <a href="/galgatabor/aszf.pdf" target="_blank"><strong>Általános Szerződési Feltételeket (ÁSZF)</strong></a>.</label>
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
        <p>A tábor alapára: 50 000 Ft<br>
           Napi egyszeri étkeztetés: 0 Ft (nem igényelt)<br>
           <strong>Fizetendő összesen: <span id="total_price">50 000</span> Ft</strong></p>
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
    const basePrice = 50000;
    const mealPrice = 6000;
    const totalPrice = mealOption ? basePrice + mealPrice : basePrice;
    const priceDetails = document.getElementById('price_details');
    
    priceDetails.innerHTML = `
        <p>A tábor alapára: 50 000 Ft<br>
           Napi egyszeri étkeztetés: ${mealOption ? '6 000 Ft' : '0 Ft (nem igényelt)'}<br>
           <strong>Fizetendő összesen: <span id="total_price">${totalPrice.toLocaleString('hu-HU')}</span> Ft</strong></p>
    `;
}

// Initialize billing fields and price on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleBillingFields();
    updateTotalPrice();
});
</script>

<?php include '../includes/footer.php'; ?>