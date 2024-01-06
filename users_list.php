<?php
require_once 'config/config.php';
require 'basedir/basedir.php';
global $base_dir;
require 'tools/dbh.tool.php';
require 'tools/error.tool.php';
global $pdo;

if (!isset($_SESSION) || !$_SESSION['isAdmin']) {
    header('Location: index.php?message=you are not the admin');
    die();
}

try {
    $stmt = $pdo->prepare('SELECT * FROM users');
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e);
}



?>

<!DOCTYPE html>

<html lang="en">

<head>
    <base href="<?php echo $base_dir?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
    <script defer src="js/profileEditScript.js"></script>
</head>


<body>
    <?php include "header.php" ?>

    <div class="userslist_container">
        <?php foreach ($users as $user) { ?>
            <div class="user">
                <a href="profile.php?id=<?php echo $user['id']?>" class="info">
                    <img src="<?php echo htmlspecialchars($user['img_small'])?>" alt="user photo">
                    <div class="name"><?php echo htmlspecialchars(($user['username']))?></div>
                </a>
                <a href="includes/pretend_to_be_user.inc.php?id=<?php echo $user['id'] ?>" class="orange_button">pretend</a>
            </div>
        <?php }?>
    </div>


    <?php include "footer.php";?>

</body>


</html>


