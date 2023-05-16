<link rel="stylesheet" href="styles.css">
<h2>Submit a Service Request</h2>

<form action="insert.php" method="POST">
    <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" class="form-control" name="date">
    </div>
    <br>
    <div class="mb-3">
        <label for="firstname" class="form-label">First Name</label>
        <input type="text" class="form-control" name="firstname">
    </div>
    <br>
    <div class="mb-3">
        <label for="lastname" class="form-label">Last Name</label>
        <input type="text" class="form-control" name="lastname">
    </div>
    <br>
    <div class="mb-3">
        <label for="protitle" class="form-label">Title</label>
        <input type="text" class="form-control" name="protitle">
    </div>
    <br>
    <div class="mb-3">
        <label for="desc" class="form-label">Description</label>
        <input type="text" class="form-control" name="desc">
    </div>
    <br>
    <div class="mb-3">
        <label for="grade" class="form-label">Grade Level</label>
        <select class="form-select" name="grade">
            <option value="11">Junior</option>
            <option value="12">Senior</option>
        </select>
    </div>
    <br>
    <div class="mb-3">
        <label for="hours" class="form-label">Hours Completed</label>
        <input type="text" class="form-control" name="hours">
    </div>
    <br>
    <div class="mb-3">
        <label for="supervisorname" class="form-label">Supervisor Name</label>
        <input type="text" class="form-control" name="supervisorname">
    </div>
    <br>
    <div class="mb-3">
        <label for="supphonenum" class="form-label">Supervisor Phone Number</label>
        <input type="text" class="form-control" name="supphonenum">
    </div>
    <br>
    <div class="mb-3">
        <label for="contactEmail" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="contactEmail">
    </div>
    <br>
    <div class="mb-3">
        <label for="supemail" class="form-label">Supervisor Email Address</label>
        <input type="email" class="form-control" name="supemail">
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>