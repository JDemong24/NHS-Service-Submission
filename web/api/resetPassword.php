<?php

/*************************************************************************************************
 * resetPassword.php
 *
 * Copyright 2017-2021
 *
 * Resets the password for the user account corresponding to the given email. This page expects the
 * following request parameters to be present:
 *
 * - email      the email address of the user account to update
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the account's password was reset successfully
 * - 400 if the given email address did not match a user account
 * - 409 if the given email address authenticates with an SSO provider
 * - 500 if the password could not be reset for some other reason
 *************************************************************************************************/

require_once '../library.php';

$to = $email; // Save original, unescaped email address
$email = mysqli_real_escape_string($dbh, $email);

$sql = <<<SQL
SELECT id, sso_google_id
  FROM user
 WHERE email = '{$email}'
SQL;

$result = mysqli_query($dbh, $sql);

$count = mysqli_num_rows($result);
if ($count == 0)
{
    // Invalid email
    http_response_code(400);
}
else
{
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    if ($row['sso_google_id'] != '')
    {
        // The account uses SSO, we can't reset the password
        http_response_code(409);
    }
    else
    {
        // Reset the password to a random 7-digit number
        $password = (string) rand(1000000, 9999999);
        $passwordHash = hash_password($password);

        $sql = <<<SQL
        UPDATE user
           SET password = '{$passwordHash}'
         WHERE email = '{$email}'
SQL;

        if (mysqli_query($dbh, $sql))
        {
            // Send email with new password
            $subject = 'Pi Day Challenge Password Reset';
            $message = 'Your new password is ' . $password;
            send_email($to, $subject, $message);

            http_response_code(200);
        }
        else
        {
            http_response_code(500);
        }
    }
}