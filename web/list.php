<script>
    function save(id, status){

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
            <th>Supervisor Name</th>
            <th>Supervisor Phone Number</th>
            <th>Project Title</th>
            <th>Project Description</th>
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
    $sql = "SELECT * FROM submissions";
    if($_SESSION['isAdmin'] && isset($userId)){
        $sql .= " WHERE sub_user_id=". $userId;
    }else{
        $sql .= " WHERE sub_user_id=". $_SESSION['userId'];
    }
    // Execute the query and save the results
    $result = $conn->query($sql);

    // Iterate over each row in the results
    while ($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>" . $row['sub_id'] . "</td>";
        echo "<td>" . $row['sub_date'] . "</td>";
        echo "<td>" . $row['sub_supervisor_name'] . "</td>";
        echo "<td>" . $row['sub_supervisor_phone_number'] . "</td>";
        echo "<td>" . $row['sub_service_title'] . "</td>";
        echo "<td>" . $row['sub_service_description'] . "</td>";
        echo "<td><a href='mailto:". $row['sub_supervisor_email'] . "'>" . $row['sub_supervisor_email'] . "</a></td>";
        echo "<td>" . $row['sub_hours'] . "</td>";
        echo "<td>" . $row['sub_grade_level'] . "</td>";
        if($_SESSION['isAdmin'] && isset($userId)){
            echo '<td>';
            echo '<div class="mb-3">';
            echo '<select class="form-select" name="status" onchange="save(' . $row["sub_id"] .', this.value)">';
            echo '<option value="1" ' . ($row['sub_status']==1 ? 'selected' : '') . '>Pending</option>';
            echo '<option value="2" ' . ($row['sub_status']==2 ? 'selected' : '') . '>Approved</option>';
            echo '<option value="3" ' . ($row['sub_status']==3 ? 'selected' : '') . '>Rejected</option>';
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
        if($_SESSION['isAdmin'] && isset($userId) && $row['sub_status']==2 || $row['sub_status']==3){
            
        }
        echo "</tr>";
    }
    ?>
    </tbody>
</table>

<?php
    if(!(isset($userId))){
        $result = $conn->query("SELECT SUM(sub_hours) as total_hours FROM submissions JOIN user on id=sub_user_id WHERE sub_status=2 AND sub_user_id=" . $_SESSION['userId']);
        $row = $result->fetch_assoc();
        echo '<p>Total hours: ' . $row['total_hours'] . '</p>';
    }
?>

<a href="index.php?content=form" class="btn btn-primary" role="button"><i class="fa fa-plus-circle" aria-hidden="true"></i>&nbsp;Add a Submission</a>
