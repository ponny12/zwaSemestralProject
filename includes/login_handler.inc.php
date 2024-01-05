<?php
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nickname = $_POST["nickname"];
    $pass = $_POST["password"];



    try {
        require "../tools/dbh.tool.php";
        global $pdo;

        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":username", $nickname);
        $stmt->execute();

        $results = $stmt->fetch();
        if (empty($results) || !password_verify($pass, $results["pwd"])) {
            $_SESSION["loginError"] = true;
            $_SESSION["lastNickname"] = $nickname;
            header("Location: ../login.php");
        } else {
            $_SESSION["loginError"] = false;
            unset($_SESSION['lastNickname']);
            $_SESSION["loginID"] = $results["id"];
            $_SESSION["loginName"] = $results["username"];
            $_SESSION["loginImage"] = $results["img_small"];


            $stmt = $pdo->prepare('SELECT * FROM admins WHERE user_id = :loginID');
            $stmt->bindParam(':loginID', $_SESSION['loginID'], PDO::PARAM_INT);
            $stmt->execute();

            $result = $stmt->fetch();
            $pdo = null;
            $stmt = null;

            if (isset($result['user_id']) && $result['user_id'] == $_SESSION['loginID']) {
                $_SESSION['isAdmin'] = true;
            } else {
                $_SESSION['isAdmin'] = false;
            }

            header("Location: ../index.php");
            die();
        }

        $pdo = null;
        $stmt = null;

        header("Location: ../login.php");

        die();
    } catch (PDOException $e) {
        die("Query failed: " . $e->getMessage());
    }


} else {
    header("Location: ../index.php");
}
