<?php
namespace App\Services;

require_once __DIR__ . "/../config/dbConfig.php";
require_once __DIR__ . "/../config/sessionConfig.php";

class UserService
{
    public function __construct(\mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function validateAndLogInUser(string $username, string $pass)
    {
        //Select the user by username
        $stmt = $this->mysqli->prepare("SELECT id, pass FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows == 1) { //user was found

            //get the res
            $userRow = $res->fetch_assoc();
            $stmt->close();

            if (!password_verify($pass, $userRow["pass"])) //passwords dont match
                return [0, "Incorrect username or password"];

            //log in user
            self::logInUser($userRow["id"]);

            return [1, ""];
        }

        //User wasnt found
        $stmt->close();
        return [0, "Incorrect username or password"];
    }

    private function logInUser(int $userId)
    {
        $_SESSION["user_id"] = $userId;
    }

    private \mysqli $mysqli;
}