<?php

/*************************************************************************************************
 * completePuzzle.php
 *
 * Copyright 2018-2022
 *
 * Updates the user's account (database and session) by marking the given puzzle as completed.
 * This page expects the following request parameters to be present:
 *
 * - id         the ID of the puzzle that was completed
 * - answer     (optional) the answer entered by the user, a puzzle will only be marked completed if
 *              the answer is correct (i.e., it matches the value from the challenge XML)
 * - parameters (optional) a JSON-formatted value to persist for this puzzle
 *
 * When a puzzle is (correctly) completed, the corresponding `puzzle_progress` record will be
 * updated and any parameters will be persisted in the database.
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the user's account was updated successfully
 * - 400 if the user doesn't have access to the given puzzle
 * - 501 if the user did not supply the correct answer
 * - 500 if the user's account could not be updated for some other reason
 *************************************************************************************************/

require_once '../library.php';

$answer = $cwz;

// Don't let the user access a puzzle that she/he hasn't unlocked
$puzzle = get_puzzle_by_id($id);
$state = get_puzzle_state($puzzle);
if ($state == PUZZLE_STATE_LOCKED || $state == PUZZLE_STATE_INVISIBLE)
{
    http_response_code(400);
    exit();
}

// Confirm that the puzzle was answered correctly (if an answer was supplied)
// if (isset($puzzle['answer']) && $puzzle['answer'] != $answer)
// {
//     http_response_code(501);
//     exit();
// }

/*
 * Now that we know the user has legitimately completed this puzzle, we need to update both the
 * `progress` and `puzzle_progress` records.
 */

// Step 1:  Update the `progress` record (puzzle states, completion time, genius indicator)
$progressId = $_SESSION['progressId'];
$puzzleStates = $_SESSION['puzzleStates'];
$isGenius = $_SESSION['isGenius'];

$puzzleStates[$id] = 1;

$temp = array_count_values($puzzleStates);
$numberOfComplete = $temp[1];

$geniusUpdates = "";
if ($numberOfComplete == count($puzzleStates))
{
    if($isGenius == false)
    {
        $geniusUpdates = ', completion_time = now(), genius_ind = true';
        $isGenius = true;
    }
}

$serial = serialize($puzzleStates);
$sql = <<<SQL
UPDATE progress
   SET puzzle_states = '{$serial}' {$geniusUpdates}
 WHERE id = {$progressId}
SQL;

mysqli_begin_transaction($dbh);

$success = mysqli_query($dbh, $sql);

// Step 2: Update the `puzzle_progress` record (input, answer, end time)
if ($success)
{
    $sql = <<<SQL
    UPDATE puzzle_progress
       SET end_time = now(), parameters = '{$parameters}'
     WHERE progress_id = {$progressId}
       AND puzzle_id = {$id}
SQL;

    $success = mysqli_query($dbh, $sql);
}

if ($success)
{
    mysqli_commit($dbh);

    session_start();

    $_SESSION['puzzleStates'] = $puzzleStates;
    $_SESSION['puzzleData'][$id] = $parameters;
    $_SESSION['solvedCount'] = $numberOfComplete;
    $_SESSION['isGenius'] = ($isGenius ? 1 : 0);

    session_write_close();

    http_response_code(200);
}
else
{
    mysqli_rollback($dbh);

    http_response_code(500);
}
