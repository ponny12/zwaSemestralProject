<?php
include '../tools/dbh.tool.php';
include '../tools/error.tool.php';
global $pdo;

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['submit'])) {
    header('Location: ../index.php');
    die();
}

$id = $_POST['id'];
$pwd = $_POST['password'];
if (!isset($_POST['delete_checkbox'])) {
    header('Location: ../profile.php?id='.$id);
    die();
}
# get pwd and images paths
try {
    $stmt = $pdo->prepare('SELECT pwd, img_small, img_big FROM users WHERE users.id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetch();
    $pwd_hash = $results['pwd'];
    $img_small = $results['img_small'];
    $img_big = $results['img_big'];
} catch (Exception $e) {
    raiseError('db failed: ' . $e, '../');
}

if (!password_verify($pwd, $pwd_hash)) {
    header('Location: ../profile.php?id='.$id);
    die();
}
# delete from db all about user, his events etc.
unlink('../'.$img_small);
unlink('../'.$img_big);
try {
    $stmt = $pdo->prepare('SELECT events.id FROM events WHERE events.creator_id = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $all_events_where_user_is_creator = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: ' . $e, '../');
}

foreach ($all_events_where_user_is_creator as $event) {
    $event_id = $event['id'];
    try {
        $stmt = $pdo->prepare('DELETE FROM events_users WHERE events_users.event_id = :event_id');
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        raiseError('db failed: ' . $e, '../');
    }
    try {
        $stmt = $pdo->prepare('DELETE FROM events WHERE events.id = :event_id');
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (Exception $e) {
        raiseError('db failed: ' . $e, '../');
    }
}
# delete all users activities in other events
try {
    $stmt = $pdo->prepare('DELETE FROM events_users WHERE events_users.user_id = :user_id');
    $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: ' . $e, '../');
}
# delete user
try {
    $stmt = $pdo->prepare('DELETE FROM users WHERE users.id = :user_id');
    $stmt->bindParam(':user_id', $id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: ' . $e, '../');
}

# unset all session fields

session_unset();
session_destroy();

header('Location: ../index.php');
