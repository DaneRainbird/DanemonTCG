<?php 
    // Check if the view argument is set
    if (isset($view)) {
        // Set the display selector value
        $displaySelectorValue = $view == 'table' ? 2 : 1;
    }
?>

<div class="container">
    <div class="controls">
        <div class="search-container">
            <form action="<?= base_url('cards/search') ?>" method="get">
                <input type="text" id="value" name="value" placeholder="Search for a card!" required value="<?= htmlspecialchars($searchQuery); ?>">
                <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
            </form>
        </div>
        <div class="dropdown">
            <label for="display-selector">Display as:</label>
            <select id="display-selector">
                <option value="1">Cards</option>
                <option value="2">Table</option>
            </select>
        </div>
    </div>
</div>

<?php if ($view === 'grid') : ?>
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

<?php else : ?>
    <div class="cards-table container">
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
                        <td><a class="view-button" href="/cards/details/<?= $card['id']; ?>" target="_blank">View Card</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (isset($pagination)) : ?>
    <div class="pagination">
        <?php 
            if (count($cards) !== 0) {
                $totalPages = $pagination->getTotalPages();
                $currentPage = $pagination->getPage();

                // Parse the current URL to extract the query string
                $urlParts = parse_url($_SERVER['REQUEST_URI']);
                parse_str($urlParts['query'], $queryParams);

                // Add "first" page button
                if ($currentPage > 3) {
                    $queryParams['page'] = 1;
                    $firstPageUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
                    echo '<a class="page-button" href="' . $firstPageUrl . '">≪</a>';
                }

                // Add page buttons
                foreach (range(max(1, $currentPage - 2), min($totalPages, $currentPage + 2)) as $page) {
                    $queryParams['page'] = $page;
                    $pageUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
                    if ($page == $currentPage) {
                        echo '<a class="page-button active" href="' . $pageUrl . '">' . $page .  "</a>";
                    } else {
                        echo '<a class="page-button" href="' . $pageUrl . '">' . $page .  "</a>";
                    }
                }

                // Add "last" page button
                if ($currentPage < $totalPages - 2) {
                    $queryParams['page'] = $totalPages;
                    $lastPageUrl = $urlParts['path'] . '?' . http_build_query($queryParams);
                    echo '<a class="page-button" href="' . $lastPageUrl . '">≫</a>';
                }
            }
        ?>
    </div>
<?php endif; ?>

<script>
    // Get the display selector
    const displaySelector = document.getElementById('display-selector');

    // Add an event listener to the display selector
    displaySelector.addEventListener('change', (event) => {
        // Get the value of the display selector
        const displaySelectorValue = event.target.value;

        // Convert the value to the appropriate argument
        const viewVal = displaySelectorValue == 1 ? 'grid' : 'table';

        // Get the current URL and parse the query string
        const currentUrl = window.location.href;
        const urlParts = currentUrl.split('?');
        const baseUrl = urlParts[0];
        const queryParams = new URLSearchParams(urlParts[1]);

        // Set the "view" parameter to the new value
        queryParams.set('view', viewVal);

        // Construct the new URL with the updated query string
        const newUrl = baseUrl + '?' + queryParams.toString();

        // Reload the page with the new URL
        window.location.href = newUrl;
    });

    // Set the display selector value
    displaySelector.value = <?= $displaySelectorValue ?>;

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
</script>