<?php
if(!isset($_SESSION))
{
    session_start();
}
require 'includes/dbh.inc.php';
global $pdo;
if (empty($_SESSION['loginID'])) {
    $_SESSION['errorType'] = 'You are not allowed to visit a profile page without a registration.';
    header('Location: error.php');
    die();
}


# get - display
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $_SESSION['errorType'] = 'Profile id was not selected.';
    header('Location: error.php');
    die();
}


if (isset($_GET['edit']) && $_GET['edit'] == 'true' && $id == $_SESSION['loginID']) {
    $edit = true;
} else {
    $edit = false;
}

try {
    $stmt = $pdo->prepare('SELECT *, admins.user_id as admin
                                FROM users
                                LEFT JOIN admins ON users.id = admins.user_id
                                WHERE users.id = :id');
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
} catch (Exception $e) {
    $_SESSION['errorType'] = 'db failed: '.$e.' while searching for user with id: '.$_GET['id'];
    header('Location: error.php');
    die();
}

if (empty($user)) {
    $_SESSION['errorType'] = 'user with id: '.$_GET['id'].' does not exist.';
    header('Location: error.php');
    die();
}


if (isset($user['admin']) && $user['admin'] != null) {
    $isAdmin = true;
} else {
    $isAdmin = false;
}
?>
<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <title>VRMate</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
</head>


<body>
    <?php include "header.php" ?>


    <?php
    # edit view
    if ($edit) { ?>
        <form class="profile_container" method="POST" action="includes/profile_edit_handler.inc.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $user['id'] ?>">
            <div class="image_and_edit">
                <img src="<?php echo htmlspecialchars($user['img_big'])?>" alt="user photo" class="user_photo">
                <input type="file" name="image" class="image_edit" id="image_edit" accept="image/webp, image/jpeg, image/png">
            </div>
            <div class="user_personal_data">
                <div class="username_and_edit">
                    <input type="text" name="name" class="username_edit" value="<?php echo htmlspecialchars($user['username']) ?>">
                    <input type="submit" name="submit" class="orange_button save" value="save">
                </div>
                <div class="info_title">info:</div>
                <textarea name="info" class="info_text_edit"><?php echo htmlspecialchars($user['info'])?></textarea>
            </div>
        </form>
    <?php } else {
        # display view
        ?>

    <content class="profile_container">
        <img src="<?php echo htmlspecialchars($user['img_big'])?>" alt="user photo" class="user_photo">
        <div class="user_personal_data">
            <div class="username_and_edit">
                <div class="username"><?php echo htmlspecialchars($user['username']) ?></div>
                <?php
                if ($user['id'] == $_SESSION['loginID']) { ?>
                    <a href="?id=<?php echo $user['id']?>&edit=<?php echo 'true'?>" class="orange_button">edit</a>
                <?php }
                ?>
            </div>
            <div class="info_title">info:</div>
            <div class="info_text"><?php echo htmlspecialchars($user['info'])?></div>
        </div>
    </content>

    <?php } 
    
    if ($id == $_SESSION['loginID'] && !$edit) { ?>
        <form action="includes/profile_delete_handler.inc.php" class="delete_user_form" method="POST">
            <label for="password"></label>
            <div class="delete">
                <input type="checkbox" name="delete_checkbox" id="delete_checkbox"> <label for="delete_checkbox" class="checkbox_label">I want to delete my account and all events I've created without the slightest possibility of recovery, I accept all the risks associated with deleting my account, understanding that for the rest of my life I will miss without a company for VR games</label>
                <input type="password" name="password" id="password" class="password" placeholder="enter your password">
                <input type="submit" name="submit" value="delete" class="delete_button">
            </div>
        </form>
    
    <?php } ?>

    <?php include "footer.php";?>

</body>


</html>

