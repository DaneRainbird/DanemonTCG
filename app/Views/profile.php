<div class="container">
    <h2>Profile</h2>
    <p>Welcome to your profile page, <?= session()->get('username') ?>!</p>
    <p>Here you can view your collections, and maybe eventually do other things. It's a work in progress.</p>

    <h2>My Collections</h2>
    <?php if (!empty($collections)) : ?>
        <p>Click on any of your collections to see their cards, or you can see <a class="fancy-link" href="/collections/viewAll" target="_blank">all of the cards you have added to a collection</a>.</p>
        <div class="series-sets collections">
            <?php foreach ($collections as $collection) : ?>
                <div class="set-container">
                    <a href="/collections/view/<?= $collection->id ?>">
                        <div class="set">
                            <div class="set-info">
                                <p><strong><?= $collection->name ?></strong></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p>You don't have any collections yet! Why not check out <a class="fancy-link" href="/cards">some cards</a> and make some?</p>
    <?php endif; ?>
</div>
