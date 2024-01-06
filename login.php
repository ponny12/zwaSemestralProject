<?php
require_once 'config/config.php';
require 'basedir/basedir.php';
global $base_dir;

include "header.php";

if (isset($_SESSION['lastNickname'])) {
    $lastNickname = $_SESSION['lastNickname'];
    unset($_SESSION['lastNickname']);
} else {
    $lastNickname = '';
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <base href="<?php echo $base_dir?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate - login</title>
</head>

<body>

    <content class="login_page">
        <h1>Log In</h1>
        <form action="includes/login_handler.inc.php" method="post" class="login_form" id="login_form">
            <label for="nickname">Nickname: <input type="text" name="nickname" id="nickname" value=<?php echo $lastNickname ?>></label>
            <label for="password">Password: <input type="password" name="password" id="password" value=""></label>
            <?php
                if (isset($_SESSION["loginError"]) && $_SESSION["loginError"]) { ?>
                    <div class="error" id="login_error">Nickname or password aren't correct.</div>
                    <?php
                    unset($_SESSION['loginError']);
                }
            ?>
            <input type="submit" name="submit" id="submit" value="Let's go">

        </form>
        <br>
        <div class="reminder">Don't have an account? <a href="signup.php">Create easily!</a></div>
    </content>

</body>

</html>

<?php

include "footer.php";