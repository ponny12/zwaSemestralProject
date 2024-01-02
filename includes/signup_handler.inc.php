<?php
if(!isset($_SESSION))
{
    session_start();
}

function checkUsernameAlreadyExist($username)
{
    try {
        global $pdo;
        require "dbh.inc.php";

        $query = "SELECT username FROM users;";
        $stmt = $pdo->prepare($query);

        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach ($results as $user) {
            if ($username == $user[0]) {
                $pdo = null;
                $stmt = null;
                return true;
            }
        }

        $pdo = null;
        $stmt = null;
        return false;
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["confirmLicense"]) {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $pass1 = $_POST["password1"];
    $pass2 = $_POST["password2"];


    if (strlen($username) < 5 || $pass1 == strtolower($pass1) || strlen($pass1) < 8 || $pass1 != $pass2 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION["errorType"] = "login data are not valid.";
        header("Location: ../error.php");
        die();
    }
    if (checkUsernameAlreadyExist($username)) {
        header("Location: ../signup.php");
        die();
    }



    try {
        require "dbh.inc.php";
        global $pdo;
        $query = "INSERT INTO users (username, pwd, email) VALUES (:username, :pwd, :email);";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $username);
        $options = ['cost' => 12];
        $hash = password_hash($pass1, PASSWORD_BCRYPT, $options);
        $stmt->bindParam(":pwd", $hash);
        $stmt->bindParam(":email", $email);

        $stmt->execute();
//        $query = "INSERT INTO users (username, pwd, email) VALUES (?, ?, ?);";
//        $stmt = $conn->prepare($query);
//
//        $stmt->bind_param("sss", $username, $pass1, $email);
//        $stmt->execute();


        $_SESSION['login'] = true;
        $pdo = null;
        $stmt = null;

        header("Location: ../login.php");

        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }


} else {
    header("Location: ../error.php");
}
