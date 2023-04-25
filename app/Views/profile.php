<div class="container">
    <?= session()->get('username') ?> is logged in!

    <h3>collections?</h3>
    <?php foreach ($collections as $collection) : ?>
        <div class="collection">
            <h4><?= $collection->name ?></h4>
            <?php foreach ($collection->cards as $card) : ?>
                <p><?= $card->card_id ?></p>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
