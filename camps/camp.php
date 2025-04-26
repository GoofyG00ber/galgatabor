<?php
include '../includes/header.php';

$id = $_GET['id'] ?? 0;
$query = "SELECT * FROM camps WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$camp = $result->fetch_assoc();

if (!$camp) {
    echo "<div class='container mt-5'><h2>Tábor nem található</h2></div>";
    include '../includes/footer.php';
    exit;
}

// Function to safely render HTML content
function safeHtml($content) {
    // Use HTMLPurifier or a similar library in production for better security
    return $content; // For simplicity, assuming content is trusted; replace with proper sanitization
}
?>


<div class="container my-5">
    <!-- Main Section -->
    <div class="row mb-5">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image']); ?>" alt="<?= htmlspecialchars($camp['name']); ?>" class="img-fluid rounded shadow camp-main-image">
        </div>
        <div class="col-md-6">
            <h1 class="mb-4"><?= htmlspecialchars($camp['name']); ?></h1>
            <div class="mb-4"><?= safeHtml($camp['description']); ?></div>
        </div>
    </div>

<div class="row mb-5">
    <div class="col-md-6">
            <ul class="list-group list-group-flush my-4">
                <li class="list-group-item"><strong>Ajánlott korosztály:</strong> <?= htmlspecialchars($camp['age_group']); ?></li>
                <li class="list-group-item"><strong>Tábor ára:</strong> <?= htmlspecialchars($camp['price']); ?> Ft</li>
                <li class="list-group-item"><strong>Igényelhető étkeztetés ára:</strong> <?= htmlspecialchars($camp['meal_price']); ?> Ft</li>
            </ul>
    </div>
    <div class="col-md-6">
            <ul class="list-group list-group-flush my-4">
                <li class="list-group-item"><strong>Előzetes tudás nem szükséges!</strong></li>
                <li class="list-group-item"><strong></strong> <?= htmlspecialchars($camp['price']); ?> Ft</li>
                <li class="list-group-item"><strong>Igényelhető étkeztetés ára:</strong> <?= htmlspecialchars($camp['meal_price']); ?> Ft</li>
            </ul>
    </div>
</div>
    <!-- terkep -->
    <div class="row align-items-center my-5 flex-md-row-reverse">
        <div class="col-md-12">
            
            <!-- Dates, Locations, Addresses, and Maps Table -->
            <?php
            $dates = array_filter([$camp['date'], $camp['date2'] ?? '', $camp['date3'] ?? ''], function($date) {
                return !empty($date);
            });
            $locations = array_filter([$camp['location'], $camp['location2'] ?? '', $camp['location3'] ?? ''], function($location) {
                return !empty($location);
            });
            $addresses = array_filter([$camp['address'], $camp['address2'] ?? '', $camp['address3'] ?? ''], function($address) {
                return !empty($address);
            });
            $iframes = array_filter([$camp['iframe'], $camp['iframe2'] ?? '', $camp['iframe3'] ?? ''], function($iframe) {
                return !empty($iframe);
            });
            $hasMultiple = count($dates) > 1 || count($locations) > 1 || count($addresses) > 1 || count($iframes) > 1;

            if ($hasMultiple) {
                echo '<h5 class="mb-3">Időpontok, Helyszínek:</h5>';
                echo '<div class="table-responsive">';
                echo '<table class="table table-bordered w-100">';
                echo '<thead class=""><tr class="table-dark""><th>Időpont</th><th>Helyszín</th><th>Cím</th><th>Térkép</th></tr></thead>';
                echo '<tbody class="">';
                for ($i = 0; $i < max(count($dates), count($locations), count($addresses), count($iframes)); $i++) {
                    $date = $dates[$i] ?? ($dates[0] ?? '');
                    $location = $locations[$i] ?? ($locations[0] ?? '');
                    $address = $addresses[$i] ?? ($addresses[0] ?? '');
                    $iframe = $iframes[$i] ?? ($iframes[0] ?? '');
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($date) . '</td>';
                    echo '<td>' . htmlspecialchars($location) . '</td>';
                    echo '<td>' . htmlspecialchars($address) . '</td>';
                    echo '<td>';
                    if (!empty($iframe)) {
                        echo '<a href="#" class="map-link" data-bs-toggle="modal" data-bs-target="#mapModal" data-iframe="' . htmlspecialchars($iframe) . '">Mutasd a térképen!</a>';
                    } else {
                        echo 'Nincs térkép';
                    }
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
                echo '</div>';
            } else {
                echo '<ul class="list-group list-group-flush">';
                echo '<li class="list-group-item"><strong>Időpont:</strong> ' . htmlspecialchars($camp['date']) . '</li>';
                echo '<li class="list-group-item"><strong>Helyszín:</strong> ' . htmlspecialchars($camp['location']) . '</li>';
                echo '<li class="list-group-item"><strong>Cím:</strong> ' . htmlspecialchars($camp['address']) . '</li>';
                if (!empty($camp['iframe'])) {
                    echo '<li class="list-group-item"><strong>Térkép:</strong> <a href="#" class="map-link" data-bs-toggle="modal" data-bs-target="#mapModal" data-iframe="' . htmlspecialchars($camp['iframe']) . '">Mutasd a térképen!</a></li>';
                }
                echo '</ul>';
            }
            ?>

            <a href="/galgatabor/apply/apply.php?camp_id=<?= $camp['id']; ?>" class="btn btn-success btn-lg mt-3 col-md-12 d-flex justify-content-center">Jelentkezés & Tábor Választás</a>
        </div>
    </div>

    <div class="row my-5">
    <div class="col-12">
        <div class="alert alert-warning shadow-sm" role="alert" style="background-color: wheat; color:darkred; border-color: #8B0000;">
            <h4 class="alert-heading mb-3 text-darkred">Fontos tudnivalók</h4>
            <p class="mb-3">Kérjük, figyelmesen olvassa el az alábbi információkat a táborral kapcsolatban:</p>
            <ul class="fontos_lista mt-2 mb-2">
                <li>A táborban való részvételhez saját laptop vagy tablet szükséges! Ezen kívűl minden más elektronikai felszerelést biztosítunk!</li>
                <li>Hozz magaddal kényelmes ruházatot és váltócipőt!</li>
                <li>Értéktárgyaidat tartsd biztonságos helyen.</li>
                <li>Érkezz 15 perccel a tábor kezdete előtt.</li>
                <li>Allergiáidat vagy speciális étrendi igényeidet jelezd előre!</li>
                <li>Szükséges napvédő krém és kulacs minden napra.</li>
                <li>Szükséges napvédő krém és kulacs minden napra.</li>
            </ul>
        </div>
    </div>
</div>

    <!-- Content Block 1 -->
    <div class="row align-items-center my-5 flex-md-row-reverse">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image2']); ?>" alt="Kép 2" class="img-fluid rounded shadow camp-secondary-image">
        </div>
        <div class="col-md-6">
            <div><?= safeHtml($camp['content2']); ?></div>
        </div>
    </div>

    <!-- Content Block 2 -->
    <div class="row align-items-center my-5">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image3']); ?>" alt="Kép 3" class="img-fluid rounded shadow camp-secondary-image">
        </div>
        <div class="col-md-6">
            <div><?= safeHtml($camp['content3']); ?></div>
        </div>
    </div>

    <!-- Content Block 3 -->
    <div class="row align-items-center my-5 flex-md-row-reverse">
        <div class="col-md-6">
            <img src="/galgatabor/public/<?= htmlspecialchars($camp['image4']); ?>" alt="Kép 4" class="img-fluid rounded shadow camp-secondary-image">
        </div>
        <div class="col-md-6">
            <div><?= safeHtml($camp['content4']); ?></div>
        </div>
    </div>
</div>

<!-- Bootstrap Modal for Map -->
<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel">Térkép</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="mapIframe" src="" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bezárás</button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapLinks = document.querySelectorAll('.map-link');
    mapLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const iframeSrc = this.getAttribute('data-iframe');
            document.getElementById('mapIframe').src = iframeSrc;
        });
    });
});
</script>

<style>
.table-responsive {
    width: 100%;
}
.table.w-100 {
    width: 100% !important;
}
.map-link {
    color: #007bff;
    text-decoration: none;
}
.table-responsive {
    width: 100%;
}
.table.w-100 {
    width: 100% !important;
}
.map-link {
    color: #007bff;
    text-decoration: none;
}
.table thead {
    background-color: #8B0000;/* Sötétvörös háttér a címsornak */
    color: #FFFFFF; /* Fehér szöveg a kontraszt miatt */
}
.table tbody {
    background-color: #F8F9FA; /* Világosszürke háttér a tartalomnak */
}
.table-bordered th, .table-bordered td {
    border: 1px solid #DEE2E6; /* Szegélyek egységesítése */
}
</style>