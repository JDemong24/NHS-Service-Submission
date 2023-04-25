<?php

/*************************************************************************************************
 * debugProgress.php
 *
 * Copyright 2023
 *
 * Debug page to show all progress information for a particular year. If the optional request
 * parameter `year` is present then the progress for that year will be displayed. If there is no
 * such parameter then the progress from the current challenge year will be displayed.
 *
 * - $year        the year of the progress to display [OPTIONAL]
 *
 * IMPORTANT: This page is NOT intended for production!
 *************************************************************************************************/

require_once 'library.php';

$challengeYear = get_default_challenge_year();
if (isset($year))
{
    $challengeYear = $year;
}

?>

<html>
    <head>
        <title>PDC Debug Console - Progress</title>

        <style>
            body {
                font-family: Arial;
                font-size: small;
            }

            tr:nth-child(odd){
                background-color: #cccccc;
            }

            th, td {
                font-family: Courier;
                font-size: small;
                max-width: 600px;
                text-align: left;
                overflow: auto;
            }

            .numeric {
                text-align: right;
            }
        </style>
    </head>

    <body>

        <h1>Pi Day Challenge Debug Console - Progress</h1>

        <em><a href="debugUser.php">View User Data</a></em>

        <h2>Puzzle Completion Stats for <?php echo $challengeYear; ?></h2>
        <?php
            $puzzleNames = array();
            $solvedPuzzleCounts = array();
            $challenge = get_challenge($challengeYear);
            foreach ($challenge->puzzle as $puzzle)
            {
                $puzzleNames[] = $puzzle['title'];
                $solvedPuzzleCounts[] = 0;
            }

            $puzzleCount = count($puzzleNames);

            $progressCount = 0;

            $sql = 'SELECT * FROM progress WHERE year = ' . $challengeYear;
            $results = mysqli_query($dbh, $sql);
            while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC))
            {
                $puzzleStates = unserialize($row['puzzle_states']);
                for ($i = 0; $i < $puzzleCount; $i++)
                {
                    if ($puzzleStates[$i] == PUZZLE_STATE_COMPLETE)
                    {
                        $solvedPuzzleCounts[$i] = $solvedPuzzleCounts[$i] + 1;
                    }
                }

                $progressCount++;
            }

            echo '<table>';
            echo '<tr>';
            echo '<th>#</th>';
            echo '<th>Puzzle Name</th>';
            echo '<th>Completion Count</th>';
            echo '<th>Completion Percent</th>';
            echo '</tr>';

            for ($i = 0; $i < $puzzleCount; $i++)
            {
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $puzzleNames[$i] . '</td>';
                echo '<td class="numeric">' . $solvedPuzzleCounts[$i] . '</td>';
                echo '<td class="numeric">' . round($solvedPuzzleCounts[$i] / $progressCount * 100, 2) . '%</td>';
                echo '</tr>';
            }

            echo '</table>';

        ?>

    </body>
</html>