<?php

/*************************************************************************************************
 * debugUser.php
 *
 * Copyright 2022-2023
 *
 * Debug page to show all relevant information for a particular user. If the optional request
 * parameter `id` is present then the user with that ID will be displayed. If there is no such
 * parameter then the user from the current session will be displayed.
 *
 * - $id          the ID of the user to display [OPTIONAL]
 *
 * IMPORTANT: This page is NOT intended for production!
 *************************************************************************************************/

require_once 'library.php';

$userId = $_SESSION['userId'];
if (isset($id))
{
    $userId = $id;
}

?>

<html>
    <head>
        <title>PDC Debug Console - User</title>

        <style>
            body {
                font-family: Arial;
                font-size: small;
            }

            th, td {
                text-align: left;
                font-family: Courier;
                font-size: small;
                max-width: 600px;
                overflow: auto;
            }
        </style>
    </head>

    <body>

        <h1>Pi Day Challenge Debug Console - User</h1>

        <em><a href="debugProgress.php">View Progress Data</a></em>

        <h2>User Record</h2>
        <?php
            $sql = 'SELECT * FROM user WHERE id = ' . $userId;
            $result = mysqli_query($dbh, $sql);
            pretty_print_query_results($result);
        ?>

        <h2>Progress Records</h2>
        <?php
            $sql = 'SELECT * FROM progress WHERE user_id = ' . $userId . ' ORDER BY year';
            $result = mysqli_query($dbh, $sql);
            pretty_print_query_results($result);
        ?>

        <h2>Puzzle Progress Records</h2>
        <?php
            $sql = 'SELECT * FROM puzzle_progress WHERE progress_id in (SELECT id from progress WHERE user_id = ' . $userId . ') ORDER BY progress_id, puzzle_id';
            $result = mysqli_query($dbh, $sql);
            pretty_print_query_results($result);
        ?>

<?php if (!isset($id)) { ?>

        <h2>Session Data</h2>
        <pre>
        <?php print_r($_SESSION); ?>
        </pre>

<?php } ?>

    </body>
</html>