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

<table class="table table-hover" id="adminTable"  cellspacing="0" style="width:100%;margin-top:30px">
    <thead>
        <tr>
        
            <th>First Name</th>
            <th>Last Name</th>
            <th>Hours Completed</th>
            <th>Grade Level</th>

            
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
        echo "<td><a href='index.php?content=list'>" . $row['sub_first_name'] . "</a></td>";
        echo "<td><a href='index.php?content=list'>" . $row['sub_last_name'] . "</a></td>";
        echo "<td><a href='index.php?content=list'>" . $row['sub_hours'] . "</a></td>";
        echo "<td><a href='index.php?content=list'>" . $row['sub_grade_level'] . "</a></td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>