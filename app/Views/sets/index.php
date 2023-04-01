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

    // Invert the array so that the most recent sets are at the top
    $setsBySeries = array_reverse($setsBySeries);
?>

<div class="cards container">
    <?php foreach ($setsBySeries as $series => $sets) : ?>
        <div class="series">
            <h2 id="<?= $set['id']; ?>"><?= $series; ?></h2>
            <div class="series-sets">
                <?php foreach ($sets as $set) : ?>
                    <a href="details/<?= $set['id']; ?>" target="_blank">
                        <div class="set">
                            <img class="set-image" src="<?= $set['images']['logo'] ?>" alt="<?= $set['name'] ?>">
                            <div class="set-info">
                                <p><?= "<strong>" . $set['name'] . '</strong><br/><em>' . $set['releaseDate'] . '</em>'; ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>