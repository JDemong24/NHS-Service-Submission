<?php

/*************************************************************************************************
 * logout.php
 *
 * Copyright 2017-2018
 *
 * Ends the user's session and redirects to the login page.
 *************************************************************************************************/

session_start();
session_unset();
session_destroy();
session_write_close();

header('Location: index.php');