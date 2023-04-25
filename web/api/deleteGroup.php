<?php

/*************************************************************************************************
 * deleteGroup.php
 *
 * Copyright 2023
 *
 * Deletes the specified group and all its members ONLY IF the currently logged in user is the
 * owner of the group. This page expects the following request parameters to be present:
 *
 * - id              the id of the group to delete
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the group was deleted successfully
 * - 500 if the group could not be deleted for some reason
 *************************************************************************************************/

require_once '../library.php';

if (!isset($id) || $id == '')
{
    http_response_code(500);
    exit();
}

$code = mysqli_real_escape_string($dbh, strtoupper($code));

$sql = <<<SQL
SELECT id
  FROM user_group
 WHERE id = {$id}
   AND owner_id = {$_SESSION['userId']}
SQL;

$query = mysqli_query($dbh, $sql);
$record = mysqli_fetch_array($query, MYSQLI_ASSOC);
$groupId = $record['id'];

if (!isset($groupId))
{
    http_response_code(500);
    exit();
}

mysqli_begin_transaction($dbh);
try
{
    // Delete the group
    $sql = <<<SQL
    DELETE FROM user_group WHERE id = {$id};
    SQL;

    if (!mysqli_query($dbh, $sql))
    {
        mysqli_rollback($dbh);
        http_response_code(500);
        exit();
    }

    // Delete the members
    $sql = <<<SQL
    DELETE FROM user_group_member WHERE group_id = {$id};
    SQL;

    if (!mysqli_query($dbh, $sql))
    {
        mysqli_rollback($dbh);
        http_response_code(500);
        exit();
    }

    mysqli_commit($dbh);
}
catch (mysqli_sql_exception $exception)
{
    mysqli_rollback($dbh);
}

http_response_code(200);