<?php

/*************************************************************************************************
 * createAccount.php
 *
 * Copyright 2017-2021
 *
 * Creates a user account. This page expects the following request parameters to be present:
 *
 * - email          the user's email address
 * - displayName    the user's display name
 * - password       the user's password
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the account was created successfully
 * - 400 if the given email address is already in use
 * - 409 if the given email address is already in use but with an SSO provider
 * - 500 if the account could not be created for some other reason
 *************************************************************************************************/

require_once '../library.php';

$to = $email; // Save original, unescaped email address
$email = mysqli_real_escape_string($dbh, $email);
$password = mysqli_real_escape_string($dbh, hash_password($password));
$displayName = mysqli_real_escape_string($dbh, $displayName);

$sql = <<<SQL
SELECT id, sso_google_id
  FROM user
 WHERE email = '{$email}'
SQL;

$result = mysqli_query($dbh, $sql);

$count = mysqli_num_rows($result);
if ($count == 0)
{
    $sql = <<<SQL
    INSERT INTO user (email, password, display_name, created, verified)
    VALUES ('{$email}', '{$password}', '{$displayName}', NOW(), 0)
SQL;

    if (mysqli_query($dbh, $sql))
    {
        send_verification_code($to);

        http_response_code(200);
    }
    else
    {
        http_response_code(500);
    }
}
else
{
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if ($row['sso_google_id'] == '')
    {
        // Email already in use
        http_response_code(400);
    }
    else
    {
        // The account uses SSO
        http_response_code(409);
    }
}