<?php

require_once __DIR__ . "/../config/dbConfig.php";
function getFromDBByID($table_name, $id, $mysqli, $idRowName = 'id', $selFields = "*")
{
    $stmt = $mysqli->prepare("SELECT $selFields FROM " . $table_name . " WHERE " . $idRowName . " = ?");
    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();
    $arr = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    return !empty($arr) ? $arr[0] : null;
}

function getNonDeletedFromDB($table_name, $mysqli, $hasDeleted = true, $searchableByID = false, $orderQuery = "", $idRowName = 'id')
{
    $query = "SELECT * FROM `$table_name`";
    if ($hasDeleted)
        $query .= " WHERE deleted = 0";
    if ($orderQuery)
        $query .= " " . $orderQuery;

    $stmt = $mysqli->prepare($query);
    $stmt->execute();

    $result = $stmt->get_result();
    $arr = [];

    if ($searchableByID) { //if this is set to true the data will be accessiblee like $arr[$id] = ['name' => 'Goshko', 'email' => 'goshko@abv.bg'....]
        while ($row = $result->fetch_assoc()) {
            $arr[$row[$idRowName]] = $row;
        }
    } else { //if not it will just return array of arrays representing all the data
        $arr = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();

    return $arr;
}

function getFromDBCondition($table_name, $condition, $mysqli, $searchableByID = false, $idRowName = 'id', $selFields = "*")
{
    $stmt = $mysqli->prepare("SELECT $selFields FROM " . $table_name . " " . $condition);
    $stmt->execute();

    $result = $stmt->get_result();
    $arr = [];

    if ($searchableByID) { //if this is set to true the data will be accessiblee like $arr[$id] = ['name' => 'Goshko', 'email' => 'goshko@abv.bg'....]
        while ($row = $result->fetch_assoc()) {
            $arr[$row[$idRowName]] = $row;
        }
    } else { //if not it will just return array of arrays representing all the data
        $arr = $result->fetch_all(MYSQLI_ASSOC);
    }

    $stmt->close();

    return $arr;
}

function getCountFromDB($table_name, $condition, $mysqli)
{
    $stmt = $mysqli->prepare("SELECT COUNT(*) as cnt FROM " . $table_name . " " . $condition);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return (int) $row['cnt'];
}

function deleteFromDB($table_name, $id, $mysqli, $idRowName = 'id')
{
    $stmt = $mysqli->prepare("DELETE FROM " . $table_name . " WHERE " . $idRowName . " = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function deleteFromDBCondition($table_name, $whereCondition, $mysqli)
{
    $stmt = $mysqli->prepare("DELETE FROM " . $table_name . " " . $whereCondition);
    $stmt->execute();
    $stmt->close();
}

function setDeletedDB($table_name, $id, $mysqli, $idRowName = 'id')
{
    $stmt = $mysqli->prepare("UPDATE `$table_name` SET deleted = 1 WHERE `$idRowName` = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

function getSumFromTable(string $table, string $column, string $condition, mysqli $mysqli)
{
    $sql = "SELECT SUM($column) AS total FROM $table $condition";
    $res = $mysqli->query($sql);
    if ($res && $row = $res->fetch_assoc()) {
        return (float) $row['total'];
    }
    return 0;
}

function isFileExpiring($table_name, $file_id, $mysqli)
{
    $fileDate = getFromDBByID($table_name, $file_id, $mysqli)["end_date"];
    if (!$fileDate || $fileDate == "0000-00-00") { //if there is no end date it isnt expiring
        return false;
    }
    $lastDayNextMonth = strtotime('last day of next month');

    return strtotime($fileDate) <= $lastDayNextMonth;
}

