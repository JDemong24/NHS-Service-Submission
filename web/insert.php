<link rel="stylesheet" href="styles.css">
<?php

include('library.php');
extract($_REQUEST);
$conn = get_database_connection();

// Sanitize all input values to prevent SQL injection exploits
$date = $conn->real_escape_string($date);
$firstname = $conn->real_escape_string($firstname);
$lastname = $conn->real_escape_string($lastname);
$protitle = $conn->real_escape_string($protitle);
$desc = $conn->real_escape_string($desc);
$supervisorname = $conn->real_escape_string($supervisorname);
$supphonenum = $conn->real_escape_string($supphonenum);
$contactEmail = $conn->real_escape_string($contactEmail);
$supemail = $conn->real_escape_string($supemail);
$hours = $conn->real_escape_string($hours);


// Build the INSERT statement
$sql = <<<SQL
INSERT INTO submissions (sub_user_id, sub_date, sub_first_name, sub_last_name,
sub_service_title, sub_service_description, sub_grade_level, sub_hours, sub_supervisor_name,
sub_supervisor_phone_number, sub_submittee_email, sub_supervisor_email)
       VALUES ({$_SESSION['userId']}, '{$date}', '{$firstname}', '{$lastname}', '{$protitle}', '{$desc}', '{$grade}', $hours, '{$supervisorname}', 
       '{$supphonenum}', '{$contactEmail}', '{$supemail}')
SQL;

// Execute the query and redirect to the list
if ($conn->query($sql) == TRUE)
{
    header('Location: index.php?content=list');
}
else
{
    echo "Error inserting record: " . $conn->error;
}

$conn->close();