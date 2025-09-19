<div class="container">
    <h2>Profile</h2>
    <p>Welcome to your profile page, <?= session()->get('username') ?>!</p>
    <p>Here you can view your collections, and maybe eventually do other things. It's a work in progress.</p>

    <h2>My Collections</h2>
    <?php if (session()->get('isAdmin') !== 'true') : ?>
        <?php if (!empty($collections)) : ?>
            <p>Click on any of your collections to see their cards, or you can see <a class="fancy-link" href="/collections/viewAll" target="_blank">all of the cards you have added to a collection</a>.</p>
            <p>You can also search using the below search function!</p>
            <div class="search-container">
                <form action="<?= base_url('collections/search') ?>" method="get">
                    <input type="text" id="value" name="value" placeholder="Search for a card!" required>
                    <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
                </form>
            </div>
            <div class="series-sets collections">
                <?php foreach ($collections as $collection) : ?>
                    <div class="set-container">
                        <a href="/collections/view/<?= $collection['id'] ?>">
                            <div class="set">
                                <div class="set-info">
                                    <p><strong><?= $collection['name'] ?></strong></p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>You don't have any collections yet! Why not check out <a class="fancy-link" href="/cards">some cards</a> and make some?</p>
        <?php endif; ?>
    <?php else : ?>
        <p>You are logged in as an admin, and thus you are able to view all of the collections in the system.</p>
        <div class="search-container" style="display: flex; padding-bottom: 1em;">
            <input type="text" id="userFilter" onkeyup="filterTable()" placeholder="Enter an email address">
            <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg></button>
        </div>
        <table class="pretty-table" id="admin-table" style="margin-bottom: 1em;">
            <thead>
                <tr>
                    <th>Collection ID</th>
                    <th>Collection Name</th>
                    <th>Owner</th>
                    <th>View</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($collections as $collection) : ?>
                    <tr>
                        <td><?= $collection['id'] ?></td>
                        <td><?= $collection['name'] ?></td>
                        <td><?= $collection['username'] ?></td>
                        <td><a class="fancy-link" href="/collections/view/<?= $collection['id'] ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <script>
            function filterTable() {
                var filter = document.getElementById('userFilter').value.toLowerCase();
                var rows = document.getElementById('admin-table').getElementsByTagName('tr');

                for (var i = 1; i < rows.length ; i++) {
                    var username = rows[i].getElementsByTagName('td')[2].innerText.toLowerCase();
                    if (username === filter || username.includes(filter)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        </script>
    <?php endif; ?>
</div>