<?php include '../includes/header.php'; ?>
<h2>Rólunk</h2>

<section class="mb-5">
    <h3>Táborunk története</h3>
    <p>
        A Galgatábor több mint 10 éve szervez nyári táborokat gyermekek számára. Célunk, hogy felejthetetlen élményeket nyújtsunk a gyerekeknek, miközben biztonságos és barátságos környezetben tanulnak és szórakoznak.
    </p>
</section>

<h3>Csapatunk</h3>
<div class="row prof">
    <?php
    $team = [
        ["name" => "Kiss Péter", "role" => "Táborvezető", "image" => "../public/profkep.jpg"],
        ["name" => "Nagy Anna", "role" => "Programszervező", "image" => "../public/placeholder2.png"],
        ["name" => "Szabó László", "role" => "Oktató", "image" => "../public/placeholder2.png"],
        ["name" => "Szabó László", "role" => "Oktató", "image" => "../public/placeholder2.png"],
        ["name" => "Szabó László", "role" => "Oktató", "image" => "../public/placeholder2.png"],
        ["name" => "Szabó László", "role" => "Oktató", "image" => "../public/placeholder2.png"]
    ];
    
    foreach ($team as $member) {
        echo '<div class="col-md-4 mb-3">';
        echo '    <div class="card text-center team-card prof">';
        echo '        <img src="'.$member['image'].'" class="card-img-top" alt="'.$member['name'].'">';
        echo '        <div class="card-body">';
        echo '            <h5 class="card-title">'.$member['name'].'</h5>';
        echo '            <p class="card-text">'.$member['role'].'</p>';
        echo '        </div>';
        echo '    </div>';
        echo '</div>';
    }
    ?>
</div>

<?php include '../includes/footer.php'; ?>
