<?php 
    const MAXIMUM_CARDS_PER_ROW = 5; // Defaulting to 5, might be changed?
    $view = isset($view) ? $view : 'grid';
    $displaySelectorValue = $view == 'table' ? 2 : 1;
    $cardsPerRow = isset($cardsPerRow) ? $cardsPerRow : $MAXIMUM_CARDS_PER_ROW;
?>

<div class="container">
    <?php if ($isCollection) : ?>
        <h2>Collection: <?= $collectionName; ?></h2>
    <?php endif; ?>
    <div class="controls">
        <?php if ($isSearch) : ?>
            <div class="control">
                <div class="search-container">
                    <form action="<?= base_url('cards/search') ?>" method="get">
                        <input type="text" id="value" name="value" placeholder="Search for a card!" required value="<?= htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
        <div class="control" id="card-search-controls">
            <div class="dropdown">
                <label for="display-selector">Display as:</label>
                <select id="display-selector">
                    <option value="1">Cards</option>
                    <option value="2">Table</option>
                </select>
            </div>
            <div class="dropdown">
                <label for="cards-per-row">Cards per row</label>
                <select id="cards-per-row">
                    <?php for ($i = 1; $i <= MAXIMUM_CARDS_PER_ROW; $i++) : ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Begin Pagination Top -->
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
    <!-- End Pagination Top -->

    <!-- Begin Grid View -->
    <?php if ($view === 'grid') : ?>
        <div class="cards" id="cards-container">
            <?php 
                foreach ($cards as $card) : ?>
                    <div class="card">
                        <a href="/cards/details/<?= $card['id']; ?>" target="_blank">
                            <div class="card-image-wrapper">
                                <img class="card-image" src="<?= $card['images']['small'] ?>" alt="<?= $card['name'] ?>">
                            </div>
                            <div class="card-info">
                                <p class="card-info-text"><?= "<strong class='card-set-name'>" . $card['set']['name'] . '</strong><br/><em>' . $card['number'] . '/' . $card['set']['total'] . '</em>'; ?></p>
                                <?php if ($isCollection) : ?>
                                    <a class="delete-button" data-card-id="<?= $card['id']; ?>" target="_blank">Remove from Collection?</a>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
            <?php endforeach; ?>

            <?php if (count($cards) == 0) : ?>
                <p>No cards found.</p>
            <?php endif; ?>
        </div>
    <!-- End Grid View -->

    <!-- Begin Table View -->
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
    <!-- End Table View -->

    <!-- Begin Pagination Bottom -->
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
    <!-- End Pagination Bottom -->
</div>


<!-- Begin Image Modal -->
<div id="image-modal" class="modal">
    <span class="close" id="modal-close">&times;</span>
    <img class="modal-content" id="modal-image"/>
</div>
<!-- End Image Modal -->

<script>
    // Constants / variables
    const DISPLAY_SELECTOR = document.getElementById('display-selector');
    const CARDS_PER_ROW_SELECTOR = document.getElementById('cards-per-row');
    const CARDS_CONTAINER = document.getElementById('cards-container');
    const MODAL = document.getElementById('image-modal');
    const MODAL_IMAGE = document.getElementById('modal-image');
    const MODAL_CLOSE_BUTTON = document.getElementById('modal-close');
    const DELETE_BUTTONS = document.querySelectorAll('.delete-button');
    const USER_SPECIFIED_CARDS_PER_ROW = <?= var_export($userSetCardsPerRow, true); ?>;
    let displaySelectorValue = null;
    let cardsPerRow = null;

    // Event listener for when the page is fully loaded
    window.onload = (event) => {
        // Initialize values
        cardsPerRow = <?= $cardsPerRow ?>;

        if (!USER_SPECIFIED_CARDS_PER_ROW) {
            if (checkIsMobile()) {
                cardsPerRow = 2;
            }
        } 

        // Apply card layout based on initial values
        CARDS_CONTAINER.style.setProperty('--cards-per-row', cardsPerRow);
        updateCardMaxWidth();

        // Update display values in the selectors
        DISPLAY_SELECTOR.value = <?= $displaySelectorValue ?>;
        CARDS_PER_ROW_SELECTOR.value = cardsPerRow

        // Event listener for image clicks in table view
        document.querySelectorAll('.pretty-table img').forEach((image) => {
            image.addEventListener('click', (event) => {
                MODAL_IMAGE.src = event.target.src;
                setTimeout(() => {
                    MODAL.classList.add('show')
                }, 10);
            });
        });

        // Event listener for closing the modal via the close button
        MODAL_CLOSE_BUTTON.addEventListener('click', () => {
            MODAL.classList.remove('show');
        });

        // Event listener for closing the modal via clicking outside the modal
        window.addEventListener('click', (event) => {
            if (event.target === MODAL) {
                MODAL.classList.remove('show');
            }
        });

        // Event listener for display selector
        DISPLAY_SELECTOR.addEventListener('change', (event) => {
            displaySelectorValue = event.target.value;
            const VIEW_VAL = displaySelectorValue == 1 ? 'grid' : 'table';

            // Update the URL params and reload the page
            updateUrlParameters('view', VIEW_VAL, true);
        });

        // Event listener for cards per row selector
        CARDS_PER_ROW_SELECTOR.addEventListener('change', (event) => {
            cardsPerRow = event.target.value;
            CARDS_CONTAINER.style.setProperty('--cards-per-row', cardsPerRow);

            // Update the max width of the cards
            updateCardMaxWidth();

            // Update the URL params, but do not reload
            updateUrlParameters('cards_per_row', cardsPerRow);
        });

        <?php if ($isCollection) : ?>
            // Event listeners for the delete buttonss
            DELETE_BUTTONS.forEach((button) => {
                button.addEventListener('click', (event) => {
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
        <?php endif; ?>
    }

    /**
     * Update the URL parameters with the new key-value pair.
     * 
     * @param {string} key The key to set
     * @param {string} value The value of the key
     * @param {boolean} reload Whether or not to reload the page
     * @returns {void}
     */
    function updateUrlParameters(key, value, reload = false) {
        const URL = window.location.href;
        const URL_PARTS = URL.split('?');
        const BASE_URL = URL_PARTS[0];
        let queryParams = new URLSearchParams(URL_PARTS[1]);
        queryParams.set(key, value);
        const NEW_URL = BASE_URL + '?' + queryParams.toString();
        if (reload) {
            window.location.href = NEW_URL;
        } else {
            history.pushState(null, '', NEW_URL);
        }
    }

    /**
     * Update the max width of the cards based on the number of cards per row.
     * 
     * @returns {void}
     */
    function updateCardMaxWidth() {
        let maxWidth;

        if (CARDS_PER_ROW_SELECTOR.value == 1 || CARDS_PER_ROW_SELECTOR.value == 5) {
            maxWidth = 100;
        } else {
            maxWidth = 100 - (CARDS_PER_ROW_SELECTOR.value * 10);
        }

        CARDS_CONTAINER.style.setProperty('--max-width', maxWidth + 'vw');
    }

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
            if (i !== columnIndex) { 
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

    /**
     * Checks if the user is on a mobile device (or at minimum is on a small viewport)
     * 
     * @returns {boolean} True if on mobile, False if not.
     */
    function checkIsMobile() {
        return window.innerWidth <= 768
    }
</script>