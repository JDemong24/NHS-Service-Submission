# Pi Day Challenge

<!-- TODO: Add description of application -->

# Setting your dev environment

## Install php
Install php
`brew install php@7.4`

Make sure the correct version of php is linked

If not, run `brew unlink php` and then `brew link php@7.4`

Check that it is installed correctly by running:
`php -v`

It should return something like:
`PHP 7.4.33 (cli)`

## Install MySQL
Install MySQL from `https://dev.mysql.com/downloads/installer/`

Set up your root user with the password `password`. You can always update the users of your mySQL instance later on using mySQL workbench.

Then install MySQL Workbench from `https://dev.mysql.com/downloads/workbench/`

Open MySQL workbench and run the lines from `../db/schema.sql` in a query in mySQL workbench.

## Check config.ini
You may have to update the `script_root` in order to run the app correctly. 

# Running pidaychallenge locally

## Start your database
Make sure your mySQL database instance is running

## Start php
`php -S localhost:8000` in the root of this project

## Navigate to your localhost
`localhost:8000/index.php`
