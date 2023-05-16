<?php

/*************************************************************************************************
 * authenticate.php
 *
 * Copyright 2018-2021
 *
 * Authenticates the user based on the provided email address and password (sent as request
 * parameters).
 *
 * - email      the user's email address
 * - password   the user's password
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the credentials were authenticated successfully
 * - 401 if the credentials could not be authenticated
 *************************************************************************************************/

require_once '../library.php';

$email = mysqli_real_escape_string($dbh, $email);
$password = mysqli_real_escape_string($dbh, hash_password($password));

$sql = <<<SQL
SELECT id, display_name, isAdmin
  FROM user
 WHERE email = '{$email}'
   AND password = '{$password}'
SQL;

$result = mysqli_query($dbh, $sql);

$count = mysqli_num_rows($result);
if ($count == 1)
{
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    session_start();

    $_SESSION['userId'] = $row['id'];
    $_SESSION['displayName'] = $row['display_name'];
    $_SESSION['ssoProvider'] = null;
    $_SESSION['authenticated'] = true;
    $_SESSION['isAdmin'] = $row['isAdmin'];

    load_progress(get_default_challenge_year());

    session_write_close();

    http_response_code(200);
}
else
{
    http_response_code(401);
}