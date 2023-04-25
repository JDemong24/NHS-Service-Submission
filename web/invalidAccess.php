<?php

/*************************************************************************************************
 * invalidAccess.php
 *
 * Copyright 2017
 *
 * General "error" page to be displayed whenever the user tries to go somewhere they shouldn't
 * (e.g., attempting to view a puzzle they have yet to unlock). This page is intended to be
 * included in index.php.
 *************************************************************************************************/

?>

<div class="jumbotron col-md-8 col-md-offset-2">
    <h2 class="text-center">Oops!</h2>
    <p class="text-center">Looks like you're on the wrong page...</p>
    <p class="text-center">Go back to the <a href="index.php?content=menu">puzzle menu</a> and try again.</p>
</div>