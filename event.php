<?php
if(!isset($_SESSION))
{
    session_start();
}
if (empty($_SESSION['loginID'])) {
    header('Location: login.php?message=login first!');
    die();
}


require_once 'tools/dbh.tool.php';
global $pdo;
require 'tools/error.tool.php';

# get all data about event from db
if (isset($_GET['id'])) {
    try {


        $stmt = $pdo->prepare('SELECT *, events.description as event_description FROM events JOIN games ON events.game_id = games.id WHERE events.id = :event_id');
        $stmt->bindParam(':event_id', $_GET['id']);
        $stmt->execute();
        $event = $stmt->fetch();
        if (empty($event)) {
            raiseError('event does not exist (id: '.$_GET["id"].')');
        }
        $max_people = $event['max_people'];
        $registered_people = $event['registered_people'];
    } catch (PDOException $e) {
        echo $e->getMessage(); die();
    } finally {
        $stmt = null;
    }

} else {
    $_SESSION['errorType'] = 'event does not exist (not has even been chosen).';
    header('Location: error.php');
    die();
}

# transform datetime
$php_date = strtotime($event['event_time']);
$dateYMD = date( 'M d, Y', $php_date );
$dateDOFW = date( 'l', $php_date );
$dateTIME = date( 'H:i', $php_date );
# get info about creator and participants
try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE users.id = :creator_id');
    $stmt->bindParam(':creator_id', $event['creator_id']);
    $stmt->execute();
    $creator = $stmt->fetch();
    if (empty($creator)) {
        raiseError('can not find creator (id: '.$event['creator_id'].') of event (id: '.$_GET["id"].')');
    }
} catch (PDOException $e) {
    echo $e->getMessage(); die();
} finally {
    $stmt = null;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM events 
                                JOIN events_users ON events_users.event_id = events.id
                                JOIN users ON events_users.user_id = users.id
                                WHERE events.id = :event_id');
    $stmt->bindParam(':event_id', $_GET['id']);
    $stmt->execute();
    $participants = $stmt->fetchAll();
    if (empty($creator)) {
        raiseError('can not find creator (id: '.$event['creator_id'].') of event (id: '.$_GET["id"].')');
    }
} catch (PDOException $e) {
    echo $e->getMessage(); die();
} finally {
    $stmt = null;
}
# find out if current user is creator or already joined event
$is_creator = false;
$already_joined = false;
if ($creator['id'] == $_SESSION['loginID']) {
    $is_creator = true;
} else {
    foreach ($participants as $participant) {
        if ($participant['id'] == $_SESSION['loginID']) {
            $already_joined = true;
            break;
        }
    }
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

<div class="event_content">
    <div class="container">
        <a href="game.php?id=<?php echo $event['game_id']?>"><img src="<?php echo $event['img_big'] ?>" alt="eleven table tennis logo"></a>
        <div class="text_section">
            <a href="game.php?id=<?php echo $event['game_id']?>" class="title"><?php echo $event['name'] ?></a>
            <div class="text"><?php echo $event['event_description'] ?></div>
        </div>
        <div class="info_section">
            <div class="counter"><?php echo $event['registered_people'] ?>/<?php echo $event['max_people'] ?></div>
            <div class="datetime_section">
                <div class="datetime"><?php echo $dateYMD ?></div>
                <div class="datetime"><?php echo $dateDOFW ?></div>
                <div class="datetime"><?php echo $dateTIME ?></div>
            </div>
        </div>
    </div>
    <?php
    if ($is_creator || $_SESSION['loginID'] == 1) { ?>
        <form action="includes/delete_event_handler.inc.php" class="delete_content" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $_GET['id']?>">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['loginID']?>">
            <label for="delete_text"></label>
            <input type="text" name="delete_text" class="delete_text" id="delete_text" placeholder="write 'DELETE' to approve">
            <input type="submit" value="DELETE" class="orange_button delete_button">
        </form>
    <?php }
    if (!$already_joined && !$is_creator) { ?>
        <form action="includes/add_user_to_event_handler.inc.php" class="join_content" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $_GET['id']?>">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['loginID']?>">
            <input <?php echo $max_people == $registered_people ? 'disabled' : '' ?> type="submit" value="JOIN" class="orange_button join_button">
        </form>
    <?php } else if (!$is_creator) { ?>
        <form action="includes/remove_user_from_event_handler.inc.php" method="POST">
            <input type="hidden" name="event_id" value="<?php echo $_GET['id']?>">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['loginID']?>">
            <input type="submit" value="LEAVE" class="orange_button leave_button">
        </form>
    <?php }
    ?>


    <div class="created_by">
        <div class="title">created by</div>
        <a href="profile.php?id=<?php echo $creator['id']?>" class="creator">
            <img src="<?php echo $creator['img_small']?>" alt="creator photo">
            <div class="name"><?php echo htmlspecialchars($creator['username'])?></div>
        </a>
    </div>

    <div class="participants">
        <div class="title">participants</div>
        <?php
        foreach ($participants as $user) { ?>
            <a href="profile.php?id=<?php echo $user['id']?>" class="user">
                <img src="<?php echo $user['img_small']?>" alt="">
                <div class="name"><?php echo $user['username'] ?></div>
            </a>
        <?php }

        if (empty($participants)) { ?>
            <div class="zero_participants">There are no participants yet :( <br> Become first!</div>
        <?php } ?>


    </div>
</div>



</body>

</html>

<?php

include "footer.php";