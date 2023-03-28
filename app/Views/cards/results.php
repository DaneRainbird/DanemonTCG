<div class="container left">
    <div class="controls">
        <div class="dropdown">
        
        </div>
    </div>
</div>

<div class="cards container">
    <?php foreach ($cards as $card) : ?>
        <div class="card">
            <img class="card-image" src="<?= $card['images']['small'] ?>" alt="<?= $card['name'] ?>">
            <div class="card-info">
                <p><?= "<strong>" . $card['set']['name'] . '</strong><br/><em>' . $card['number'] . '/' . $card['set']['total'] . '</em>'; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>