<?php
if(!isset($_SESSION)) session_start();
require '../tools/error.tool.php';

require 'dbh.inc.php';
global $pdo;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php');
    die();
}

if (!isset($_POST['submit'])) {
    raiseError('try to edit profile without submitting.', '../');
}
# check if user id is correct
$user_id = intval($_POST['id']);
if ($user_id != $_SESSION['loginID']) {
    raiseError('attempt to change other users data.', '../');
}
# check if name is correct
$new_name = $_POST['name'];
if ($new_name == '') {
    raiseError('attempt to change profile data, but new username is empty string.', '../');
}
if ($new_name == 'default') {
    raiseError('attempt to change profile data, but new username is special word "default".', '../');
}
# check if info is correct
$new_info = $_POST['info'];
if ($new_info == '') {
    raiseError('attempt to change profile data, but new info is empty string.', '../');
}

# get all old data about user
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE users.id = :user_id');
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user_old = $stmt->fetch();
} catch (Exception $e) {
    raiseError('db failed: '.$e.' on user with id: '.$user_id, '../');
}
# check if new username already exists in db
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE LOWER(users.username) = LOWER(:name)');
    $stmt->bindParam(':name', $new_name);
    $stmt->execute();
    $result = $stmt->fetch();
    if (!empty($result) && $result['username'] != $user_old['username']) {
        raiseError('user with name: '.$new_name.' already exists', '../');
    }
} catch (Exception $e) {
    raiseError('db failed: '.$e.' while processing user with id: '.$user_id, '../');
}


# insert new name and info
try {
    $stmt = $pdo->prepare('UPDATE users SET username = :new_name, info = :new_info WHERE users.id = :user_id');
    $stmt->bindParam(':new_name', $new_name);
    $stmt->bindParam(':new_info', $new_info);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e.' while processing user with id: '.$user_id, '../');
}

# check if image presents and insert if it is

if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
    #check if file received
    $file = $_FILES['image'];
    if (empty($file['name'])) {
        raiseError('attempt to add game, but no file for image was selected.', '../');
    }
#check type of file
    if (!preg_match('#image/*#', $file['type'])) {
        raiseError('attempt to add game, but attached file is not image.', '../');
    }
#resize the image
    $file_name = $new_name;
    $file_name = str_replace(" ", "_", $file_name);
    $file_name = str_replace("/", "_", $file_name);
    $file_name = str_replace("\\", "_", $file_name);
    $file_name = str_replace(":", "_", $file_name);
    $file_name = str_replace("*", "_", $file_name);
    $file_name = str_replace("?", "_", $file_name);
    $file_name = str_replace("\"", "_", $file_name);
    $file_name = str_replace("<", "_", $file_name);
    $file_name = str_replace(">", "_", $file_name);
    $file_name = str_replace("|", "_", $file_name);
    $file_name = str_replace(".", "_", $file_name);

    $type = array_slice(explode('.', $file['name']), -1, 1)[0];
    $new_path_big = '../images/profiles_images/'.$file_name.'_big.'.$type;
    $new_path_small = '../images/profiles_images/'.$file_name.'_small.'.$type;
# for db without ../
    $new_path_big_for_db = 'images/profiles_images/'.$file_name.'_big.'.$type;
    $new_path_small_for_db = 'images/profiles_images/'.$file_name.'_small.'.$type;
    move_uploaded_file($file['tmp_name'], $new_path_big);
    copy($new_path_big, $new_path_small);
    require '../tools/image.tool.php';
    resize_image($new_path_big, 150);
    resize_image($new_path_small, 50);
# updating img paths
    try {
        $stmt = $pdo->prepare('UPDATE users SET img_small = :image_small, img_big = :image_big WHERE users.id = :user_id');
        $stmt->bindParam(':image_small', $new_path_small_for_db, PDO::PARAM_STR);
        $stmt->bindParam(':image_big', $new_path_big_for_db, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $result = $stmt->execute();
    } catch (Exception $e) {
        raiseError('db failed: '.$e, '../');
    }
    # delete old images using old paths if needed
    if ($user_old['img_small'] != 'images/profiles_images/default_small.jpg' && $user_old['username'] != $new_name) {
        unlink('../'.$user_old['img_small']);
        unlink('../'.$user_old['img_big']);
    }
}
$stmt = null;
$pdo = null;

header('Location: ../profile.php?id='.$user_id);


