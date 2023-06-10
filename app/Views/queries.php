<div class="container">
    <h2 id="keywords">Advanced Querying<span class="copy">🔗</span></h2>
    <p>Danemon TCG supports querying the results via the use of one (or more) of the below keywords:</p>
    <div class="code-block">
        <p>name
            subtypes
            supertype
            types
            rules
            attacks.name
            weaknesses.type
            retreatCost
            nationalPokedexNumbers
            hp
            rarity
            set.id
            set.name
            set.series
            set.ptcgoCode
            number
            artist
        </p>
    </div>

    <p>For example, to search for all Pikachu cards in the Sword and Shield sets, you would use the following query:</p>
    <div class="code-block">
        <p>name:pikachu set.series:Sword & Shield</p>
    </div>

    <p>Detailed specifics for the available queries can be found at the <a href="https://docs.pokemontcg.io/" target="_blank" class="fancy-link">Pokémon TCG API Documentation</a>, but some useful values can also be found below:</p>

    <h2 id="types">Types<span class="copy">🔗</span></h2>
    <p>The following are considered valid Types in the Pokémon TCG API database:</p>
    <div class="code-block">
        <p>Colourless
            Darkness
            Dragon
            Fairy
            Fighting
            Fire
            Grass
            Lightning
            Metal
            Psychic
            Water
        </p>
    </div>

    <h2 id="subtypes">Subtypes<span class="copy">🔗</span></h2>
    <p>The following are considered valid Subtypes in the Pokémon TCG API database:</p>
    <div class="code-block">
        <p>BREAK
            Baby
            Basic
            EX
            GX
            Goldenrod Game Corner
            Item
            LEGEND
            Level-Up
            MEGA
            Pokémon Tool
            Pokémon Tool F
            Rapid Strike
            Restored
            Rocket's Secret Machine
            Single Strike
            Special
            Stadium
            Stage 1
            Stage 2
            Supporter
            TAG TEAM
            Technical Machine
            V
            VMAX
        </p>
    </div>

    <h2 id="supertypes">Supertypes<span class="copy">🔗</span></h2>
    <p>The following are considered valid Supertypes in the Pokémon TCG API database:</p>
    <div class="code-block">
        <p>Energy
            Pokémon
            Trainer
        </p>
    </div>

    <h2 id="rarities">Rarities<span class="copy">🔗</span></h2>
    <p>The following are considered valid rarities in the Pokémon TCG API database:</p>
    <div class="code-block">
        <p>Amazing Rare
            Common
            LEGEND
            Promo
            Rare
            Rare ACE
            Rare BREAK
            Rare Holo
            Rare Holo EX
            Rare Holo GX
            Rare Holo LV.X
            Rare Holo Star
            Rare Holo V
            Rare Holo VMAX
            Rare Prime
            Rare Prism Star
            Rare Rainbow
            Rare Secret
            Rare Shining
            Rare Shiny
            Rare Shiny GX
            Rare Ultra
            Uncommon
        </p>
    </div>
</div>