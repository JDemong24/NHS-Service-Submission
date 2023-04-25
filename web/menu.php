<?php

/*************************************************************************************************
 * menu.php
 *
 * Copyright 2017-2023
 *
 * Puzzle menu page. This page is intended to be included in index.php.
 *************************************************************************************************/


if ($_SESSION['isGenius'])
{
    $sql = 'SELECT verified FROM user WHERE id = ' . $_SESSION['userId'];
    $result = mysqli_query($dbh, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $verifyPrompt = ($row['verified'] == 1 ? '' : ' Head over to <a href="index.php?content=settings">settings</a> and verify your email address so you can be listed on the Genius Board.');

?>

<div class="jumbotron col-md-8 col-md-offset-2">
    <h2>You are a Genius!</h2>
    <p>Congrats on completing the Pi Day Challenge.<?php echo $verifyPrompt; ?></p>
    <div class="container col-md-6 col-md-offset-3">
        <a class="btn btn-primary btn-block" href="index.php?content=genius" role="button">See your Genius Certificate</a>
    </div>
</div>

<?php

}

$challenge = get_current_challenge();

// Header with challenge year
echo <<<HTML
<div class="col-md-8 col-md-offset-2">
    <h2 class="text-center">{$challenge['year']} Puzzles</h2>
</div>
HTML;

const colorBlue = '#0073e6';
const colorGreen = '#248f24';
const colorRed = '#cc0000';
const colorYellow = '#999900';
const colorOffWhite = '#f2f2f2';
const colorLightBlue = '#95bbed';
const colorLightGreen = '#c2f2d1';
const colorLightRed = '#f08e89';

const styleInfo = 'info';
const styleSuccess = 'success';
const styleDanger = 'danger';
const styleWarning = 'warning';

const iconCircle = 'fa-circle-o';
const iconCheckCircle = 'fa-check-circle-o';
const iconLock = 'fa-lock';
const iconLowVision = 'fa-low-vision';

$displayMode = 'default';
$debugMode = false; // Used for debugging purposes only

// Update variables based on the challenge year
if ($challenge['year'] == 2023)
{
    $displayMode = 'ticket';
}

echo '<div class="row col-md-12 col-sm-12" style="display: flex; flex-flow: row wrap; justify-content: center;">';
foreach ($challenge->puzzle as $puzzle)
{
    $state = get_puzzle_state($puzzle);
    display_puzzle($puzzle, $displayMode, $state, $debugMode);
}
echo '</div>';

function display_puzzle($puzzle, $displayMode, $state, $showHidden)
{
    $color = '';
    $backgroundColor = '';
    $style = '';
    $faIcon = '';
    $includeLink = true;

    if ($state == PUZZLE_STATE_OPEN)
    {
        $color = colorBlue;
        $backgroundColor = colorLightBlue;
        $style = styleInfo;
        $faIcon = iconCircle;
    }
    elseif ($state == PUZZLE_STATE_COMPLETE)
    {
        $color = colorGreen;
        $backgroundColor = colorLightGreen;
        $style = styleSuccess;
        $faIcon = iconCheckCircle;
    }
    elseif ($state == PUZZLE_STATE_LOCKED)
    {
        $color = colorRed;
        $backgroundColor = colorLightRed;
        $style = styleDanger;
        $faIcon = iconLock;
    }
    else if ($state == PUZZLE_STATE_INVISIBLE)
    {
        $color = colorYellow;
        $backgroundColor = colorOffWhite;
        $includeLink = false;
        $style = styleWarning;
        $faIcon = iconLowVision;
        if (!$showHidden)
        {
            return;
        }
    }

    $details = '&nbsp;';
    if ($puzzle['unlock-ids'] != '')
    {
        $details = '<i class="fa fa-lock" aria-hidden="true"></i> ' . $puzzle['unlock-ids'];
    }

    if ($includeLink)
    {
        echo '<a href="index.php?content=puzzleContainer&id=' . $puzzle['id'] . '">';
    }

    if ($displayMode == 'default')
    {
        echo '
            <div class="col-md-2 col-sm-6">
                <div class="panel panel-' . $style . '" style="width: 150px; padding-left: 0; padding-right: 0; color:' . $color . '; background-color:#f2f2f2;">
                    <div class="panel-heading">
                        <p><span class="panel-title">' . $puzzle['id'] . ': </span>' . $puzzle['title'] . '</p>
                    </div>
                    <div class="panel-body">
                        <p class="text-center"><i class="fa ' . $faIcon . ' fa-5x" aria-hidden="true"></i></p>
                    </div>
                    <div class="panel-footer">
                        <p class="text-center">' . $details . '</p>
                    </div>
                </div>
            </div>';
    }
    elseif ($displayMode == 'ticket')
    {
        $stamp = '';
        if ($state == PUZZLE_STATE_COMPLETE)
        {
            $rotateDeg = rand(-15, 15);
            $stamp = '<p class="" style="position: absolute; top: 28%; right: 14%; margin-top: -30px; opacity: 0.3; rotate: ' . $rotateDeg . 'deg;"><i class="fa ' . $faIcon . ' fa-5x" aria-hidden="true"></i></p>';
        }

        echo '
            <div class="col-md-4 col-sm-6">
                <div class="ticket" style="padding-left: 1px; padding-right: 1px; color: ' . $color . '; background-color:' . $backgroundColor . ';">
                    <div style="border: 4px double ' . $color . '; border-radius: 5px; height: 100%; width: 80%; margin: auto;">
                        <p class="text-center" style="font-size: 24px;">' . $puzzle['id']  .'</p>
                        <p class="text-center" style="font-size: 24px;">' . $puzzle['title'] .'</p>
                        ' . $stamp . '
                        <p class="text-right" style="font-size: 18px; margin-right: 1em;">' . $details . '</p>
                    </div>
                </div>
            </div>
        ';
    }

    if ($includeLink)
    {
        echo '</a>';
    }
}

?>