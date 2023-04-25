<?php

/*************************************************************************************************
 * leaderBoard.php
 *
 * Copyright 2023
 *
 * Private leader board page content. This page is intended to be included in index.php. This page
 * expects the following request parameters to be present:
 *
 * - group (optional) the ID of the group to display, if not present then only the list of groups
 *                    will be displayed
 *************************************************************************************************/

$year = get_default_challenge_year();
if (isset($_SESSION['year']))
{
    $year = $_SESSION['year'];
}

$selectGroupName = '';

?>

<div class="col-md-6 col-md-offset-3">
    <h2 class="text-center"><?php echo $year ?> Private Leader Boards</h2>
    <a href="index.php?content=geniusBoard" class="btn btn-block">View the Global Genius Board</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th width="*">Leader Board</th>
                <th width="1">Join&nbsp;Code</th>
                <th width="1">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $sql = <<<SQL
            SELECT id, name, join_code, owner_id
            FROM user_group
            WHERE id IN (SELECT group_id
                            FROM user_group_member
                        WHERE user_id = {$_SESSION['userId']})
            ORDER BY name
            SQL;

            $count = 0;

            $query = mysqli_query($dbh, $sql);
            while($record = mysqli_fetch_array($query, MYSQLI_ASSOC))
            {
                $selected = isset($group) && $group == $record['id'];

                if ($selected)
                {
                    $selectGroupName = $record['name'];
                }

                echo '<tr>';
                echo '<td class="pointer" onclick="selectGroup(' . $record['id'] . ')">';
                echo $record['name'];
                echo '</td>';
                echo '<td>';
                if ($record['owner_id'] == $_SESSION['userId'])
                {
                    echo '<span class="fixed-width">' . $record['join_code'] . '</span>';
                }
                else
                {
                    echo '<em>Unavailable</em>';
                }
                echo '</td>';

                if ($record['owner_id'] == $_SESSION['userId'])
                {
                    echo '<td><a onclick="deleteGroup(' . $record['id'] . ')"><i class="fa fa-trash" title="Delete this leader board"></a></td>';
                }
                else
                {
                    echo '<td>&nbsp;</td>';
                }

                echo '</tr>';

                $count++;
            }

            if ($count == 0)
            {
                echo '<tr><td colspan="3" style="text-align:center; font-style: italic;">No Records</td></tr>';
            }

            ?>

        </tbody>
    </table>
</div>

<div class="col-md-6 col-md-offset-3 text-center">
    <button type="button" class="btn btn-primary" onclick="joinGroup()">Join a Private Leader Board</button>
    <button type="button" class="btn" onclick="createGroup()">Create a Private Leader Board</button>
</div>

<script>

function createGroup() {
    var name = prompt('Enter a name for your leader board: ');
    if (name != null) {
        if (name != '') {
            var settings = {
                'async': true,
                'url': 'api/createGroup.php?name=' + name,
                'method': 'POST',
                'headers': {
                    'Cache-Control': 'no-cache'
                }
            };

            $.ajax(settings).done(function(response) {
                window.location.replace('index.php?content=leaderBoard&group=' + response);
            }).fail(function(response) {
                showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
            });
        } else {
            showAlert('danger', 'Name Required!', 'You must specify a name for your leader board.');
        }
    }
}

function deleteGroup(id) {
    if (confirm('Are you sure want to delete this leader board?')) {
        var settings = {
            'async': true,
            'url': 'api/deleteGroup.php?id=' + id,
            'method': 'POST',
            'headers': {
                'Cache-Control': 'no-cache'
            }
        };

        $.ajax(settings).done(function(response) {
            window.location.replace('index.php?content=leaderBoard');
        }).fail(function(response) {
            showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
        });
    }
}

function joinGroup() {
    var code = prompt('Enter the leader board code: ');
    if (code != null) {
        if (code != '') {
            var settings = {
                'async': true,
                'url': 'api/joinGroup.php?code=' + code,
                'method': 'POST',
                'headers': {
                    'Cache-Control': 'no-cache'
                }
            };

            $.ajax(settings).done(function(response) {
                window.location.replace('index.php?content=leaderBoard&group=' + response);
            }).fail(function(response) {

                if (response.status == 406) {
                    showAlert('danger', 'Invalid Code!', 'Check the code and try joining again.');
                } else if (response.status == 409) {
                    showAlert('warning', 'Duplicate Code!', 'You are already a member of this leader board.');
                } else {
                    showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
                }


            });
        } else {
            showAlert('danger', 'Code Required!', 'You must enter the leader board code.');
        }
    }
}

function selectGroup(id) {
    window.location.replace('index.php?content=leaderBoard&group=' + id);
}

</script>

<?php

if (isset($group))
{
    /*
     * The calculation for the number of puzzles solved is based on this post:
     *
     * https://stackoverflow.com/questions/12344795/count-the-number-of-occurrences-of-a-string-in-a-varchar-field
     *
     * The strategy goes like this: the serialized `puzzle_states` array lists each puzzle index
     * with a value of either 0 (unsolved) or 1 (solved). Replacing all the instances ':1;' with
     * 'xx' will shorten the length of the string by one more than the number of solved puzzles.
     * The extra ':1;' comes from the index of the second puzzle which is why we subtract 1 from
     * the total.
     */
    $sql = <<<SQL
    SELECT u.id AS user_id, u.display_name, u.verified, p.genius_ind,
           IF(ISNULL(p.puzzle_states), 0, LENGTH(p.puzzle_states) - LENGTH(replace(p.puzzle_states, ':1;', 'xx')) - 1) AS solved_count
      FROM user u
      JOIN user_group_member m    ON m.user_id = u.id
      JOIN user_group g           ON m.group_id = g.id
                                 AND g.id = {$group}
      LEFT OUTER JOIN progress p  ON u.id = p.user_id
                                 AND p.year = {$year}
     ORDER BY solved_count DESC, p.completion_time, u.display_name
    SQL;

    $query = mysqli_query($dbh, $sql);

?>

<div class="col-md-6 col-md-offset-3">
    <h3 class="text-center"><?php echo $selectGroupName; ?></h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th class="text-center">Puzzles Solved</th>
                <th class="text-center">Rank</th>
            </tr>
        </thead>
        <tbody>
            <?php

                $rank = 1;
                $repeats = 0;
                $lastSolvedCount = 0;
                while($record = mysqli_fetch_array($query, MYSQLI_ASSOC))
                {
                    // Private leader boards can have ties (whereas the Genius Board cannot)
                    $solvedCount = $record['solved_count'];
                    if ($solvedCount == $lastSolvedCount && $record['genius_ind'] == 0)
                    {
                        $rank--;
                        $repeats++;
                    }
                    else
                    {
                        $rank += $repeats;
                        $repeats = 0;
                    }

                    // Display the user's name regardless of whether or not the account has been verified
                    if (isset($_SESSION['userId']) && $record['user_id'] == $_SESSION['userId'])
                    {
                        echo '<tr class="success">';
                    }
                    else
                    {
                        echo '<tr>';
                    }
                    echo '<td>' . strip_tags($record['display_name']) . '</td>';
                    echo '<td class="text-center">' . $solvedCount . '</td>';
                    echo '<td class="text-center">' . $rank . '</td>';
                    echo '</tr>';

                    $rank++;
                    $lastSolvedCount = $solvedCount;
                }

            ?>
        </tbody>
    </table>
</div>

<?php

}

?>