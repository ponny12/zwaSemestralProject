<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST['event_id']) || empty($_POST['user_id'])) {
    header('Location: ../index.php');
    die();
}
require '../tools/error.tool.php';
require '../includes/dbh.inc.php';
global $pdo;

$event_id = intval($_POST['event_id']);
$user_id = intval($_POST['user_id']);

if ($user_id != $_SESSION['loginID']) {
    raiseError('try to add user (id: '.$user_id.') while under account of user (id: '.$_SESSION['loginID'].')', '../');
}

# check that there are available place for user at the event
try {
    $stmt = $pdo->prepare('SELECT events.max_people, events.registered_people FROM events WHERE events.id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
    $people_after_update = $result['registered_people'] + 1;
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
if ($result['registered_people'] >= $result['max_people']) {
    raiseError('there are no place in event (id: '.$event_id.') for user (id: '.$user_id.')', '../');
}
# check that user not a creator of the event
try {
    $stmt = $pdo->prepare('SELECT events.creator_id FROM events WHERE events.id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
if ($result['creator_id'] == $user_id) {
    raiseError('User (id: '.$user_id.') is a creator of the event (id: '.$event_id.'), so can not be an participant at the same time', '../');
}
# check that user not already joined the event
try {
    $stmt = $pdo->prepare('SELECT * FROM events_users WHERE events_users.event_id = :event_id');
    $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e, '../');
}
foreach ($result as $user) {
    if ($user['user_id'] == $user_id) {
        raiseError('User (id: '.$user_id.') already joined the event (id: '.$event_id.')', '../');
    }
}


# finally, add user to the event
try {
    $stmt = $pdo->prepare('INSERT INTO events_users (event_id, user_id) VALUES (:event_id, :user_id)');
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