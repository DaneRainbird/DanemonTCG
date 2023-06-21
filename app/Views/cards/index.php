<?php 
    $sampleSearches = [
        '0' => ['all Grass-type Rare cards from the "Paldea Evolved" set', 'set.id:sv2 types:grass rarity:"Rare"'],
        '1' => ['all cards with "Charizard" in their name', 'name:Charizard'],
        '2' => ['all cards with "Charizard" in their name, but only from the "Vivid Voltage" set', 'name:Charizard set.id:swsh4'],
        '3' => ['all Grass-type cards', 'types:grass'],
    ];

    // Select a random sample search
    $sampleSearch = $sampleSearches[array_rand($sampleSearches)];
?> 

<div class="container">
    <h2>Search</h2>
    <div class="search-container">
        <form action="<?= base_url('cards/search') ?>" method="get">
            <input type="text" id="value" name="value" placeholder="Search for a card!" required>
            <button type="submit" class="search-button"><svg xmlns="http://www.w3.org/2000/svg" height="16" width="16" viewBox="0 0 512 512"><path d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"/></svg></button>
        </form>
        <p>Try searching <span class="code"><?= $sampleSearch[1] ?></span> to get <?= $sampleSearch[0]; ?>!</p>
    </div>
    <h2>How do I search?</h2>
    <p>Danemon supports two main "ways" of searching - either "simple", or "complex".</p>
    <h3>Simple</h3>
    <p>Simple searching is the default search method. It will search for cards whose name match the value you entered in the search box. For example, if you search for <span class="code">Charizard</span>, you will get all cards that have <span class="code">Charizard</span> in their name.</p>
    <h3>Complex</h3>
    <p>Complex searching is a bit more advanced, but allows you to search for cards based on a variety of criteria. For example, if you search for <span class="code">name:Charizard rarity:vmax</span>, you will get all VMAX cards that have <span class="code">Charizard</span> in their name. You can also search for cards based on their set, rarity, type, and more!</p>
    <p>See <a class="fancy-link" href="<?= base_url('about/queries') ?>" target="_blank">the docs</a> for more information!</p>
</div>