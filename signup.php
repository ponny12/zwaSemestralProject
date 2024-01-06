<?php
require_once 'config/config.php';
require 'basedir/basedir.php';
global $base_dir;





$login = true;
include "header.php"
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo $base_dir?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate - signup</title>
    <script defer src="js/signupScript.js"></script>
</head>

<body>

    <content class="registration_page">
        <h1 id="love">Registration</h1>
        <form action="includes/signup_handler.inc.php" id="signupForm" method="post" class="registration_form" novalidate>
            <label for="username">Nickname: <input type="text" name="username" id="username"></label>
            <div id="usernameError" class="error"></div>
            <label for="email">E-mail: <input type="email" name="email" id="email"></label>
            <div id="emailError" class="error"></div>
            <label for="password1">Password: <input type="password" name="password1" id="password1" value=""></label>
            <div id="weakPassError" class="error"></div>
            <label for="password2">Password again: <input type="password" name="password2" id="password2" value=""></label>
            <div id="matchPassError" class="error"></div>
            <label for="confirmLicense">Confirm with<a href="license.php" target="_blank"
                    class="license_link">license:</a><input type="checkbox" name="confirmLicense"
                    id="confirmLicense"></label>
            <div id="confirmError" class="error"></div>

            <input type="submit" name="submit" id="submit" class="submit" value="Let's go">

        </form>
        <br>
        <div class="reminder">Already have an account? <a href="login.php">Just log in!</a></div>
    </content>

</body>

</html>

<?php

include "footer.php";