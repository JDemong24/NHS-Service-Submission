<?php

/*************************************************************************************************
 * geniusBoard.php
 *
 * Copyright 2017-2023
 *
 * Genius board page content. This page is intended to be included in index.php.
 *************************************************************************************************/

$year = get_default_challenge_year();
if (isset($_SESSION['year']))
{
    $year = $_SESSION['year'];
}

$sql = <<<SQL
SELECT u.id AS user_id, u.display_name, u.verified
  FROM progress p
  JOIN user u
    ON u.id = p.user_id
 WHERE p.genius_ind = 1
   AND p.year = {$year}
 ORDER BY p.completion_time
SQL;

$query = mysqli_query($dbh, $sql);

?>

<div class="col-md-6 col-md-offset-3">
    <h2 class="text-center"><?php echo $year ?> Genius Board</h2>
</div>

<div class="col-md-6 col-md-offset-3">
    <a href="index.php?content=leaderBoard" class="btn btn-block">Access Private Leader Boards</a>
</div>

<div class="col-md-6 col-md-offset-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>&nbsp;</th>
                <th>Name</th>
                <th class="text-center">World Rank*</th>
            </tr>
        </thead>
        <tbody>
            <?php

                $rank = 1;
                while($record = mysqli_fetch_array($query, MYSQLI_ASSOC))
                {
                    if ($record['verified'] == 1)
                    {
                        if (isset($_SESSION['userId']) && $record['user_id'] == $_SESSION['userId'])
                        {
                            echo '<tr class="success" style="font-size:300%;">';
                        }
                        else
                        {
                            echo '<tr>';
                        }
                        echo '<td>&nbsp;</td>';
                        echo '<td>' . strip_tags($record['display_name']) . '</td>';
                        echo '<td class="text-center">' . $rank . '</td>';
                        echo '</tr>';
                    }

                    $rank++;
                }

            ?>
        </tbody>
    </table>
</div>

<div class="col-md-6 col-md-offset-3">
    <p><em>* The Genius Board leaves gaps in the rankings for the Geniuses who have not yet verified their email address</em></p>
</div>