<?php

/*************************************************************************************************
 * createGroup.php
 *
 * Copyright 2023
 *
 * Creates a group owned by the currently logged in user. This page expects the following request
 * parameters to be present:
 *
 * - name           the name of the group to create
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the group was created successfully
 * - 500 if the group could not be created for some reason
 *************************************************************************************************/

require_once '../library.php';

if (!isset($name) || $name == '')
{
    http_response_code(500);
    exit();
}

$name = mysqli_real_escape_string($dbh, $name);
$joinCode = generate_code(6);

$sql = <<<SQL
INSERT INTO user_group (owner_id, name, join_code)
VALUES ({$_SESSION['userId']}, '{$name}', '{$joinCode}')
SQL;

if (!mysqli_query($dbh, $sql))
{
    http_response_code(500);
    exit();
}

$groupId = mysqli_insert_id($dbh);

$sql = <<<SQL
INSERT INTO user_group_member (group_id, user_id)
VALUES ({$groupId}, {$_SESSION['userId']})
SQL;

if (!mysqli_query($dbh, $sql))
{
    http_response_code(500);
    exit();
}

http_response_code(200);
echo $groupId;

/*
 * Generates a random string of $length digits and/or uppercase letters.
 */
function generate_code($length)
{
    $code = '';

    for ($i = 0; $i < $length; $i++)
    {
        $number = random_int(0, 35);
        if ($number > 9)
        {
            $code .= chr($number - 10 + ord('A'));
        }
        else
        {
            $code .= $number;
        }
    }

    return $code;
}