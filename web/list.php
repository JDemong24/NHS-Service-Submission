<?php

/*************************************************************************************************
 * ticketList.php
 *
 * Content page to display a list of tickets. This page is expected to be contained within
 * index.php.
 *************************************************************************************************/

?>

<h2>All Tickets</h2>

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

            
        </tr>
    </thead>
    <tbody>

    <?php

    $conn = get_database_connection();

    // Build the SELECT statement
    $sql = "SELECT * FROM submissions WHERE sub_user_id=" . $_SESSION['userId'];

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
        echo "</tr>";
    }

    ?>

    </tbody>
</table>

<a href="index.php?content=menu" class="btn btn-primary" role="button"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;Add a ticket</a>
