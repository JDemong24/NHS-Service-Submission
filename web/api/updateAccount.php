<?php

/*************************************************************************************************
 * updateAccount.php
 *
 * Copyright 2017-2021
 *
 * Updates a user account. This page expects some combination of the following request parameters
 * to be present:
 *
 * - securityCheck  the user's current password; the password cannot be updated unless this value
 *                  matches the current password
 * - challengeYear  the challenge year from which to display puzzles (this value is only stored on
 *                  the user's session, it is not persisted in the database)
 * - email          the new email for the user; a new email will be considered unverified and
 *                  trigger a verification email to be sent; this value will not be updated if the
 *                  account is configured to authenticate with an SSO provider
 * - displayName    the new display name for the user
 * - password       the new password for the user; this value will not be updated if the
 *                  account is configured to authenticate with an SSO provider
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the account was updated successfully
 * - 401 if the security check did not pass
 * - 409 if the specified email address is already in use
 * - 500 if the account could not be updated for some reason
 *************************************************************************************************/

require_once '../library.php';

$sql = <<<SQL
SELECT *
  FROM user
 WHERE id = {$_SESSION['userId']}
SQL;

$result = mysqli_query($dbh, $sql);

$originalValues = mysqli_fetch_array($result, MYSQLI_ASSOC);

$ssoAccount = $originalValues['sso_google_id'] != '';

if (isset($password) && !$ssoAccount && hash_password($securityCheck) != $originalValues['password'])
{
    // The security check didn't match the current password
    http_response_code(401);
}
else
{
    $sql = 'UPDATE user SET ';

    $hasFields = false;

    if (isset($password) && hash_password($securityCheck) == $originalValues['password'] && !$ssoAccount)
    {
        $sql .= 'password = \'' . mysqli_real_escape_string($dbh, hash_password($password)) . '\'';
        $hasFields = true;
    }

    if (isset($displayName) && $displayName != $originalValues['display_name'])
    {
        if ($hasFields)
        {
            $sql .= ', ';
        }

        $sql .= 'display_name = \'' . mysqli_real_escape_string($dbh, $displayName) . '\'';
        $hasFields = true;
    }

    if (isset($email) && $email != $originalValues['email'] && !$ssoAccount)
    {
        if ($hasFields)
        {
            $sql .= ', ';
        }

        $sql .= 'email = \'' . mysqli_real_escape_string($dbh, $email) . '\', verified = 0';
        $hasFields = true;
    }

    $sql .= ' WHERE id = ' . $_SESSION['userId'];

    if (!$hasFields || mysqli_query($dbh, $sql))
    {
        session_start();

        if (isset($displayName) && $_SESSION['displayName'] != $displayName)
        {
            $_SESSION['displayName'] = $displayName;
        }

        if (isset($challengeYear) && $_SESSION['year'] != $challengeYear)
        {
            load_progress($challengeYear);
        }

        session_write_close();

        http_response_code(200);
    }
    else
    {
        if (isset($email))
        {
             // The new email address is assigned to another user
            http_response_code(409);
        }
        else
        {
            http_response_code(500);
        }
    }
}