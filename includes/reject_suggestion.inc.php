<?php
include '../tools/dbh.tool.php';
include '../tools/error.tool.php';
global $pdo;
if (!$_SESSION['isAdmin']) {
    header('Location: ../index.php?message=you are not allowed to do that thing!');
    die();
}
if (!isset($_GET['id'])) {
    raiseError('can not delete suggestion without id given in $_GET :(', '../');
}
# get suggestion info
try {
    $stmt = $pdo->prepare('SELECT * FROM suggestions WHERE suggestions.id = :id');
    $stmt->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
    $suggestion = $stmt->fetch();
    $img_small = $suggestion['img_small'];
    $img_big = $suggestion['img_big'];
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# delete images from games_images
unlink('../'.$img_big);
unlink('../'.$img_small);
# delete suggestion
try {
    $stmt = $pdo->prepare('DELETE FROM suggestions WHERE suggestions.id = :id');
    $stmt->bindParam(":id", $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# redirect
header('Location: ../suggestions_list.php');
die();