<?php

/*************************************************************************************************
 * permitDetail.php
 *
 * Content page to display the detail form for a single application. This page is expected to be
 * contained within index.php.
 *************************************************************************************************/

$appId = '';
$date = '';
$firstName = '';
$lastName = '';
$phone = '';
$email = '';
$pkaIds = array();

$conn = get_database_connection();

if (isset($id))
{
    // Step 1: Load the customer and application records
    $sql = <<<SQL
    SELECT *
      FROM applications
      JOIN customers ON app_cus_id = cus_id
     WHERE app_id = $id
    SQL;

    $result = $conn->query($sql);

    $row = $result->fetch_assoc();

    $appId = $row['app_id'];
    $date = $row['app_date'];
    $cusId = $row['cus_id'];
    $firstName = $row['cus_first_name'];
    $lastName = $row['cus_last_name'];
    $phone = $row['cus_phone'];
    $email = $row['cus_email'];

    // Step 2: Load the children application_park_area records
    $sql = <<<SQL
    SELECT apa_pka_id
      FROM application_park_areas
     WHERE apa_app_id = $id
    SQL;

    $result = $conn->query($sql);

    $row = $result->fetch_assoc();
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc())
    {
        $pkaIds[] = $row['apa_pka_id'];
    }
}

?>

<script>
    function deleteApplication(id) {
        if (confirm('Are you sure you want to delete this application?')) {
            location.href = 'delete.php?id=' + id;
        }
    }
</script>

<form action="save.php" method="POST">
    <input type="hidden" name="appId" value="<?php echo $appId; ?>" />
    <input type="hidden" name="cusId" value="<?php echo $cusId; ?>" />
    <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" class="form-control" name="date" value="<?php echo $date; ?>">
    </div>
    <div class="mb-3">
        <label for="firstName" class="form-label">First name</label>
        <input type="text" class="form-control" name="firstName" value="<?php echo $firstName; ?>">
    </div>
    <div class="mb-3">
        <label for="lastName" class="form-label">Last name</label>
        <input type="text" class="form-control" name="lastName" value="<?php echo $lastName; ?>">
    </div>
    <div class="mb-3">
        <label for="phone" class="form-label">Phone number</label>
        <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>">
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
    </div>
    <div class="mb-3">
        <label for="parkAreas[]" class="form-label">Fields (hold CTRL or CMD to select multiple)</label>
        <select name="parkAreas[]" class="form-select" multiple="true" size="10">
<?php

$sql = <<<SQL
SELECT pka_id, pka_name, par_name
  FROM park_areas
  JOIN parks ON pka_par_id = par_id
 ORDER BY par_name, pka_name, pka_id
SQL;

$result = $conn->query($sql);
while ($row = $result->fetch_assoc())
{
    $select = in_array($row['pka_id'], $pkaIds) ? ' selected="true" ' : '';
    echo '<option value="' . $row['pka_id'] . '"' . $select . '>' . $row['par_name'] . ' - ' . $row['pka_name'] . '</option>';
}

$conn->close();

?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
<?php

if (isset($id))
{
    echo '<a href="javascript:deleteApplication(' . $id . ')" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</a>';
}

?>
    <a href="index.php?content=list" class="btn btn-secondary" role="button">Cancel</a>
</form>