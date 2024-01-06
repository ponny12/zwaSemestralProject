<?php
require_once 'config/config.php';
require 'basedir/basedir.php';
global $base_dir;
?>

<!doctype html>
<html lang="en">
<head>
    <base href="<?php echo $base_dir?>">
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">

    <title>Document</title>
</head>
<body>
    <?php include 'header.php' ?>

    <div class="error_page_content">
        <h1 class="title">SOMETHING WENT WRONG BECAUSE...</h1>
        <p class="description"><?php
            if (isset($_SESSION["errorType"])) {
                echo htmlspecialchars($_SESSION["errorType"]);
            } else
                echo "error type wasn't set before throwing this error.";
            unset($_SESSION['errorType']);
            ?></p>
        <a href="index.php" class="go_to_home_btn">GO TO HOME PAGE</a>

    </div>

    <?php include 'footer.php' ?>
</body>
</html>