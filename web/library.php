<?php

/*************************************************************************************************
 * library.php
 *
 * Copyright 2017-2022
 *
 * Defines helper functions used by index.php.
 *************************************************************************************************/

// Suppress all error and warning messages
//error_reporting(0);

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

/*
 * Global variables
 */

$config = parse_ini_file(__DIR__ . '/../config/config.ini', true);

// Pages/scripts that do not require authentication
$unauthenticatedContents = array('contact', 'geniusBoard', 'login', 'passwordRecovery', 'register', 'releaseNotes');
$unauthenticatedScripts = array('api/authenticate.php', 'api/authenticateSso.php', 'api/createAccount.php', 'api/resetPassword.php');

define('PUZZLE_STATE_OPEN', 0);
define('PUZZLE_STATE_COMPLETE', 1);
define('PUZZLE_STATE_LOCKED', 2);
define('PUZZLE_STATE_INVISIBLE', 3);

/*
 * Standard configuration for all pages
 */

date_default_timezone_set('America/New_York');

extract($_REQUEST);

if (!isset($content) || $content == '' || strpos($content, '://') || !file_exists($content . '.php'))
{
    $content = 'login';
}

session_start();

verify_authentication();

$dbh = get_database_connection();

/*
 * Creates a `progress` record for the given challenge year and returns the ID of that new
 * record.
 *
 * $year - the year for the `progress` record to create
 */
function create_progress($year)
{
    global $dbh;
    $defaultPuzzleStates = array();
    $challenge = get_challenge($year);
    foreach ($challenge->puzzle as $puzzle)
    {
        $defaultPuzzleStates[] = 0;
    }

    $defaultPuzzleStatesSerialized = serialize($defaultPuzzleStates);

    $sql = <<<SQL
    INSERT INTO progress (user_id, year, puzzle_states, completion_time)
    VALUES ({$_SESSION['userId']}, {$year}, '{$defaultPuzzleStatesSerialized}', null)
SQL;

    $progressId = 0;
    if (mysqli_query($dbh, $sql))
    {
        $progressId = mysqli_insert_id($dbh);
    }

    return $progressId;
}

/*
 * Creates a `puzzle_progress` record for the given progress ID and puzzle ID if one does not
 * already exist. This function returns the ID of the `puzzle_progress` record that was either
 * freshly created or just retrieved.
 *
 * $progressId - the ID of a `progress` record
 * $puzzleId - the ID of a puzzle record from the challenge XML file
 */
function create_puzzle_progress($progressId, $puzzleId)
{
    global $dbh;

    $recordId = 0;

    $sql = <<<SQL
    SELECT id
      FROM puzzle_progress
     WHERE progress_id = {$progressId}
       AND puzzle_id = {$puzzleId}
SQL;

    $result = mysqli_query($dbh, $sql);

    $count = mysqli_num_rows($result);
    if ($count == 0)
    {
        $sql = <<<SQL
        INSERT INTO puzzle_progress (progress_id, puzzle_id, start_time)
        VALUES ({$progressId}, {$puzzleId}, now())
SQL;

        if (mysqli_query($dbh, $sql))
        {
            $recordId = mysqli_insert_id($dbh);
        }
    }
    else
    {
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $recordId = $row['id'];
    }

    return $recordId;
}

/*
 * Returns the challenge (i.e., collection of puzzles) for the given year.
 *
 * $year - the year of the challenge to load
 */
function get_challenge($year)
{
    $file = 'challenges/' . $year . '/challenge.xml';
    if (!file_exists($file))
    {
        $file = '../' . $file;
    }

    return simplexml_load_file($file);
}

/*
 * Returns the challenge (i.e., collection of puzzles) for the year specified on the
 * user's session.
 */
function get_current_challenge()
{
    return get_challenge($_SESSION['year']);
}

/*
 * Opens and returns PDO connection to the PDC database.
 */
function get_database_connection()
{
    global $config;

    $host = $config['database']['host'];
    $user = $config['database']['user'];
    $password = $config['database']['password'];
    $database = $config['database']['database'];

    return mysqli_connect($host, $user, $password, $database);
}

/*
 * Returns whether or not debug mode has been enabled as specified in the application config file.
 */
function get_debug_mode()
{
    global $config;

    return $config['general']['debug'];
}

/*
 * Returns the default challenge year as specified in the application config file.
 */
function get_default_challenge_year()
{
    global $config;

    return $config['general']['challenge_year'];
}

/*
 * Returns the puzzle from the current challenge with the corresponding ID or null if no such
 * puzzle exists.
 *
 * $id - the ID of the puzzle to retrieve
 */
function get_puzzle_by_id($id)
{
    $match = null;

    $challenge = get_current_challenge();
    foreach ($challenge->puzzle as $puzzle)
    {
        if ($puzzle['id'] == $id)
        {
            $match = $puzzle;
            break;
        }
    }

    return $match;
}

/*
 * Returns an array of the supporting files (path + file name) required by the given puzzle. This
 * function assumes the puzzle is part of the currently selected challenge year on the user's
 * session.
 *
 * $id - the ID of the puzzle
 * $extension - the type of files to return, either 'css' for stylesheets, 'js' for scripts, or
*               'html' for HTML files
 */
function get_puzzle_files($id, $extension)
{
    $files = array();

    // Step 1: Get the files common to all puzzles from the lib folder
    $path = 'challenges/' . $_SESSION['year'] . '/lib/';
    foreach (scandir($path) as $fileName)
    {
        if (substr($fileName, 0, 1) != '.' &&
            substr($fileName, strrpos($fileName, '.') + 1) === $extension)
        {
            $files[] = $path . $fileName;
        }
    }

    // Step 2: Get the explicitly listed files for this puzzle
    $puzzle = get_puzzle_by_id($id);
    $fileList = explode(',', $puzzle['files']);

    $path = 'challenges/' . $_SESSION['year'] . '/';
    foreach ($fileList as $fileName)
    {
        $fileName = trim($fileName);
        if (substr($fileName, -2) === '.*')
        {
            $fileName = substr($fileName, 0, strlen($fileName) - 2) . '.' . $extension;
        }

        if (file_exists($path . $fileName))
        {
            $files[] = $path . $fileName;
        }
    }

    return $files;
}

/*
 * Determines the "state" of the given puzzle. The return value is one of the PUZZLE_STATE_*
 * constants.
 *
 * $puzzle - the puzzle to check
 */
function get_puzzle_state($puzzle)
{
    $state = PUZZLE_STATE_INVISIBLE;

    if ($_SESSION['puzzleStates'][intval($puzzle['id'])] == 1)
    {
        $state = PUZZLE_STATE_COMPLETE;
    }
    else if ($_SESSION['solvedCount'] >= intval($puzzle['min-solutions']))
    {
        $state = PUZZLE_STATE_OPEN;

        if ($puzzle['unlock-ids'] != '')
        {
            foreach (explode(',', $puzzle['unlock-ids']) as $prereqId)
            {
                if ($_SESSION['puzzleStates'][intval($prereqId)] == 0)
                {
                    $state = PUZZLE_STATE_LOCKED;
                    break;
                }
            }
        }
    }

    return $state;
}

/*
 * Returns the root web folder for the site as specified in the application config file.
 */
function get_script_root()
{
    global $config;

    return $config['general']['script_root'];
}

/*
 * Returns the server name hosting this script along with the accessingn protocol (HTTP or HTTPS).
 * On production this function will return https://www.pidaychallenge.com, in a development
 * environment it will return something like http://localhost.
 */
function get_server_name()
{
    return 'http' . ($_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'];
}

/*
 * Returns a hashed version of the given password suitable for use with the `user` record in the
 * database.
 *
 * $password - the plaintext password
 */
function hash_password($password)
{
    return '*' . strtoupper(hash('sha1', hex2bin(hash('sha1', $password))));
}

/*
 * Loads the `progress` record and it's children 'puzzle_progress' records for the given challenge
 * year and updates the session data. A `progress` record will be created if one does not yet exist
 * for the given challenge year. No `puzzle_progress` records will be created; any puzzles without
 * a `puzzle_progress` record will have a placeholder added to the session data.
 *
 * This function assummes that the calling function will start and close the session.
 *
 * $year - the year for the `progress` record
 */
function load_progress($year)
{
    global $dbh;

    // Step 1: Load the `progress` record
    $sql = <<<SQL
    SELECT *
      FROM progress
     WHERE user_id = {$_SESSION['userId']}
       AND year = {$year}
SQL;

    $result = mysqli_query($dbh, $sql);

    $count = mysqli_num_rows($result);
    if ($count == 0)
    {
        $progressId = create_progress($year);

        $sql = <<<SQL
        SELECT *
          FROM progress
         WHERE id = {$progressId}
SQL;

        $result = mysqli_query($dbh, $sql);
    }

    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $_SESSION['progressId'] = $row['id'];
    $_SESSION['year'] = $row['year'];
    $_SESSION['puzzleStates'] = unserialize($row['puzzle_states']);
    $_SESSION['solvedCount'] = array_count_values($_SESSION['puzzleStates'])[1];
    $_SESSION['isGenius'] = $row['genius_ind'];

    // Step 2: Load the children `puzzle_progress` records
    $data = array();
    for ($i = 0; $i < sizeof($_SESSION['puzzleStates']); $i++)
    {
        $data[$i] = '';
    }

    $sql = <<<SQL
    SELECT puzzle_id, parameters
      FROM puzzle_progress
     WHERE progress_id = {$_SESSION['progressId']}
     ORDER BY puzzle_id
SQL;

    $result = mysqli_query($dbh, $sql);
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
    {
        $data[intval($row[0])] = $row[1];
    }

    $_SESSION['puzzleData'] = $data;
}

/*
 * Prints an HTML-formatted table of the given query results.
 *
 * $results - the query results to display
 */
function pretty_print_query_results($results)
{
    $row = mysqli_fetch_array($results, MYSQLI_ASSOC);
    if ($row)
    {
        $columns = array_keys($row);
        print '<table border="1"><tr>';
        foreach ($columns as $column)
        {
            print '<th>' . $column . '</th>';
        }
        print '</tr>';

        do
        {
            print '<tr>';
            foreach ($columns as $column)
            {
                print '<td nowrap>' . $row[$column] . '</td>';
            }
            print '</tr>';
        }
        while ($row = mysqli_fetch_array($results, MYSQLI_ASSOC));

        print '</table>';
    }
    else
    {
        print 'NO RESULTS';
    }
}

/*
 * Sends an email to a single recipient with the given subject/message. The email is sent from the
 * Gmail address specified in the application config file.
 *
 * $recipient - the recipient's email address
 * $subject - the subject of the email
 * $message - the body of the email
 */
function send_email($recipient, $subject, $message)
{
    global $config;

    $mail = new PHPMailer();

    try
    {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;

        $mail->isSMTP();

        // $mail->SMTPDebug = 0;
        // $mail->Debugoutput = 'html';

        $mail->Host = 'smtp.gmail.com';
        $mail->Port = 465;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->AuthType = 'XOAUTH2';

        $email = $config['google']['email'];
        $name = $config['google']['name'];
        $clientId = $config['google']['client_id'];
        $clientSecret = $config['google']['client_secret'];
        $refreshToken = $config['google']['refresh_token'];

        $provider = new Google(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
            ]
        );

        $mail->setOAuth(
            new OAuth(
                [
                    'provider' => $provider,
                    'clientId' => $clientId,
                    'clientSecret' => $clientSecret,
                    'refreshToken' => $refreshToken,
                    'userName' => $email,
                ]
            )
        );

        $mail->setFrom($email, $name);
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->msgHTML($message);

        $mail->send();
    }
    catch (Exception $e)
    {
        // Ignore the error, the recipient's email address is likely invalid
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
    catch (\Exception $e)
    {
        // For development debugging
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

/*
 * Generates and sends a verification code to the given email address. The recipient will have one
 * hour to verify the address.
 *
 * $email - the recipient's email address
 */
function send_verification_code($email)
{
    global $dbh;

    $to = $email; // Save original, unescaped email address
    $email = mysqli_real_escape_string($dbh, $email);

    // Generate a random 32-character verification hash code
    $verificationHash = hash('md5', uniqid($to, true));

    $url = get_server_name() . get_script_root() . 'index.php?content=verifyEmail&hash=' . $verificationHash;

    $sql = <<<SQL
    UPDATE user
       SET verification_hash = '{$verificationHash}',
           verification_expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR)
     WHERE email = '{$email}'
SQL;

    // Update the user's record in the database
    if (mysqli_query($dbh, $sql))
    {
        // Now send the email with the link
        $subject = 'Pi Day Challenge Email Verification';
        $message = 'Thank you for participating in the Pi Day Challenge!<br />' .
                   'Click this link to verify your email address:<br />' .
                   '<a href="' . $url . '">' . $url . '</a><br />' .
                   'The link expires in one hour.';
        send_email($to, $subject, $message);
    }
}

/*
 * Verifies that the current session has been authenticated. If not then the script exits and the
 * client browser is redirected to the login page. Note that some pages (e.g., the login page) do
 * not require authentication.
 */
function verify_authentication()
{
    global $config, $content, $unauthenticatedContents, $unauthenticatedScripts;

    $scriptName = substr($_SERVER['PHP_SELF'], strlen(get_script_root()));

    if (!(($scriptName == 'index.php' && in_array($content, $unauthenticatedContents)) ||
          in_array($scriptName, $unauthenticatedScripts)))
    {
        if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated'])
        {
            session_unset();
            session_destroy();
            session_write_close();

            header('Location: index.php');

            exit();
        }
    }
}