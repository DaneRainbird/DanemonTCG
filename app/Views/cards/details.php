<?php 
    $typeString = "";
?>

<div class="cards container">
    <div class="card-details">
        <div class="card-details-image">
            <img src="<?= $card['images']['large'] ?>" alt="<?= $card['name'] ?>">
        </div>
        <div class="card-details-info">
            <h1>
                <?= img($card['set']['images']['symbol'], false, ['height' => 'auto', 'width' => '5%'])?>
                <?= $card['name'] . " - " . $card['number'] . ' / ' . $card['set']['printedTotal']  ?>
            </h1>
               
            <h2>Card Details</h2>
            <table class="pretty-table">
                <thead>
                    <th>Rarity</th>
                    <th>Type</th>
                    <th>Set</th>
                    <th>Artist</th>
                </thead>
                <tbody>
                    <td><?= $card['rarity']; ?></td>
                    <td>
                        <?php if (isset($card['types'])) : ?>
                            <?php foreach ($card['types'] as $type) : ?>
                                <?php $typeString = $typeString . $type . ' / '; ?>
                            <?php endforeach;
                            echo trim($typeString, ' / '); ?>
                        <?php else: echo "N/A"; endif; ?>
                    </td>
                    <td><a href="/cards/search?value=set.id:<?= $card['set']['id']; ?>" target="_blank"><?= $card['set']['name']; ?></a></td>
                    <td><?= $card['artist']; ?></td>
                </tbody>
            </table>

            <h2>Price Details (USD)</h2>
            <table class="pretty-table">
                <thead>
                    <th></th>
                    <th>Low</th>
                    <th>Medium</th>
                    <th>High</th>
                    <th>Market</th>
                </thead>
                <tbody>
                    <td title="Last updated at <?php echo $card['tcgplayer']['updatedAt'] ?>" ><?php echo anchor($card['tcgplayer']['url'], "TCGPlayer", ['target' => '_blank', 'class' => 'text-underline']) ?></td>
                    <?php if (isset($card['tcgplayer']['prices']['normal'])) { ?>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['low']) ? number_format($card['tcgplayer']['prices']['normal']['low'], 2, ".", ",") : "N/A"); ?></td>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['mid']) ? number_format($card['tcgplayer']['prices']['normal']['mid'], 2, ".", ",") : "N/A"); ?></td>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['high']) ? number_format($card['tcgplayer']['prices']['normal']['high'], 2, ".", ",") : "N/A"); ?></td>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['market']) ? number_format($card['tcgplayer']['prices']['normal']['market'], 2, ".", ",") : "N/A"); ?></td>
                    <?php } else { ?>
                        <td colspan="4" style="text-align: center">N/A</td>
                    <?php } ?>
                </tbody>
            </table>
           
            <div class="ebay-data">
                <h2 class="card-subheading">eBay Listings</h2>
                <div class="ebay-links">
                    <p><?= anchor("https://www.ebay.com.au/sch/i.html?_nkw=" . $card['name'] . "+" . $card['number'] . "%2F" . $card['set']['printedTotal'], img(base_url('assets/img/ebay.ico'), false) . "eBay Sales (only Open)", ['target' => '_blank']) ?><?= anchor("https://www.ebay.com.au/sch/i.html?_nkw=" . $card['name'] . "+" . $card['number'] . "%2F" . $card['set']['printedTotal'] . "&_in_kw=1&_ex_kw=&_sacat=0&LH_Sold=1&Complete=1&_fosrp=1", img(base_url('assets/img/ebay.ico'), false) . "eBay Sales (including Sold)", ['target' => '_blank']) ?></p>
                </div>
            </div>

            <?php if (session()->get('username')): ?>
                <div class="collection-buttons">
                    <div class="dropdown">
                        <label for="collection">Add to Collection</label>
                        <select name="collection" id="collection">
                            <?php foreach ($collections as $collection) : ?>
                                <option value="<?= $collection->id ?>"><?= $collection->name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="card_id" value="<?= $card['id'] ?>">
                        <button class="btn btn-primary" id="add-to-collection" type="button" onclick="addToCollection()">Add</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function addToCollection() {
        var xhr = new XMLHttpRequest();
        var url = "/cards/addToCollection"; 
        var formData = new FormData();
        formData.append("collection_id", document.getElementById("collection").value);
        formData.append("card_id", document.getElementById("card_id").value);
        formData.append("user_id", "<?= session()->get('uid') ?>");
        xhr.open("POST", url);
        xhr.send(formData);

        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                alert("Card added to collection!");
            }
        }
    }
</script>