<?php $commit = getGitCommitLink(); ?>

<div class="container">
    <h2 id="about">About!<span class="copy">🔗</span></h2>
    <p>Welcome to Danemon TCG! This website is a fan-mode utility designed to allow users to view and manage their Pokémon TCG collections.</p>
    <p>It is not affiliated with The Pokémon Company or Nintendo in any way.</p>
    <p>This project is entirely open source, and the code is available on <a class="fancy-link" href="https://github.com/DaneRainbird/DanemonTCG" target="_blank">GitHub</a>. The version of Danemon currently being served is <a class="fancy-link" href="<?= $commit['url'] ?>" target="_blank"><?= $commit['short_hash'] ?></a>, which is running on <a class="fancy-link" href="https://codeigniter.com/user_guide/changelogs/v<?= \CodeIgniter\CodeIgniter::CI_VERSION ?>.html" target="_blank">CodeIgniter version <?= \CodeIgniter\CodeIgniter::CI_VERSION ?></a>.</p>

    <h2 id="registering">Registering<span class="copy">🔗</span></h2>
    <p>While the site is in alpha, I've restricted account creation.</p>
    <p>Feel free to send me an email at <span class="code">danemon [at] danerainbird.me</span>, and let me know why you'd like to join.</p>

    <h2 id="suggestions">Suggestions / Comments?<span class="copy">🔗</span></h2>
    <p>Suggestions, comments, and criticism are welcome (but please make sure it's constructive!).</p>
    <p>Feel free to send me an email at <span class="code">danemon [at] danerainbird.me</span>. I'll get back to you as soon as I can!</p>

    <h2 id="credits">Credits / Thanks<span class="copy">🔗</span></h2>
    <p>Huge thanks to the following people / groups that have made this project possible:</p>
    <ul>
        <li><a class="fancy-link" href="https://pokemontcg.io" target="_blank">The Pokémon TCG API</a>, for providing the data used by this site,</li>
        <li><a class="fancy-link" href="https://www.pokemon.com/us" target="_blank">The Pokémon Company</a>, for creating the Pokémon TCG, and</li>
        <li>My partner Kim, for giving me the idea for the project in general. This is for her.</li>
    </ul>
    <br />
</div>