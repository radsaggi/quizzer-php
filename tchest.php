<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$from = "tchestpage";
require './support/check.php';

if (!isset($_GET["q"]) || $_SESSION["question"] === "") {
    header("Location: ./profile.php");
    return;
}

function treasure_chest_located($i) {
    require_once './support/dbcon.php';
    global $db_connection;
    global $_SESSION;
    $value = 1 << $i;
    $_SESSION["tchests"] = $_SESSION["tchests"] | $value;
    $query = "UPDATE `ContestantsData` SET `TChests Unlocked`= '{$_SESSION["tchests"]}' WHERE "
            . "`Username` = '{$_SESSION["username"]}'";
    mysqli_query($db_connection, $query);
    
    $score = (int) ($_SESSION["total-score"] / 10);
    push_increase("Treasure Chest UNLOCKED!!!", $score, FALSE);
    sync_scores();
}

for ($i = 0; $i < 4; $i++) {
    $string = create_tchest_string($i, $_SESSION["prev-salt"]);
    if ($string === $_GET["q"] && !check_tchest($i)) {
        treasure_chest_located($i);
        break;
    }
}

header("Location: ./profile.php");
die();
