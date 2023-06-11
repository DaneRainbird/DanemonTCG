<div class="container">
    <div class="col">
        <h2>Danemon TCG</h2>
        <p>Welcome to Danemon TCG - a fan-mode utility designed to allow users to view and manage their Pokémon TCG collections.</p>
        <p>Please note that this website is <em>constantly</em> under development, and things may change. I'm not planning on modifying base functionality, and if I do, it'll be with good reason.</p>
        <p>As of 2023-06-11, I consider this website to be in an <span style="color: red;">alpha</span> state.</p>
        <p>While the site is in alpha, account creation is restricted and must be requested. Please <a href="/about#registering" class="fancy-link">see here for more information</a>.</p>
    </div>
    <div class="col">
        <h2>Search for cards!</h2>
        <div class="search-container">
            <form action="<?= base_url('cards/search') ?>" method="get">
                <input type="text" id="value" name="value" placeholder="Search for a card!" required>
                <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
            </form>
            <p>Supports complex queries! See <a class="fancy-link" href="<?= base_url('about/queries') ?>" target="_blank"> the docs</a> for more info!</p>
        </div>
    </div>
</div>