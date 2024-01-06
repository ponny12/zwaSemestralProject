<?php
require_once '../config/config.php';
require '../tools/error.tool.php';
require '../tools/dbh.tool.php';
global $pdo;

# check if connecting via post
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $_SESSION['errorType'] = 'attempt to add game, but not by post request.';
    header('Location: ../error.php');
    die();
}
# check if post data received
if (!isset($_POST['submit'])) {
    $_SESSION['errorType'] = 'attempt to add game, but no post data found.';
    header('Location: ../error.php');
    die();
}
# check creator
$suggested_by = $_POST['suggested_by'];
# check if name is empty
$name = trim($_POST['name']);
if (empty($name)) {
    $_SESSION['errorType'] = 'attempt to add game, but name can not be empty.';
    header('Location: ../error.php');
    die();
}
# check if game with the same name already exist
try {
    $stmt = $pdo->prepare('SELECT name FROM games WHERE LOWER(games.name) = :name');
    $stmt->bindParam(':name', $name);
    $stmt->execute();
    $result = $stmt->fetch();
    if (!empty($result)) {
        $_SESSION['errorType'] = 'attempt to add game that already exist(same name).';
        header('Location: ../error.php');
        die();
    }
} catch (PDOException $e) {
    $_SESSION['errorType'] = 'db connection failed: '.$e;
    header('Location: ../error.php');
    die();
} finally {
    $stmt = null;
    $result = null;
}
# check if description is empty
$description = trim($_POST['description']);
if (empty($description)) {
    $_SESSION['errorType'] = 'attempt to add game, but description can not be empty.';
    header('Location: ../error.php');
    die();
}

# check checkboxes(at least 1 should be selected)
if (!isset($_POST['meta']) && !isset($_POST['pico']) && !isset($_POST['pcvr'])) {
    $_SESSION['errorType'] = 'attempt to add game, but no platform selected.';
    header('Location: ../error.php');
    die();
}
#set checkboxes
if (isset($_POST['meta'])) {
    $meta = 1;
} else {
    $meta = 0;
}
if (isset($_POST['pico'])) {
    $pico = 1;
} else {
    $pico = 0;
}
if (isset($_POST['pcvr'])) {
    $pcvr = 1;
} else {
    $pcvr = 0;
}
#-----------------------------------

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
$file_name = $name;
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
$new_path_big = '../images/games_images/'.$file_name.'_big.'.$type;
$new_path_small = '../images/games_images/'.$file_name.'_small.'.$type;
# for db without ../
$new_path_big_for_db = 'images/games_images/'.$file_name.'_big.'.$type;
$new_path_small_for_db = 'images/games_images/'.$file_name.'_small.'.$type;
move_uploaded_file($file['tmp_name'], $new_path_big);
copy($new_path_big, $new_path_small);
require '../tools/image.tool.php';
resize_image($new_path_big, 150);
resize_image($new_path_small, 50);

#insert suggestion
try {
    $stmt = $pdo->prepare('INSERT INTO suggestions (name, description, img_small, img_big, meta, pico, pcvr, suggested_by) VALUES (:name, :description, :image_small, :image_big, :meta, :pico, :pcvr, :suggested_by)');
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':image_small', $new_path_small_for_db, PDO::PARAM_STR);
    $stmt->bindParam(':image_big', $new_path_big_for_db, PDO::PARAM_STR);
    $stmt->bindParam(':meta', $meta, PDO::PARAM_INT);
    $stmt->bindParam(':pico', $pico, PDO::PARAM_INT);
    $stmt->bindParam(':pcvr', $pcvr, PDO::PARAM_INT);
    $stmt->bindParam(':suggested_by', $suggested_by, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    raiseError('db failed: '.$e, '../');
} finally {
    $stmt = null;
    $result = null;
}

header("Location: ../index.php?message=the admin will carefully consider your proposal");
die();

