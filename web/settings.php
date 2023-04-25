<?php

/*************************************************************************************************
 * settings.php
 *
 * Copyright 2017-2021
 *
 * Settings page content. This page is intended to be included in index.php.
 *************************************************************************************************/

$userId = $_SESSION['userId'];

$sql = <<<SQL
 SELECT email, verified
   FROM user
  WHERE id = {$userId}
SQL;

$result = mysqli_query($dbh, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$email = $row['email'];
$verified = ($row['verified'] == 1);

?>

<div class="col-md-8 col-md-offset-2">
    <h2 class="text-center">Settings for <span id="displayNameHeader"><?php echo $_SESSION['displayName']; ?></span></h2>
</div>

<div class="jumbotron col-md-8 col-md-offset-2">
    <form class="form-horizontal" action="javascript:void(0);">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="challengeYear">Challenge Year:</label>
            <div class="col-sm-9">
                <select class="form-control" id="challengeYear" name="challengeYear">
                    <?php

                    // The first year we support for backward compatibility is hard-coded to 2017
                    for ($i = 2017; $i <= get_default_challenge_year(); $i++)
                    {
                        print '<option value="' . $i . '"' . ($_SESSION['year'] == $i ? 'selected="true"' : '') . '>' . $i . '</option>';
                    }

                    ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="email">Email Address:</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" id="email" name="email" placeholder="Email Address" value="<?php echo $email; ?>" <?php echo is_null($_SESSION['ssoProvider']) ? '' : 'disabled="true"'; ?> />
            </div>
            <div class="col-sm-3">
                <?php

                if ($verified)
                {
                    echo '<i class="fa fa-check-circle-o fa-2x" style="color: #248f24;" aria-hidden="true" title="Email has been verified"></i>';
                }
                else
                {
                    echo '<input type="button" id="sendVerificationEmailButton" class="btn btn-block" value="Verify Email" onclick="sendVerificationEmail()" />';
                }

                ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="displayName">Display Name:</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="displayName" name="displayName" placeholder="Display Name" value="<?php echo $_SESSION['displayName']; ?>" />
            </div>
        </div>
        <div class="container col-sm-6 col-sm-offset-3">
            <input type="button" id="saveSettingsButton" class="btn btn-primary btn-block" value="Save Settings" onclick="saveSettings()" />
        </div>
    </form>
</div>

<?php if (is_null($_SESSION['ssoProvider'])) { ?>

<div class="jumbotron col-md-8 col-md-offset-2">
    <form class="form-horizontal" action="javascript:void(0);">
        <div class="form-group">
            <label class="col-sm-3 control-label" for="newPassword">New Password:</label>
            <div class="col-sm-9">
                <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="New Password" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label" for="confirmPassword">Confirm Password:</label>
            <div class="col-sm-9">
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" />
            </div>
        </div>
        <div class="container col-sm-6 col-sm-offset-3">
            <input type="button" id="savePasswordButton" class="btn btn-primary btn-block" value="Change Password" onclick="$('#securityCheckModal').modal('show')" />
        </div>
    </form>
</div>

<?php } ?>

<div class="modal fade" id="securityCheckModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Security Check</h4>
      </div>
      <div class="modal-body">
        Confirm your current password: <input type="password" class="form-control" id="securityCheck" name="securityCheck" placeholder="" />
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="savePassword()">Continue</button>
      </div>
    </div>
  </div>
</div>

<script>

function savePassword() {
    $('#securityCheckModal').modal('hide');

    if ($('#newPassword').val() == '') {
        showAlert('danger', 'Missing Value!', 'Your new password can\'t be blank. Enter a password and try again.');
    } else if ($('#newPassword').val() != $('#confirmPassword').val()) {
        showAlert('danger', 'Password Mismatch!', 'Your passwords don\'t match, try again.');
    } else {
        var settings = {
            'async': true,
            'url': 'api/updateAccount.php?securityCheck=' + $('#securityCheck').val() + '&password=' + $('#newPassword').val(),
            'method': 'POST',
            'headers': {
                'Cache-Control': 'no-cache'
            }
        };

        $('#savePasswordButton').prop('disabled', true);

        $.ajax(settings).done(function(response) {
            showAlert('success', 'Settings Saved!', 'Your password has been updated.');
        }).fail(function(jqXHR) {
            if (jqXHR.status == 401) {
                showAlert('danger', 'Invalid Password!', 'You did not enter your current password correctly. Please try again.');
            } else {
                showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
            }
        }).always(function() {
            $('#savePasswordButton').prop('disabled', false);
        });
    }

    $('#securityCheck').val('');
}

function saveSettings() {
    if ($('#displayName').val() == '') {
        showAlert('danger', 'Display Name Required!', 'Your display name can\'t be blank. Enter a value and try again.');
    } else {
        var settings = {
            'async': true,
            'url': 'api/updateAccount.php?email=' + $('#email').val() + '&displayName=' + $('#displayName').val() + '&challengeYear=' + $('#challengeYear').val(),
            'method': 'POST',
            'headers': {
                'Cache-Control': 'no-cache'
            }
        };

        $('#saveSettingsButton').prop('disabled', true);

        $.ajax(settings).done(function(response) {
            showAlert('success', 'Settings Saved!', 'Your account has been updated.');
            $('#displayNameHeader').text($('#displayName').val());
        }).fail(function(jqXHR) {
            if (jqXHR.status == 409) {
                    showAlert('danger', 'Email Taken!', 'That email address is assigned to another user. Enter a different address.');
            } else {
                showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
            }
        }).always(function() {
            $('#saveSettingsButton').prop('disabled', false);
        });
    }
}

function sendVerificationEmail() {
    var settings = {
        'async': true,
        'url': 'api/sendVerificationEmail.php',
        'method': 'POST',
        'headers': {
            'Cache-Control': 'no-cache'
        }
    };

    $.ajax(settings).done(function(response) {
        showAlert('success', 'Verification Sent!', 'Check your email for a message from Pi Day Challenge and follow the instructions.');
    }).fail(function() {
        showAlert('danger', 'Oops, Error!', 'Something went wrong, try again later.');
    });
}

</script>