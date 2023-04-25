<?php

/*************************************************************************************************
 * authenticateSso.php
 *
 * Copyright 2021
 *
 * Authenticates the user based on the provided ID token from a 3rd party authentication service.
 *
 * - token      the authentication token
 * - provider   the authentication provider (currently only Google is supported)
 *
 * This page returns the following HTTP status codes:
 *
 * - 200 if the credentials were authenticated successfully
 * - 401 if the credentials could not be authenticated
 *************************************************************************************************/

require_once '../library.php';

$ssoId = null;
$displayName = null;
$email = null;

if ($provider == 'google')
{
    $clientId = $config['google']['client_id'];
    $client = new Google_Client(['client_id' => $clientId]);
    $payload = $client->verifyIdToken($token);
    if ($payload)
    {
        $aud = $payload['aud'];
        if ($aud == $clientId)
        {
            $ssoId = $payload['sub'];
            $displayName = $payload['name'];
            $email = $payload['email'];
        }
    }
}

if (!is_null($ssoId))
{
    $sql = <<<SQL
    SELECT id, display_name
      FROM user
     WHERE sso_{$provider}_id = '{$ssoId}'
SQL;

    $result = mysqli_query($dbh, $sql);

    $count = mysqli_num_rows($result);
    if ($count == 0)
    {
        /*
         * The user hasn't used SSO for this provider yet. There are two cases:
         *
         * 1. The user has previously created an account with a matching email address. In this
         *    situation we will merge the SSO information into that existing `user` record and
         *    clear the password to prevent "normal" logins.
         *
         * 2. The user has never logged into the PDC (at least not with this email address). In this
         *    situation we will create a `user` record on the fly.
         *
         * For both cases, we consider the SSO account to be verified and will update the `user`
         * record as such.
         */
        $sql = <<<SQL
        SELECT id, display_name
          FROM user
         WHERE email = '{$email}'
SQL;

        $result = mysqli_query($dbh, $sql);

        $count = mysqli_num_rows($result);
        if ($count == 1)
        {
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $userId = $row['id'];
            $displayName = $row['display_name'];

            $sql = <<<SQL
            UPDATE user
               SET sso_{$provider}_id = '{$ssoId}',
                   password = 'N/A',
                   verified = 1,
                   verification_hash = NULL,
                   verification_expiration = NULL
             WHERE id = $userId
SQL;

            mysqli_query($dbh, $sql);
        }
        else
        {
            $sql = <<<SQL
            INSERT INTO user (email, password, sso_{$provider}_id, display_name, created, verified)
            VALUES ('{$email}', 'N/A', '{$ssoId}', '{$displayName}', NOW(), 1)
SQL;

            mysqli_query($dbh, $sql);
            $userId = mysqli_insert_id($dbh);
        }

    }
    else if ($count == 1)
    {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $userId = $row['id'];
        $displayName = $row['display_name'];
    }

    session_start();

    $_SESSION['userId'] = $userId;
    $_SESSION['displayName'] = $displayName;
    $_SESSION['ssoProvider'] = $provider;
    $_SESSION['authenticated'] = true;

    load_progress(get_default_challenge_year());

    session_write_close();

    http_response_code(200);
}
else
{
    http_response_code(401);
}