<?php
//if(!isset($_SESSION))
//{
//    session_start();
//}
//
//if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["confirmLicense"]) {
//    $nickname = htmlspecialchars($_POST["nickname"]);
//    $email = htmlspecialchars($_POST["email"]);
//    $pass1 = htmlspecialchars($_POST["password1"]);
//    $pass2 = htmlspecialchars($_POST["password2"]);
//
//
//    if (strlen($nickname) < 5 || $pass1 == strtolower($pass1) || strlen($pass1) < 8 || $pass1 != $pass2) {
//        die("ERROR IN DATA");
//    }
//
//    echo "hello $nickname";
//    header("Location: index.html");
//
//
//} else {
//    echo '<h1> FUCK OFF! </h1>';
//    header("Location: ../index.html");
//}
