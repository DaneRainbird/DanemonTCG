<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= "Danemon TCG | " . $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- OpenGraph tags -->
    <meta property="og:title" content="<?= isset($ogTitle) ? $ogTitle : "Danemon TCG" ?>">
    <meta property="og:description" content="<?= isset($ogDescription) ? $ogDescription : "A simple web app to search for Pokémon TCG cards using the Pokémon TCG API" ?>">
    <meta property="og:image" content="<?= isset($ogImage) ? $ogImage : '/assets/img/logo.png' ?>">
    <meta property="og:url" content="https://danemon.danerainbird.me">
    <meta property="og:type" content="website">
    <meta property="og:logo" content="<?= isset($ogImage) ? $ogImage : '/assets/img/logo.png' ?>">

    <!-- Styles -->
    <?php 
        foreach ($styles as $style) {
            echo "<link rel='stylesheet' href='$style'>";
        }
    ?>
</head>