<div class="container">
    <h2>Collection: <?= $collectionName; ?></h2>
    <div class="controls">
        <div class="dropdown">
            <label for="display-selector">Display as:</label>
            <select id="display-selector">
                <option value="1">Cards</option>
                <option value="2">Table</option>
            </select>
        </div>
    </div>
</div>

<div class="cards container">
    <?php 
        foreach ($cards as $card) : ?>
        <a href="/cards/details/<?= $card['id']; ?>" target="_blank">
            <div class="card">
                <img class="card-image" src="<?= $card['images']['small'] ?>" alt="<?= $card['name'] ?>">
                <div class="card-info">
                    <p><?= "<strong>" . $card['set']['name'] . '</strong><br/><em>' . $card['number'] . '/' . $card['set']['total'] . '</em>'; ?></p>
                </div>
            </div>
        </a>
    <?php endforeach; ?>

    <?php if (count($cards) == 0) : ?>
        <p>No cards found.</p>
    <?php endif; ?>
</div>

<div class="cards-table container hide">
    <table id="card-table" class="pretty-table">
        <thead>
            <tr>
                <th>Image</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Set</th>
                <th onclick="sortTable(3)">Number</th>
                <th>More Details?</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cards as $card) : ?>
                <tr>
                    <td><img class="card-image" src="<?= $card['images']['small'] ?>" alt="<?= $card['name'] ?>"></td>
                    <td><?= $card['name'] ?></td>
                    <td><?= $card['set']['name'] ?></td>
                    <td><?= $card['number'] . '/' . $card['set']['total'] ?></td>
                    <td>
                        <a class="view-button" href="/cards/details/<?= $card['id']; ?>" target="_blank">View Card</a>
                        <a class="delete-button" data-card-id="<?= $card['id']; ?>" target="_blank">Remove from Collection?</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Get the display selector
    const displaySelector = document.getElementById('display-selector');

    // Get the cards container
    const cardsContainer = document.querySelector('.cards');

    // Get the table container
    const tableContainer = document.querySelector('.cards-table');

    // Add an event listener to the display selector
    displaySelector.addEventListener('change', (event) => {
        // Get the value of the display selector
        const displaySelectorValue = event.target.value;

        // If the value is 1, show the cards container and hide the table container
        if (displaySelectorValue === '1') {
            cardsContainer.classList.remove('hide');
            tableContainer.classList.add('hide');
        }

        // If the value is 2, show the table container and hide the cards container
        if (displaySelectorValue === '2') {
            cardsContainer.classList.add('hide');
            tableContainer.classList.remove('hide');
        }
    });


    /**
     * Sort the table by the column index.
     * 
     * @param {number} columnIndex The column index
     */
    function sortTable(columnIndex) {
        const table = document.getElementById('card-table');
        const rows = Array.from(table.rows).slice(1); // exclude the header row
        const headerRow = table.rows[0];
        const headerCells = headerRow.cells;

        for (let i = 0; i < headerCells.length; i++) {
            if (i !== columnIndex ) { 
                headerCells[i].removeAttribute('data-sort');
            }
        }

        let sortDirection = headerCells[columnIndex].getAttribute('data-sort');
        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'; // toggle sort direction
        headerCells[columnIndex].setAttribute('data-sort', sortDirection);

        rows.sort((a, b) => {
            const aText = a.cells[columnIndex].textContent.toLowerCase();
            const bText = b.cells[columnIndex].textContent.toLowerCase();

            if (sortDirection === 'asc') {
                if (aText < bText) {
                    return -1;
                }
                if (aText > bText) {
                    return 1;
                }
            } else {
                if (aText > bText) {
                    return -1;
                }
                if (aText < bText) {
                    return 1;
                }
            }
            return 0;
        });

        const newTbody = document.createElement('tbody');
        rows.forEach((row) => {
            newTbody.appendChild(row);
        });

        table.replaceChild(newTbody, table.tBodies[0]);
    }

    // Add an event listener to the delete button
    const deleteButtons = document.querySelectorAll('.delete-button');
    deleteButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            // The user selected an existing collection, so add the card to it
            var xhr = new XMLHttpRequest();
            var url = "/collections/removeCardFromCollection"; 
            var formData = new FormData();
            formData.append("collection_id", "<?= $collectionId ?>");
            formData.append("card_id", button.getAttribute('data-card-id'));
            formData.append("user_id", "<?= session()->get('uid') ?>");
            xhr.open("POST", url);
            xhr.send(formData);

            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    createNotification("Card successfully deleted from the collection!", "is-success");
                    button.parentElement.parentElement.remove();
                } else if (this.readyState == 4 && this.status == 400) {
                    createNotification("Card could not be removed from the collection: " + JSON.parse(this.responseText).message, "is-danger");
                }  
            }
        });
    });
</script>