<?php 
    $typeString = "";
?>

<div class="card-details">
    <div class="card-detail">
        <img src="<?= $card['images']['large'] ?>" alt="<?= $card['name'] ?>" class="card-detail-image">
    </div>
    <div class="card-detail">
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
                <?php if (isset($card['tcgplayer'])) { ?>
                    <td title="Last updated at <?php echo $card['tcgplayer']['updatedAt'] ?>" ><?php echo anchor($card['tcgplayer']['url'], "TCGPlayer", ['target' => '_blank', 'class' => 'text-underline']) ?></td>
                    <?php if (isset($card['tcgplayer']['prices']['normal'])) { ?>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['low']) ? number_format($card['tcgplayer']['prices']['normal']['low'], 2, ".", ",") : "N/A"); ?></td>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['mid']) ? number_format($card['tcgplayer']['prices']['normal']['mid'], 2, ".", ",") : "N/A"); ?></td>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['high']) ? number_format($card['tcgplayer']['prices']['normal']['high'], 2, ".", ",") : "N/A"); ?></td>
                        <td><?= "$" . (isset($card['tcgplayer']['prices']['normal']['market']) ? number_format($card['tcgplayer']['prices']['normal']['market'], 2, ".", ",") : "N/A"); ?></td>
                    <?php } else { ?>
                        <td colspan="4" style="text-align: center">N/A</td>
                    <?php } ?>
                <?php } else { ?>
                    <td>TCGPlayer</td>
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
                <h2 class="card-subheading">Collections</h2>
                <div class="dropdown">
                    <label for="collection">Collection:</label>
                    <select name="collection" id="collection">
                        <?php foreach ($collections as $collection) : ?>
                            <option value="<?= $collection->id ?>"><?= $collection->name ?></option>
                        <?php endforeach; ?>
                        <option value="__new__">Create new collection!</option>
                    </select>
                    <input type="hidden" id="card_id" value="<?= $card['id'] ?>">
                    <button class="view-button" id="add-to-collection" type="button" onclick="addToCollection()">Add</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function addToCollection() {
    var collectionSelect = document.getElementById("collection");
    var collectionId = collectionSelect.value;

    // If the user asked to create a new collection, then do that first
    if (collectionId === "__new__") {
        // Prompt the user to enter the name of the new collection
        var newCollectionName = prompt("Enter the name of the new collection:");

        // If the user entered a name, create the new collection and add it to the dropdown
        if (newCollectionName) {
            var xhr = new XMLHttpRequest();
            var url = "/collections/createCollection";
            var formData = new FormData();
            formData.append("collection_name", newCollectionName);
            formData.append("user_id", "<?= session()->get('uid') ?>");
            xhr.open("POST", url);
            xhr.send(formData);

            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Add the new collection to the dropdown
                    var newCollectionId = JSON.parse(this.responseText).id;
                    var newOption = document.createElement("option");
                    newOption.value = newCollectionId;
                    newOption.textContent = newCollectionName;
                    collectionSelect.insertBefore(newOption, collectionSelect.lastChild);
                    
                    // Select the new collection
                    collectionSelect.value = newCollectionId;
                }
            }
        } else {
            // No name was entered, or the prompt was closed, so do nothing
            return;
        }
    } else {
        // The user selected an existing collection, so add the card to it
        var xhr = new XMLHttpRequest();
        var url = "/collections/addCardToCollection"; 
        var formData = new FormData();
        formData.append("collection_id", collectionId);
        formData.append("card_id", document.getElementById("card_id").value);
        formData.append("user_id", "<?= session()->get('uid') ?>");
        xhr.open("POST", url);
        xhr.send(formData);

        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                createNotification("Card successfully added to the collection!", "is-success");
            } else if (this.readyState == 4 && this.status == 400) {
                createNotification("Card could not be added to the collection: " + JSON.parse(this.responseText).message, "is-danger");
            }  
        }
    }
}
</script>