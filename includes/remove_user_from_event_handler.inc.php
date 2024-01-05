<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST['event_id']) || empty($_POST['user_id'])) {
    header('Location: ../index.php');
    die();
}
require '../tools/error.tool.php';
require '../tools/dbh.tool.php';
global $pdo;

$event_id = intval($_POST['event_id']);
$user_id = intval($_POST['user_id']);

if ($user_id != $_SESSION['loginID']) {
    raiseError('try to remove user (id: '.$user_id.') while under account of user (id: '.$_SESSION['loginID'].')', '../');
}

# get number of users after remove
try {
    $stmt = $pdo->prepare('SELECT events.registered_people FROM events WHERE events.id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    $people_after_update = $result['registered_people'] - 1;
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# check that user is a participant of the event
try {
    $stmt = $pdo->prepare('SELECT * FROM events_users WHERE events_users.event_id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
$in_event = false;
foreach ($result as $user) {
    if ($user['user_id'] == $user_id) {
        $in_event = true;
        break;
    }
}
if (!$in_event) {
    raiseError('attempt of remove user (id: '.$user_id.'), but user is not a participant of event (id: '.$event_id.')', '../');
}

# finally, remove user from the event
try {
    $stmt = $pdo->prepare('DELETE FROM events_users WHERE events_users.event_id = :event_id AND events_users.user_id = :user_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# change number of registered users on the event
try {
    $stmt = $pdo->prepare('UPDATE events SET events.registered_people = :new_r_p_value WHERE events.id = :event_id');
    $stmt->bindParam(':new_r_p_value', $people_after_update, PDO::PARAM_INT);
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
# redirect
header('Location: ../event.php?id='.$event_id);
die();