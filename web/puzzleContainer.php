<?php

/*************************************************************************************************
 * puzzleContainer.php
 *
 * Copyright 2017-2022
 *
 * Container for the individual puzzles. This page is intended to be included in index.php and
 * expects the following request parameters to be present:
 *
 * - $id          the ID of the puzzle to display
 *************************************************************************************************/

$isGenius = $_SESSION['isGenius'];

// Don't let the user access a puzzle that she/he hasn't unlocked
$challenge = get_current_challenge();
$state = get_puzzle_state($challenge->puzzle[intval($id)]);
if ($state == PUZZLE_STATE_LOCKED || $state == PUZZLE_STATE_INVISIBLE)
{
    include('invalidAccess.php');
    exit();
}

// If this is the first time the user has accessed this puzzle then we need to create a record
create_puzzle_progress($_SESSION['progressId'], $id);

?>

<script type="text/javascript">

/*
 * This variable is kept for challenges from 2021 and earlier. It should not be used beyond 2021.
 */
var wasPreviouslySolved = <?php $state == PUZZLE_STATE_COMPLETE ? print 'true' : print 'false' ?>;

/*
 * Global Variables
 * ================
 *
 * All global variables defined at this level begin with 'pdc_'. Each is described in detail below.
 *
 * pdc_answer - an optional string value that, if set, will be passed to the server to verify a
 *              solution with data from the challenge XML file before marking a puzzle as solved
 *
 * pdc_parameters - an optional, JSON-formatted string value that, if set, will be passed to the
 *                  server to when a puzzle is solved and persisted in the database for future
 *                  reference; this value will ultimately be converted to an object and made
 *                  accessible via the pdc_pbp variable (see below)
 *
 * pdc_pbp - "Parameters by Puzzle" which is a list of "puzzle parameter" objects. Example usage:
 *
 *               pdc_pbp[PUZZLE_ID].parameterName
 *
 *           where PUZZLE_ID is the numeric ID for a puzzle within the current challenge year and
 *           parameterName is a puzzle-specific property. In addition to the puzzle-specific
 *           properties, a 'solved' property is available that is either true or false (this
 *           property replaces the wasPreviouslySolved variable defined above).
 *
 * pdc_user - the display name for the currently logged in user
 */
var pdc_cwz = null;
var pdc_parameters = null;
var pdc_pbp = [];
var pdc_user = "<?php print $_SESSION['displayName'] ?>";

<?php

for ($i = 0; $i < sizeof($_SESSION['puzzleData']); $i++)
{
    $parameter = $_SESSION['puzzleData'][$i];
    if ($parameter != '')
    {
        // Using double quotes in this PHP string because the $parameter value contains double quotes that will conflict with the generated Javascript code
        print "pdc_pbp.push(JSON.parse('" . $parameter . "'));";
    }
    else
    {
        print 'pdc_pbp.push({});';
    }

    $state = get_puzzle_state($challenge->puzzle[$i]);
    print 'pdc_pbp[' . $i . '].solved = ' . ($state == PUZZLE_STATE_COMPLETE ? 'true' : 'false') . ';';
}

?>

function completePuzzle(puzzleId) {
    var parametersString = '';
    if (pdc_parameters) {
        parametersString = JSON.stringify(pdc_parameters);
    }

    var settings = {
        'async': true,
        'url': 'api/completePuzzle.php?id=' + puzzleId + '&cwz=' + pdc_cwz + '&parameters=' + parametersString,
        'method': 'POST',
        'headers': {
            'Cache-Control': 'no-cache'
        }
    };

    $.ajax(settings).done(function(response) {
        window.location.replace('index.php?content=menu');
    }).fail(function(response) {
        if (response.status == 501) {
            showAlert('warning', 'Wrong Answer!', 'Sorry, that\'s not the correct answer. Try again!');
        } else {
            showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
        }
    });
}

</script>

<?php

// Include challenge- and puzzle-specific stylesheets
foreach (get_puzzle_files($id, 'css') as $file)
{
    print '<link href="' . $file . '?v=' . rand() . '" rel="stylesheet" type="text/css" />';
}

// Include challenge- and puzzle-specific scripts
foreach (get_puzzle_files($id, 'js') as $file)
{
    print '<script type="text/javascript" src="' . $file . '?v=' . rand() . '"></script>';
}

if ($_SESSION['year'] == 2017) // Legacy support for the 2017 challenge
{

?>

<div class"container col-md-6">
    <div type="submit"><canvas id="clockCanvas" class="piCanvasClassShow" style="display: none;" width="200" height="200"></canvas></div>
    <form action="javascript:void(0);">
        <input type="button" id="clockMessage" class="piMessageClassShow btn btn-primary" style="display: none;" value="It's time to move on" onclick="completePuzzle(<?php echo $id ?>)" />
    </form>
</div>

<div id="wrapper"><object type="image/svg+xml" data="challenges/<?php echo $_SESSION['year'] ?>/puzzles/fpuzzle<?php echo $id ?>.svg">Your browser does not support SVG</object></div>
<div align="left" id="myMessage">Good Luck</div>

<script type="text/javascript">

function toggleClock(piBoolean) {
	if (piBoolean) {
		document.getElementById("clockCanvas").style["display"] = "block";
		document.getElementById("clockMessage").style["display"] = "block";
	}
}

</script>

<?php

}
else // All challenges after 2017
{
    include(get_puzzle_files($id, 'html')[0]); // There should only be one HTML file
}

?>
