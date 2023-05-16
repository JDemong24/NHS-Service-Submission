<link rel="stylesheet" href="styles.css">
<?php

/*************************************************************************************************
 * ticketList.php
 *
 * Content page to display a list of tickets. This page is expected to be contained within
 * index.php.
 *************************************************************************************************/

?>

<h2>Submissions</h2>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Supervisor Name</th>
            <th>Supervisor Phone Number</th>
            <th>Project Title</th>
            <th>Project Description</th>
            <th>Email</th>
            <th>Supervisor Email</th>
            <th>Hours Completed</th>
            <th>Grade Level</th>
            <th>Status</th>

            
        </tr>
    </thead>
    <tbody>

    <?php

    $conn = get_database_connection();

    // Build the SELECT statement
    if($_SESSION['isAdmin']){
        $sql = "SELECT * FROM submissions";
    }else{
        $sql = "SELECT * FROM submissions WHERE sub_user_id=" . $_SESSION['userId'];
    }
    // Execute the query and save the results
    $result = $conn->query($sql);

    // Iterate over each row in the results
    while ($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>" . $row['sub_user_id'] . "</td>";
        echo "<td>" . $row['sub_date'] . "</td>";
        echo "<td>" . $row['sub_first_name'] . "</td>";
        echo "<td>" . $row['sub_last_name'] . "</td>";
        echo "<td>" . $row['sub_supervisor_name'] . "</td>";
        echo "<td>" . $row['sub_supervisor_phone_number'] . "</td>";
        echo "<td>" . $row['sub_service_title'] . "</td>";
        echo "<td>" . $row['sub_service_description'] . "</td>";
        echo "<td>" . $row['sub_submittee_email'] . "</td>";
        echo "<td><a href='mailto:". $row['sub_supervisor_email'] . "'>" . $row['sub_supervisor_email'] . "</a></td>";
        echo "<td>" . $row['sub_hours'] . "</td>";
        echo "<td>" . $row['sub_grade_level'] . "</td>";
        if($_SESSION['isAdmin']){
            echo '<td>';
            echo '<div class="mb-3">';
            echo '<label for="status" class="form-label"></label>';
            echo '<select class="form-select" name="status">';
            echo '<option value="1">Pending</option>';
            echo '<option value="2">Approved</option>';
            echo '<option value="3">Rejected</option>';
            echo '</select>';
            echo '</div>';
            echo '</td>';
        }else{
            echo "<td>" . $row['sub_status'] . "</td>";
        }   
        echo "</tr>";
    }

    ?>
    <form action="list.php" method="POST"><input type="submit"></form>
    </tbody>
</table>

<a href="index.php?content=menu" class="btn btn-primary" role="button"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;Add a ticket</a>
