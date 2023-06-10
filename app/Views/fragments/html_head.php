<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= "Danemon TCG | " . $title ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <?php 
        foreach ($styles as $style) {
            echo "<link rel='stylesheet' href='$style'>";
        }
    ?>
</head>