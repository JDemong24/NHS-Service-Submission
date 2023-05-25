<link rel="stylesheet" href="styles.css">
<h2>Submit a Service Request</h2>

<form action="javascript:void(0);" method="POST">
    <div class="mb-3">
        <label for="date" class="form-label">Date</label>
        <input type="date" class="form-control" name="date" id="date">
    </div>
    <br>
    <div class="mb-3">
        <label for="firstname" class="form-label">First Name</label>
        <input type="text" class="form-control" name="firstname" id="firstname">
    </div>
    <br>
    <div class="mb-3">
        <label for="lastname" class="form-label">Last Name</label>
        <input type="text" class="form-control" name="lastname" id="lastname">
    </div>
    <br>
    <div class="mb-3">
        <label for="protitle" class="form-label">Title</label>
        <input type="text" class="form-control" name="protitle" id="protitle">
    </div>
    <br>
    <div class="mb-3">
        <label for="desc" class="form-label">Description</label>
        <input type="text" class="form-control" name="desc" id="desc">
    </div>
    <br>
    <div class="mb-3">
        <label for="grade" class="form-label">Grade Level</label>
        <select class="form-select" name="grade" id="grade">
            <option value="11">Junior</option>
            <option value="12">Senior</option>
        </select>
    </div>
    <br>
    <div class="mb-3">
        <label for="hours" class="form-label">Hours Completed</label>
        <input type="text" class="form-control" name="hours" id="hours">
    </div>
    <br>
    <div class="mb-3">
        <label for="supervisorname" class="form-label">Supervisor Name</label>
        <input type="text" class="form-control" name="supervisorname" id="supervisorname">
    </div>
    <br>
    <div class="mb-3">
        <label for="supphonenum" class="form-label">Supervisor Phone Number</label>
        <input type="text" class="form-control" name="supphonenum" id="supphonenum">
    </div>
    <br>
    <div class="mb-3">
        <label for="contactEmail" class="form-label">Email Address</label>
        <input type="email" class="form-control" name="contactEmail" id="contactEmail">
    </div>
    <br>
    <div class="mb-3">
        <label for="supemail" class="form-label">Supervisor Email Address</label>
        <input type="email" class="form-control" name="supemail" id="supemail">
    </div>
    <br>
    <button type="submit" onclick='submitForm()' class="btn btn-primary">Save</button>
    <br>
    <br>
</form>


<script>

function submitForm() {
    if ($('#date').val() == '') {
        showAlert('danger', 'Date Required!', 'Enter the service date and try again!');
    } else if ($('#firstname').val() == '') {
        showAlert('danger', 'First Name Required!', 'Enter your first name and try again!');
    } else if ($('#lastname').val() == '') {
        showAlert('danger', 'Last Name Required!', 'Enter your last name and try again!');
    } else if ($('#protitle').val() == '') {
        showAlert('danger', 'Project Title Required!', 'Enter the project title and try again!');
    } else if ($('#desc').val() == '') {
        showAlert('danger', 'Description Required!', 'Enter the project description and try again!');
    } else if ($('#hours').val() == '') {
        showAlert('danger', 'Project Hours Required!', 'Enter the total project hours and try again!');
    } else if ($('#supervisorname').val() == '') {
        showAlert('danger', 'Supervisor Name Required!', 'Enter the project supervisor\'s name and try again!');
    } else if ($('#supphonenum').val() == '') {
        showAlert('danger', 'Supervisor Phone Number Required!', 'Enter the supervisor\'s phone number and try again!');
    } else if ($('#contactEmail').val() == '') {
        showAlert('danger', 'Contact Email Required!', 'Enter your email and try again!');
    } else if ($('#supemail').val() == '') {
        showAlert('danger', 'Supervisor Email Required!', 'Enter the supervisor\'s email and try again!');
    } else {
        var settings = {
            'async': true,
            'url': 'api/insert.php?date=' + $('#date').val() + 
            '&firstname=' + $('#firstname').val() + 
            '&lastname=' + $('#lastname').val() +
            '&protitle=' + $('#protitle').val() + 
            '&desc=' + $('#desc').val() +
            '&hours=' + $('#hours').val() +
            '&supervisorname=' + $('#supervisorname').val() +
            '&supphonenum=' + $('#supphonenum').val() +
            '&contactEmail=' + $('#contactEmail').val() +
            '&grade=' + $('#grade').val() +
            '&supemail=' + $('#supemail').val(),
            'method': 'POST',
            'headers': {
                'Cache-Control': 'no-cache'
            }
        };

        $('#registerButton').prop('disabled', true);

        $.ajax(settings).done(function(response) {
            showAlert('success', 'Service Request Submitted!', 'Thank you for submitting a service request, we will get to it shortly.');
        }).fail(function(jqXHR) {
            showAlert('danger', 'Unable to Submit', 'Error submitting the form');
        }).always(function() {
            $('#registerButton').prop('disabled', false);
        });
    }
}

</script>