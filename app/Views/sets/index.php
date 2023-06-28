<?php 
    // Get the sets data and sort them into an object with the key being the "series" and the value being an array of sets
    $setsBySeries = [];
    foreach ($sets as $set) {
        $set = $set->toArray();
        if (!isset($setsBySeries[$set['series']])) {
            $setsBySeries[$set['series']] = [];
        }
        array_push($setsBySeries[$set['series']], $set);
    }

    // Sort the sets within each key by release date, from most recent to least recent
    foreach ($setsBySeries as $series => $sets) {
        usort($sets, function($a, $b) {
            return strtotime($b['releaseDate']) - strtotime($a['releaseDate']);
        });
        $setsBySeries[$series] = $sets;
    }

    // Sort the keys by the release date of the most recent set in each series
    uasort($setsBySeries, function($a, $b) {
        return strtotime($b[0]['releaseDate']) - strtotime($a[0]['releaseDate']);
    });
?>

<div class="sets">
    <?php foreach ($setsBySeries as $series => $sets) : ?>
        <div class="series">
            <h2 id="<?= $set['id']; ?>"><?= $series; ?></h2>
            <div class="series-sets">
                <?php foreach ($sets as $set) : ?>
                    <div class="set-container">
                        <a href="cards/search?value=set.id:<?= $set['id']; ?>" target="_blank">
                            <div class="set">
                                <div class="set-image">
                                    <img src="<?= $set['images']['logo'] ?>" alt="<?= $set['name'] ?>">
                                </div>
                                <div class="set-info">
                                    <p><?= "<strong>" . $set['name'] . '</strong><br/><em>' . $set['releaseDate'] . '</em>'; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>