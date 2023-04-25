<?php

/*************************************************************************************************
 * joinGroup.php
 *
 * Copyright 2023
 *
 * Joins the currently logged in user to a group. This page expects the following request
 * parameters to be present:
 *
 * - code           the code of the group to join
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the group was joined successfully
 * - 406 if the group code was invalid
 * - 409 if the user is already a member of the group
 *************************************************************************************************/

require_once '../library.php';

if (!isset($code) || $code == '')
{
    http_response_code(500);
    exit();
}

$code = mysqli_real_escape_string($dbh, strtoupper($code));

$sql = <<<SQL
SELECT id
  FROM user_group
 WHERE join_code = '{$code}'
SQL;

$query = mysqli_query($dbh, $sql);
$record = mysqli_fetch_array($query, MYSQLI_ASSOC);
$groupId = $record['id'];

if (!isset($groupId))
{
    http_response_code(406);
    exit();
}

$sql = <<<SQL
INSERT INTO user_group_member (group_id, user_id)
VALUES ({$groupId}, {$_SESSION['userId']})
SQL;

if (!mysqli_query($dbh, $sql))
{
    http_response_code(409);
    exit();
}

http_response_code(200);
echo $groupId;