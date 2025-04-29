<?php include '../includes/header.php'; ?>

<h2 class="mb-4">Rólunk</h2>

<section class="mb-5">
    <h3>Táborunk története</h3>
    <p>
        Csapatunk tagjai több éves táboroztatási tapasztalattal rendelkeznek. 
        Célunk egy olyan felejthetetlen élmény nyújtása a gyerekek számára,
        amely továbbtanulásukban és később karrierjük választásában segíthet nekik.
        
    </p>
</section>

<h3>Csapatunk</h3>
<div class="row prof">
    <?php
    $team = [
        ["name" => "Czanik Csanád", "role" => "Táborvezető", "image" => "../public/profkep.JPG"],
        ["name" => "Tóth Loretta", "role" => "Programszervező", "image" => "../public/lotti_kep.jpg"],
        ["name" => "Masznyik Jázmin", "role" => "Oktató", "image" => "../public/jazmin_kep.jpg"],
        ["name" => "Hargitai Zalán", "role" => "Oktató", "image" => "../public/zalan_kep.jpg"],
    ];
    
    foreach ($team as $member) {
        echo '<div class="col-12 col-md-4 col-lg-3 mb-2">';
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