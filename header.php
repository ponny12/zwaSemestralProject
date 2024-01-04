<?php
if (isset($_SESSION['loginID'])) {
    $isLogin = true;

    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']) {
        $isAdmin = true;
    } else {
        $isAdmin = false;
    }



} else {
    $isLogin = false;
    $isAdmin = false;
}

?>
<header>
    <div class="header_content">
        <a href="index.php" class="header_logo">
            <div class="find">FIND</div>
            <div class="your">YOUR</div>
            <div class="team">TEAM</div>
        </a>
        <div class="header_img_holder">
            <img class="header_img" src="images/header_image.png" alt="man in VR headset">
        </div>
        <div class="header_menu">
            <a href="game_list.php" class="header_menu_btn">games</a>
            <a href="create.php" class="header_menu_btn">create</a>
            <a href="discover.php" class="header_menu_btn">discover</a>
            <?php if ($isLogin) { ?>
                <a href="profile.php?id=<?php echo htmlspecialchars($_SESSION["loginID"])?>" class="btn header_profile_btn">
                    <img src="<?php echo htmlspecialchars($_SESSION["loginImage"])?>" alt="profile image">
                    <?php echo htmlspecialchars($_SESSION["loginName"])?>
                </a>
                <a href="includes/logout_handler.inc.php" class="btn logout_btn"><img src="images/logout_green.png" class="logout_img" alt="log out"></a>

                <?php
            } else { ?>
                <a href="login.php" class="btn header_login_btn">log in</a>
                <a href="signup.php" class="btn header_signup_btn">sign up</a>
                <?php
            }
            ?>

        </div>
    </div>
</header>

