<?php
if(!isset($_SESSION)) session_start();
require 'tools/error.tool.php';
require 'includes/dbh.inc.php';
global $pdo;
try {
    $stmt = $pdo->query('SELECT *, events.id as event_id FROM events JOIN games ON events.game_id = games.id
                                WHERE events.event_time >= CURDATE()
                                ORDER BY events.event_time
                                LIMIT 4');
    $hot_events = $stmt->fetchAll();
} catch (Exception $e) {
    raiseError('db failed: '.$e.' While selecting hot events');
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

    <div class="body_content">
        <div class="hot_events_section">
            <div class="textnew">
                &#128293;
                <span class="hot">HOT</span> events
                &#128293;
            </div>

            <div class="menu">
<!--                <a href="#" class="circle">-->
<!--                    <p class="arrow"></p>-->
<!--                    <p class="line"></p>-->
<!--                </a>-->

                <?php
                if (empty($hot_events)) { ?>
                    <div class="nothing_hot">There are no upcoming events at all :(</div>
                <?php }


                foreach ($hot_events as $event) {
                    $php_date = strtotime($event['event_time']);

                    try {
                        $event_time_proper_format = new DateTime(strval(date("Y-m-d", $php_date)));
                        $today = new DateTime(strval(date("Y-m-d", strtotime('today'))));
                        if ($today > $event_time_proper_format) {
                            $time_text = 'already passed';
                        } else if ($today == $event_time_proper_format) {
                            $time_text = 'today';
                        } else {
                            $diff = $today->diff($event_time_proper_format)->days;
                            if ($diff == 1) {
                                $time_text = 'tomorrow';
                            } else {
                                $time_text = 'in '.$diff.' days';
                            }
                        }
                    } catch (Exception $e) {
                        raiseError('datetime failed: '.$e);
                        die();
                    }

                    ?>
                    <a href="event.php?id=<?php echo $event['event_id'] ?>" class="item">
                        <div class="title"><?php echo htmlspecialchars($event['name']) ?></div>
                        <div class="date"><?php echo strtoupper($time_text) ?></div>
                        <img src="<?php echo $event['img_big'] ?>" alt="beatsaber" class="img">
                    </a>
                <?php }

                ?>

<!--                <a href="event.php" class="item">-->
<!--                    <div class="title">BEAT SABER</div>-->
<!--                    <div class="date">TODAY / 17:30</div>-->
<!--                    <img src="images\beat-saber.jpg" alt="beatsaber" class="img">-->
<!--                    <button class="more">more...</button>-->
<!--                </a>-->
<!--                <a href="event.php" class="item">-->
<!--                    <div class="title">eleven table tennis</div>-->
<!--                    <div class="date">TODAY / 17:30</div>-->
<!--                    <img src="images/eleven_logo.png" alt="beatsaber" class="img">-->
<!--                    <button class="more">more...</button>-->
<!--                </a>-->
<!--                <a href="event.php" class="item">-->
<!--                    <div class="title">rumble vr</div>-->
<!--                    <div class="date">TODAY / 17:30</div>-->
<!--                    <img src="images/rumble_logo.png" alt="beatsaber" class="img">-->
<!--                    <button class="more">more...</button>-->
<!--                </a>-->
<!--                <a href="event.php" class="item">-->
<!--                    <div class="title">walkabout minigolf</div>-->
<!--                    <div class="date">TODAY / 17:30</div>-->
<!--                    <img src="images/walkabout_minigolf_logo.png" alt="beatsaber" class="img">-->
<!--                    <button class="more">more...</button>-->
<!--                </a>-->
<!--                <a href="#" class="circle">-->
<!--                    <p class="arrow left_arrow"></p>-->
<!--                    <p class="line"></p>-->
<!--                </a>-->
            </div>

        </div>
        <div class="decoration_line"></div>
        <div class="about_section">
            <h1>
                Welcome to VRMates!<br> the platform that brings together individuals <br> seeking enjoyable
                experiences
                in
                virtual reality
            </h1>
            <div class="decoration_line"></div>

            <h2 class="key_features_title">Key Features:</h2>
            <div class="key_features_conteiner">
                <div class="key_features_block">
                    <h3 class="key_features_block_title">Connect and Play:</h3>
                    <div class="key_features_block_text">

                        Easily connect with fellow VR enthusiasts who share your passion. Whether you're a solo gamer
                        looking
                        for a team or
                        someone who just wants to explore VR experiences with new friends, VRMates is the place to be.
                    </div>

                </div>
                <div class="key_features_block">

                    <h3 class="key_features_block_title">Create and Join Events:</h3>
                    <div class="key_features_block_text">

                        Take charge of your VR social life by creating events tailored to your interests. Host gaming
                        sessions,
                        virtual meetups,
                        or collaborative experiences – the possibilities are endless. Alternatively, browse and join
                        existing
                        events to meet new
                        people and expand your VR circle.
                    </div>

                </div>

                <div class="key_features_block">

                    <h3 class="key_features_block_title">Absolutely Free:</h3>
                    <div class="key_features_block_text">
                        VRMates is committed to breaking down barriers. Joining our community, creating events, and
                        connecting
                        with others – all
                        of it is completely free! We believe that everyone should have the opportunity to enjoy the
                        wonders
                        of
                        virtual reality
                        without any financial constraints.
                    </div>
                </div>

            </div>

            <div class="decoration_line"></div>

            <h2 class="how_it_works_title">How It Works:</h2>
            <div class="how_it_works_conteiner">
                <div class="how_it_works_block">
                    <h3 class="how_it_works_block_title">Sign Up:</h3>
                    <div class="how_it_works_block_text">
                        Creating your VRMates account is quick and easy. Simply sign up, set up your profile, and start
                        exploring the VR
                        community.
                    </div>
                </div>
                <div class="how_it_works_block">

                    <h3 class="how_it_works_block_title">Discover Events:</h3>
                    <div class="how_it_works_block_text">
                        Browse through a variety of upcoming events hosted by members. Whether it's a VR game night, a
                        virtual
                        museum tour, or a
                        creative collaboration, there's something for everyone.
                    </div>
                </div>

                <div class="how_it_works_block">

                    <h3 class="how_it_works_block_title">Create Your Event:</h3>
                    <div class="how_it_works_block_text">
                        Feeling adventurous? Plan your own VR event and invite others to join. Choose your activity, set
                        the
                        date and time, and
                        watch your event come to life with participants eager to share the experience with you.
                    </div>

                </div>


                <div class="how_it_works_block">

                    <h3 class="how_it_works_block_title">Connect and Enjoy:</h3>
                    <div class="how_it_works_block_text">

                        Forge new friendships, embark on virtual adventures, and immerse yourself in the exciting world
                        of
                        VR.
                        Connect with
                        VRMates who share your interests and make every VR experience memorable.
                    </div>

                </div>

            </div>



        </div>

    </div>


    <?php include "footer.php"; ?>
</body>

</html>

