<?php
if(!isset($_SESSION))
{
    session_start();
}

if (empty($_SESSION['loginID'])) {
    header('index.php?message=login first');
    die();
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
    <script defer src="js/addGameScript.js"></script>

</head>

<body>

<?php include "header.php" ?>

<div class="admin_content">
    <div class="add_game_title">SUGGEST GAME</div>
    <form action="includes/suggest_game_handler.inc.php" class="create_game_form" method="POST" enctype="multipart/form-data" id="create_game_form">
        <input type="hidden" name="suggested_by" value="<?php echo $_SESSION['loginID'] ?>">
        <label for="game_name">Game name:</label>
        <input type="text" name="name" id="game_name" class="name_input">
        <div class="error" id="name_error"></div>

        <label for="game_description">Game description:</label>
        <textarea name="description" id="game_description" class="description_input"></textarea>
        <div class="error" id="description_error"></div>

        <!--image-->

        <label for="game_image">Game image:</label>
        <input type="file" name="image" id="game_image" accept="image/webp, image/jpeg, image/png">
        <div class="error" id="image_error"></div>

        <!--checkboxes-->
        <div class="checkbox_title">choose platform(s):</div>
        <div class="checkboxes">
            <label for="meta_checkbox">meta<input type="checkbox" name="meta" id="meta_checkbox"></label>
            <label for="pico_checkbox">pico<input type="checkbox" name="pico" id="pico_checkbox"></label>
            <label for="pcvr_checkbox">pcvr<input type="checkbox" name="pcvr" id="pcvr_checkbox"></label>
        </div>
        <div class="error" id="checkbox_error"></div>

        <input type="submit" id="submit" name="submit" value="suggest" class="submit">

    </form>
</div>

<?php include "footer.php"; ?>
</body>

</html>

