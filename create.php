<?php
if(!isset($_SESSION))
{
    session_start();
}
require 'includes/dbh.inc.php';
require 'tools/error.tool.php';
global $pdo;

$games = [];
$successfully_upload = false;

if (empty($_SESSION['loginID'])) {
    header('Location: login.php');
    die();
}

# check if search btn pressed
if (isset($_POST['search_button']) && $_POST['search_line'] != '' && $_POST['search_line'] != '_') {
    $search = $_POST['search_line'];
    $sanitized_search = $search;
    #sanitizing
    $sanitized_search = str_replace('\\', '\\\\', $sanitized_search);
    $sanitized_search = str_replace('%', '\%', $sanitized_search);
    $sanitized_search = str_replace('_', '\_', $sanitized_search);
    $sanitized_search = str_replace('"', '\"', $sanitized_search);
    $sanitized_search = str_replace('-', '\-"', $sanitized_search);
} else {
    $search = '_';
    $sanitized_search = $search;
}


try {
    $stmt = $pdo->prepare('SELECT * FROM games g
                                JOIN games_platforms p ON g.id = p.id
                                WHERE g.name LIKE "%'.$sanitized_search.'%"
                                ORDER BY g.name');
    $stmt->execute();
    $games = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e);
}


if (isset($_POST['create_button'])) {
    #check all fields
    #get game_id
    if (isset($_POST['game'])) {
        $game_id = $_POST['game'];
        # check that game exists
        try {
            $stmt = $pdo->prepare('SELECT id FROM games WHERE games.id = :game_id');
            $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
        } catch (Exception $e) {
            $_SESSION['errorType'] = 'db error: '.htmlspecialchars($e).', while searching for game with id: '.$game_id;
            header('Location: error.php');
            die();
        }




        if (empty($result)) {
            $_SESSION['errorType'] = 'attempt to create event, but did not found game with id: '.htmlspecialchars($game_id);
            header('Location: error.php');
            die();
        }


    } else {
        $_SESSION['errorType'] = 'attempt to create event, but no game selected';
        header('Location: error.php');
        die();
    }
    # get event description
    if (isset($_POST['event_description']) && $_POST['event_description'] != '') {
        $event_description = $_POST['event_description'];
    } else {
        $_SESSION['errorType'] = 'attempt to create event, but no description given';
        header('Location: error.php');
        die();
    }
    # get datetime
    if (isset($_POST['event_time'])) {
        $event_time = $_POST['event_time'];
    } else {
        $_SESSION['errorType'] = 'attempt to create event, but no datetime selected';
        header('Location: error.php');
        die();
    }
    # get max people
    if (isset($_POST['max_people'])) {
        $max_people = intval($_POST['max_people']);
        if ($max_people < 2) {
            $_SESSION['errorType'] = 'attempt to create event, but no max people less than 2';
            header('Location: error.php');
            die();
        }
    } else {
        $_SESSION['errorType'] = 'attempt to create event, but no max people selected';
        header('Location: error.php');
        die();
    }

    try {
        $timestamp = strtotime($event_time);
        $event_time_sql_format = date('Y-m-d H:i:s', $timestamp);
    } catch (Exception $e) {
        $_SESSION['errorType'] = 'failed convert datetime: '.$event_time.'. Error occurred: '.$e;
        header('Location: error.php');
        die();
    }






    # insert event to db
    try {
        $stmt = $pdo->prepare('INSERT INTO events (creator_id, game_id, description, event_time, max_people)
                                    VALUES (:creator_id, :game_id, :description, :event_time, :max_people)');
        $stmt->bindParam(':creator_id', $_SESSION['loginID'], PDO::PARAM_INT);
        $stmt->bindParam(':game_id', $game_id, PDO::PARAM_INT);
        $stmt->bindParam(':description', $event_description);
        $stmt->bindParam(':event_time', $event_time_sql_format);
        $stmt->bindParam(':max_people', $max_people);
        $successfully_upload = $stmt->execute();
    } catch (Exception $e) {
        $_SESSION['errorType'] = 'db failed: '.$e;
        header('Location: error.php');
        die();
    }




}



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>VRMates - create new event</title>
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">

    <script defer src=""></script>
</head>

<body>

    <?php include "header.php" ?>



    <div class="create_content">
        <?php
        if ($successfully_upload) { ?>
            <div class="success">You have successfully loaded the event!</div>
        <?php }

        ?>
        <form action="#" class="create_event_form" method="POST">

            <div class="title">
                create your event
            </div>

            <div class="search">
                <input type="search" name="search_line" id="search" value="<?php echo $search == '_' ? '' : $search ?>" placeholder="type a game name" class="search_line">
                <input type="submit" name="search_button" value="search" class="search_button">
            </div>

            <div class="game_window">
                <?php
                if (empty($games)) { ?>
                    <div class="nothing_found">nothing found...</div>
                <?php }

                foreach ($games as $game) { ?>

                    <label for="<?php echo $game['id']?>">
                        <img src="<?php echo htmlspecialchars($game['img_small']) ?>" alt="game_logo">
                        <?php echo htmlspecialchars($game['name']) ?>
                        <input type="radio" name="game" id="<?php echo $game['id']?>" value="<?php echo $game['id']?>" class="radio">

                    </label>

                <?php }

                ?>
            </div>



            <label>Event description: <textarea name="event_description" id="event_description" placeholder="write all what people need to know about your event!"></textarea></label>


            <label>Select date and time: <input type="datetime-local" name="event_time" id=""> </label>
            <label>How many people do you need: <input type="number" name="max_people" id="event_people" min="2"
                    max="99"></label>
            <input type="submit" name="create_button" value="create" class="create_button">
        </form>
    </div>

    <?php include "footer.php"; ?>

</body>

</html>

