<?php

/*************************************************************************************************
 * sendVerificationEmail.php
 *
 * Copyright 2020
 *
 * Send a verification email to the address stored in the database for the current user. This page
 * assumes that the email address has not already been verified.
 *
 * This page always returns a 200 HTTP status code.
 *************************************************************************************************/

require_once '../library.php';

$sql = <<<SQL
SELECT email
  FROM user
 WHERE id = {$_SESSION['userId']}
SQL;

$result = mysqli_query($dbh, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

send_verification_code($row['email']);

http_response_code(200);