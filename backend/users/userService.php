<?php
namespace App\Services;

require_once __DIR__ . "/../config/dbConfig.php";
require_once __DIR__ . "/../config/sessionConfig.php";

require_once __DIR__ . "/../utils/dbUtils.php";

class UserService
{
    //public
    public function __construct(\mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function registerUser(string $username, string $pass, string $passConf)
    {
        //vars
        $usernameLen = strlen($username);
        $passLen = strlen($pass);
        $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
        //escape
        $username = $this->mysqli->real_escape_string($username);

        //validate 
        //input validation
        if ($usernameLen < 3 || $usernameLen > 20) //invalid username len
            return ["success" => false, "error" => "Username must be between 3 and 20 characters"];

        if ($passLen < 4) //invalid pass
            return ["success" => false, "error" => "Password must be more than 4 characters"];

        if ($pass != $passConf) //pass dont match
            return ["success" => false, "error" => "Passwords don't match"];

        //db validation
        if (\getCountFromDB("users", "WHERE username = '$username'", $this->mysqli) != 0) //username is taken
            return ["success" => false, "error" => "Username already taken"];

        //insert in db
        $stmt = $this->mysqli->prepare("INSERT INTO users (username, pass) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPass);
        $stmt->execute();
        $newUserId = $stmt->insert_id;
        $stmt->close();

        //Log in the new user
        self::logInUser($newUserId);

        return ["success" => true, "error" => ""];
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
                return ["success" => false, "error" => "Incorrect username or password"];

            //log in user
            self::logInUser($userRow["id"]);

            return ["success" => true, "error" => ""];
        }

        //User wasnt found
        $stmt->close();
        return ["success" => false, "error" => "Incorrect username or password"];
    }

    public function logOutUser()
    {
        //user id
        $userId = $_SESSION["user_id"] ?? null;

        if ($userId) { //if there was a logged in user
            //log out user
            unset($_SESSION["user_id"]);
        }
    }

    public function getUsersTsp(int $userId, int $tspId)
    {
        //Validation
        if ($userId <= 0)
            return ["success" => false, "error" => "Invalid user"];

        if ($tspId <= 0)
            return ["success" => false, "error" => "Invalid saved TSP"];

        //get from db
        $stmt = $this->mysqli->prepare("SELECT name, coords_min, coords_max, coords FROM saved_tsps WHERE id = ? AND user_id = ? AND deleted = 0");
        $stmt->bind_param("ii", $tspId, $userId);
        $stmt->execute();

        $result = $stmt->get_result();
        $tsp = $result->fetch_assoc();

        if (!$tsp)
            return ["success" => false, "error" => "Saved TSP not found"];

        $tsp["coords"] = json_decode($tsp["coords"], true);

        return ["success" => true, "error" => "", "tsp" => $tsp];
    }

    public function getAllUsersTspsNames(int $userId)
    {
        //Validation
        if ($userId <= 0)
            return ["success" => false, "error" => "Invalid user"];

        //get from db
        $stmt = $this->mysqli->prepare("SELECT id, name FROM saved_tsps WHERE user_id = ? AND deleted = 0 ORDER BY id DESC");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $result = $stmt->get_result();

        $tsps = [];

        while ($row = $result->fetch_assoc()) {
            $tsps[] = $row;
        }

        return ["success" => true, "error" => "", "tsps" => $tsps];
    }

    public function saveCustomTsp(int $userId, string $name, int $coordsMin, int $coordsMax, ?array $coords)
    {
        //Validation
        if ($userId <= 0)
            return ["success" => false, "error" => "Invalid user"];

        if ($name == "")
            return ["success" => false, "error" => "Invalid name"];

        if ($coordsMin >= $coordsMax)
            return ["success" => false, "error" => "Invalid min/max coordinates pairing"];

        if (!$coords)
            return ["success" => false, "error" => "Invalid coords"];

        //decode
        $coordsJson = json_encode($coords);

        //save to db
        $stmt = $this->mysqli->prepare("INSERT INTO saved_tsps (user_id, name, coords_min, coords_max, coords) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiis", $userId, $name, $coordsMin, $coordsMax, $coordsJson);

        if (!$stmt->execute())
            return ["success" => false, "error" => "Failed to save TSP"];

        return ["success" => true, "error" => ""];
    }


    //private
    private function logInUser(int $userId)
    {
        $_SESSION["user_id"] = $userId;
    }

    private \mysqli $mysqli;
}