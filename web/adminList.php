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

<h2>User List</h2>

<table class="table table-hover" id="adminTable"  cellspacing="0" style="width:100%;margin-top:30px">
    <thead>
        <tr>
        
            <th>User</th>
            <th>Hours Completed</th>
            <th>Grade Level</th>

            
        </tr>
    </thead>
    <tbody>

    <?php

    $conn = get_database_connection();

    // Build the SELECT statement
    $sql =<<<SQL
    SELECT display_name, sum(sub_hours) AS hours, sub_grade_level, id
    FROM user 
    JOIN submissions
    ON id=sub_user_id
    JOIN config
    WHERE sub_date>=cfg_school_start_date
    GROUP BY display_name, sub_grade_level, id
    ORDER BY display_name
    SQL;

    // Execute the query and save the results
    $result = $conn->query($sql);

    // Iterate over each row in the results
    while ($row = $result->fetch_assoc())
    {
        echo "<tr>";
        echo "<td><a href='index.php?content=list&userId=" . $row['id'] . "'>" . $row['display_name'] . "</a></td>";
        echo "<td><a href='index.php?content=list&userId=" . $row['id'] . "'>" . $row['hours'] . "</a></td>";
        echo "<td><a href='index.php?content=list&userId=" . $row['id'] . "'>" . $row['sub_grade_level'] . "</a></td>";
        echo "</tr>";
    }
    ?>
    </tbody>
</table>