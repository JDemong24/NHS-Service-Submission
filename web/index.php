<?php

require_once 'library.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Thu, 14 Mar 1996 00:00:00 GMT");

?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-115397967-1"></script>
        
        <script>

        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'UA-115397967-1');

        </script>

        <!-- Google SSO -->
        <script src="https://apis.google.com/js/platform.js" async defer></script>
        <meta name="google-signin-client_id" content="<?php echo $config['google']['client_id']; ?>">

        <title>NHS Service Submission</title>

        <meta http-equiv="Content-Type"    content="text/html; charset=ISO-8859-1" />
        <meta http-equiv="keywords"        content="pidaychallenge">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="author"      content="Mr. Pi">
        <meta name="description" content="pidaychallenge.com | Pi Day Challenge">

        <link href="css/bootstrap.min.css"                 rel="stylesheet" type="text/css" />
        <link href="css/font-awesome.css"                  rel="styleSheet" type="text/css" />
        <link href="css/piday.css?v=<?php echo rand(); ?>" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="styles.css?v=<?php echo rand(); ?>">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Candal&family=Cute+Font&family=Noto+Serif+Lao&display=swap" rel="stylesheet">

        <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>

        <script>

        var debug = <?php get_debug_mode() ? print 'true' : print 'false' ?>;
        function log(message) {
            if (debug) {
                console.log(message);
            }
        }

        function googleSignOut() {
            gapi.load('auth2', function() {
                log('signout 1');
                gapi.auth2.init().then(function() {
                    log('signout 2');
                    gapi.auth2.getAuthInstance().signOut();
                    // gapi.auth2.getAuthInstance().disconnect().then(function () {
                        log('User signed out.');
                        window.location.replace('logout.php');
                    // });
                });
            });
        }

        function showAlert(type, title, message) {
            $('#alert').hide();
            $('#alert').removeClass('alert-success alert-info alert-warning alert-danger').addClass('alert-' + type);
            $('#alertTitle').text(title);
            $('#alertMessage').html(message);
            $('#alert').fadeIn();
        }

        </script>
    </head>

    <body>
        <!-- Navigation -->
        <nav class="navbar navbar-dark">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                </div>
                <div class="collapse navbar-collapse" id="navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']) { ?>
                            <li><a href="index.php?content=form">Service Form</a></li>
                            <li><a href="index.php?content=list">Your Submissions</a></li>
                        <?php } ?>
                        <?php if (isset($_SESSION['authenticated']) && $_SESSION['isAdmin'] && $_SESSION['authenticated']) { ?>
                            <li><a href="index.php?content=adminList">Admin</a></li>                        <?php } ?>
                        <li><a href="index.php?content=contact">Contact</a></li>
                        <?php if (!isset($_SESSION['authenticated']) || !$_SESSION['authenticated']) { ?>
                            <li><a href="index.php?content=login">Log In</a></li>
                        <?php } else { ?>
                            <li><a href="index.php?content=settings">Settings</a></li>
                            <?php if ($_SESSION['ssoProvider'] == "google") { ?>
                                <li><a href="#" onclick="googleSignOut()">Log Out</a></li>
                            <?php } else { ?>
                                <li><a href="logout.php">Log Out</a></li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Generic alert -->
        <div id="alert" class="alert alert-position alert-success">
            <a class="close" onclick="$('#alert').fadeOut()"><span aria-hidden="true">&times;</span></a>
            <strong id="alertTitle">Success!</strong> <span id="alertMessage">Success message.</span>
        </div>

        <!-- <div><div class="g-signin2" data-width="400" data-longtitle="true" data-onsuccess="loginGoogleSso"></div> -->

        <!-- Main content -->
        <div class="container">
            <?php 
            include($content . '.php'); ?>
        </div>
    </body>
</html>
