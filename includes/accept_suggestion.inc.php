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
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
$id = $suggestion['id'];
$name = $suggestion['name'];
$description = $suggestion['description'];
$img_small = $suggestion['img_small'];
$img_big = $suggestion['img_big'];
$meta = $suggestion['meta'];
$pico = $suggestion['pico'];
$pcvr = $suggestion['pcvr'];

# check that game do not already exist
try {
    $stmt = $pdo->prepare('SELECT * FROM games WHERE LOWER(games.name) = LOWER(:name)');
    $stmt->bindParam(":name", $name);
    $stmt->execute();
    $result = $stmt->fetch();
    if (!empty($result)) {
        raiseError('cannot add game '.htmlspecialchars($name).' from suggestion because it is already in game list', '../');
    }
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# add game
try {
    $stmt = $pdo->prepare('INSERT INTO games (name, description, img_small, img_big) VALUES (:name, :description, :img_small, :img_big)');
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":img_small", $img_small);
    $stmt->bindParam(":img_big", $img_big);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# get new game id
try {
    $stmt = $pdo->prepare('SELECT games.id FROM games WHERE LOWER(games.name) = LOWER(:name)');
    $stmt->bindParam(":name", $name);
    $stmt->execute();
    $new_id = $stmt->fetch()['id'];
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# add platforms
try {
    $stmt = $pdo->prepare('INSERT INTO games_platforms (id, meta, pico, pcvr) VALUES (:id, :meta, :pico, :pcvr)');
    $stmt->bindParam(":id", $new_id, PDO::PARAM_INT);
    $stmt->bindParam(":meta", $meta, PDO::PARAM_INT);
    $stmt->bindParam(":pico", $pico, PDO::PARAM_INT);
    $stmt->bindParam(":pcvr", $pcvr, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
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