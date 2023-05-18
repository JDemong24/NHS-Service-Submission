<link rel="stylesheet" href="styles.css">
<script>
    function save(id, status){
        // WORK ON THIS NEXT!!
        var settings = {
            'async': true,
            'url': 'api/updateStatus.php?id=' + id + '&status=' + status,
            'method': 'POST',
            'headers': {
                'Cache-Control': 'no-cache'
            }
        };


        $.ajax(settings).done(function(response) {
            showAlert('success', 'Status Updated', 'The submission status has been updated  ');
        }).fail(function(jqXHR) {   
            showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
        });
    }
</script>

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
        echo "<td>" . $row['sub_id'] . "</td>";
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
            echo '<select class="form-select" name="status" onchange="save(' . $row["sub_id"] .', this.value)">';
            echo '<option value="1" selected=true>Pending</option>';
            echo '<option value="2">Approved</option>';
            echo '<option value="3">Rejected</option>';
            echo '</select>';
            echo '</div>';
            echo '</td>';
        }else{
            if ($row['sub_status']==1){
                echo "<td class='pending'>Pending</td>";
            }else if ($row['sub_status']==2){
                echo "<td class='approved'>Approved</td>";
            }else if ($row['sub_status']==3){
                echo "<td class='rejected'>Rejected</td>";
            } 
        }   
        echo "</tr>";
    }

    ?>
    </tbody>
</table>

<a href="index.php?content=menu" class="btn btn-primary" role="button"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;Add a Submission</a>
