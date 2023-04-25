<?php

/*************************************************************************************************
 * genius.php
 *
 * Copyright 2017
 *
 * Genius "certificate" content. This page is intended to be included in index.php.
 *************************************************************************************************/

$isGenius = $_SESSION['isGenius'];

if (!$isGenius)
{
    include('invalidAccess.php');
    exit();
}

?>

<div class="jumbotron col-md-8 col-md-offset-2">
    <h2 class="text-center">Certificate of Genius</h2>
    <p class="text-center">This certificate is awarded to</p>
    <h1 class="text-center"><?php echo $_SESSION['displayName'] ?></h1>
    <p>For successfully solving all of the puzzles of the <?php echo $_SESSION['year'] ?> Pi Day Challenge</p>
</div>