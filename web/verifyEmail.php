<?php

/*************************************************************************************************
 * verifyEmail.php
 *
 * Copyright 2020
 *
 * Email verification page. This page is intended to be included in index.php and expects the
 * following request parameters to be present:
 *
 * - $hash          the hashed verification code
 *************************************************************************************************/

$userId = $_SESSION['userId'];

$sql = <<<SQL
SELECT email,
       verified,
       IF(verification_hash = '{$hash}', 1, 0)   AS code_match,
       IF(verification_expiration > NOW(), 1, 0) AS still_valid
  FROM user
 WHERE id = {$userId}
SQL;

$result = mysqli_query($dbh, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$messageLine1 = '';
$messageLine2 = '';

if ($row['verified']) // if already verified
{
    $messageLine1 = 'Your email address has already been verified.';
    $messageLine2 = 'Now get back to <a href="index.php?content=menu">solving those puzzles</a>!';
}
else if ($row['code_match'])
{
    if ($row['still_valid'])
    {
        // update account
        $sql = <<<SQL
        UPDATE user
           SET verified = 1,
               verification_hash = NULL,
               verification_expiration = NULL
         WHERE id = {$userId}
SQL;

        mysqli_query($dbh, $sql);

        $messageLine1 = 'Thank you, your email address has been verified!';
    }
    else
    {
        send_verification_code($row['email']);

        $messageLine1 = 'Sorry, this verification link has expired.';
        $messageLine2 = 'We just sent you an email with a new code that will be valid for one hour.';
    }
}
else
{
    $messageLine1 = 'D\'oh! This is an invalid verification link.';
    $messageLine2 = 'Please go to <a href="index.php?content=settings">settings</a> to check your email address and get another link.';
}

?>

<div class="jumbotron col-md-8 col-md-offset-2">
    <h2><?php echo $messageLine1; ?></h2>
    <p><?php echo $messageLine2; ?></p>
</div>