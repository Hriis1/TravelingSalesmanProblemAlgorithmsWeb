<?php
require_once __DIR__ . "/../config/dbConfig.php";
require_once __DIR__ . "/userService.php";

use App\Services\UserService;

$userService = new UserService($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST["action"] ?? "") == "registerUser") { //register user
        $username = $_POST["username"] ?? "";
        $pass = $_POST["password"] ?? "";
        $passConf = $_POST["password_confirm"] ?? "";

        $res = $userService->registerUser($username, $pass, $passConf);
        echo json_encode($res);
        exit;
    }

    if (($_POST["action"] ?? "") == "logInUser") { //log in user
        $username = $_POST["username"] ?? "";
        $pass = $_POST["password"] ?? "";

        $res = $userService->validateAndLogInUser($username, $pass);
        echo json_encode($res);
        exit;
    }

    if (($_POST["action"] ?? "") == "logOutUser") { //log out user
        $userService->logOutUser();
        echo 1;
        exit;
    }
}