<?php

include('../library.php');
extract($_REQUEST);
$conn = get_database_connection();

// Sanitize all input values to prevent SQL injection exploits
$date = $conn->real_escape_string($date);
$protitle = $conn->real_escape_string($protitle);
$desc = $conn->real_escape_string($desc);
$supervisorname = $conn->real_escape_string($supervisorname);
$supphonenum = $conn->real_escape_string($supphonenum);
$supemail = $conn->real_escape_string($supemail);
$hours = $conn->real_escape_string($hours);


// Build the INSERT statement
$sql = <<<SQL
INSERT INTO submissions (sub_user_id, sub_date,
sub_service_title, sub_service_description, sub_grade_level, sub_hours, sub_supervisor_name,
sub_supervisor_phone_number, sub_supervisor_email)
       VALUES ({$_SESSION['userId']}, '{$date}', '{$protitle}', '{$desc}', '{$grade}', $hours, '{$supervisorname}', 
       '{$supphonenum}', '{$supemail}')
SQL;

echo $sql;
// Execute the query and redirect to the list
if ($conn->query($sql) == TRUE)
{
    http_response_code(200);
}
else
{
    http_response_code(500);
}

$conn->close();