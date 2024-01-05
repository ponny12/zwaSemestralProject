<?php
if(!isset($_SESSION)) session_start();
require '../tools/error.tool.php';
require '../tools/dbh.tool.php';
global $pdo;

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../index.php');
    die();
}
if (!isset($_POST['submit'])) {
    raiseError('try to edit profile without submitting.', '../');
}
$game_id = $_POST['id'];
$new_name = trim($_POST['name']);
$new_description = trim($_POST['description']);
$new_meta = isset($_POST['meta']) ? 1 : 0;
$new_pico = isset($_POST['pico']) ? 1 : 0;
$new_pcvr = isset($_POST['pcvr']) ? 1 : 0;

# check if it is admin
if (!$_SESSION['isAdmin']) {
    raiseError('try to edit profile without admins rights.', '../');
}
# get all info about game from db
try {
    $stmt = $pdo->prepare('SELECT * FROM games WHERE games.id = :id');
    $stmt->bindParam(':id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
    $game = $stmt->fetch();
    if (empty($game)) {
        raiseError('game with id = '.$game_id.' not in db.', '../');
    }
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
$old_name = $game['name'];


# check new name
if ($new_name == '') {
    raiseError('game name = "".', '../');
}
if ($new_name != $old_name) {
    try {
        $stmt = $pdo->prepare('SELECT * FROM games WHERE LOWER(games.name) = LOWER(:new_name)');
        $stmt->bindParam(':new_name', $new_name);
        $stmt->execute();
        $result = $stmt->fetch();
        if (!empty($result)) {
            raiseError('game with name = '.$new_name.' already in db.', '../');
        }
    } catch (Exception $e) {
        raiseError('db failed: '.$e, '../');
    }
}
# check new description
if ($new_description == '') {
    raiseError('game description cannot be ""', '../');
}
# check new platforms
if (!$new_meta && !$new_pico && !$new_pcvr) {
    raiseError('at least one platform must be selected', '../');
}
# updating game info and platforms info in db
try {
    $stmt = $pdo->prepare('UPDATE games SET name = :new_name, description = :new_description WHERE games.id = :game_id');
    $stmt->bindParam(':new_name', $new_name);
    $stmt->bindParam(':new_description', $new_description);
    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e.' while processing user with id: '.$user_id, '../');
}
try {
    $stmt = $pdo->prepare('UPDATE games_platforms SET meta = :new_meta, pico = :new_pico, pcvr = :new_pcvr WHERE games_platforms.id = :game_id');
    $stmt->bindParam(':new_meta', $new_meta, PDO::PARAM_INT);
    $stmt->bindParam(':new_pico', $new_pico, PDO::PARAM_INT);
    $stmt->bindParam(':new_pcvr', $new_pcvr, PDO::PARAM_INT);
    $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e.' while processing user with id: '.$user_id, '../');
}
# check if image presents and insert if it is
if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
    #check if file received
    $file = $_FILES['image'];
    if (empty($file['name'])) {
        raiseError('attempt to update game, but no file for image was selected.', '../');
    }
#check type of file
    if (!preg_match('#image/*#', $file['type'])) {
        raiseError('attempt to update game, but attached file is not image.', '../');
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
# updating img paths
    try {
        $stmt = $pdo->prepare('UPDATE games SET img_small = :image_small, img_big = :image_big WHERE games.id = :game_id');
        $stmt->bindParam(':image_small', $new_path_small_for_db, PDO::PARAM_STR);
        $stmt->bindParam(':image_big', $new_path_big_for_db, PDO::PARAM_STR);
        $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
        $result = $stmt->execute();
    } catch (Exception $e) {
        raiseError('db failed: '.$e, '../');
    }
    # delete old images using old paths if needed
    if ($game['name'] != $new_name) {
        unlink('../'.$game['img_small']);
        unlink('../'.$game['img_big']);
    }
}


#redirect
header('Location: ../game.php?id='.$game_id);