<link rel="stylesheet" href="styles.css">

<div class="jumbotron col-md-8 col-md-offset-2">
    <h2>Reset Password</h2>

    <p>Please enter the email for your account. We will reset your password and send it to you via email. Please be sure to check your spam folder.</p>

    <form class="form-horizontal" action="javascript:void(0);">
        <div class="col-xs-12" style="height:20px;"></div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="email">Email:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="email" name="email" placeholder="Email" autofocus />
            </div>
        </div>
        <div class="container col-md-6 col-md-offset-3">
            <input type="button" id="resetPasswordButton" class="btn btn-primary btn-block" value="Reset Password" onclick="resetPassword()" />
        </div>
        <div class="col-xs-12" style="height:30px;"></div>
        <div class="container col-md-6 col-md-offset-3">
            <a  href="index.php?content=login" role="button">Return to the login page</a>
        </div>
    </form>
</div>

<script>

function resetPassword() {
    if ($('#email').val() == '') {
        showAlert('danger', 'Email Required!', 'Enter your email address and try again.');
    } else {
        var settings = {
            'async': true,
            'url': 'api/resetPassword.php?email=' + $('#email').val(),
            'method': 'POST',
            'headers': {
                'Cache-Control': 'no-cache'
            }
        };

        $('#resetPasswordButton').prop('disabled', true);

        $.ajax(settings).done(function(response) {
            showAlert('success', 'Password Reset!', 'We sent you an email with the new password.');
        }).fail(function(jqXHR) {
            if (jqXHR.status == 400) {
                showAlert('danger', 'Invalid Email!', 'We couldn\'t find an account with that email address.');
            } else if (jqXHR.status == 409) {
                showAlert('warning', 'Sign In With Google!', 'This email address uses Google sign-in. Click the "Sign in with Google" button on the <a href="index.php?content=login">login page</a>.');
            } else {
                showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
            }
        }).always(function() {
            $('#resetPasswordButton').prop('disabled', false);
        });
    }
}

</script>