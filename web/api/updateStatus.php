<?php
require_once '../library.php';

$sql = <<<SQL
    update submissions
    set sub_status=$status
    where sub_id=$id
SQL;

$result = mysqli_query($dbh, $sql);