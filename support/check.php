<?php

if (!file_exists("./support/dbcon.php")) {
    header("Location: ./index.php");
    die();
}
require_once './support/dbcon.php';
session_start();

if (!function_exists("destroy_session")) {

    function destroy_session() {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', 0, $params['path'], $params['domain'], $params['secure'], isset($params['httponly']));
        session_destroy();
        unset($_SESSION);
        session_write_close();
    }

}

if (!function_exists("push_increase")) {

    function push_increase($text, $value, $both = true) {
        global $_SESSION;
        $n = count($_SESSION["increase"]);
        $_SESSION["increase"][$n]["text"] = $text;
        $_SESSION["increase"][$n]["value"] = $value;
	
	if ($both) {
            $_SESSION["level-score"] += $value;
        }
        $_SESSION["total-score"] += $value;
    }

}

if (!function_exists("sync_scores")) {

    function sync_scores() {
        global $_SESSION;
        require_once './support/dbcon.php';
        global $db_connection;
        $query = "UPDATE `ContestantsData` "
                . "SET `Level Score` = '{$_SESSION["level-score"]}', `Total Score` = '{$_SESSION["total-score"]}' "
                . "WHERE `Username` = '{$_SESSION["username"]}';";
        mysqli_query($db_connection, $query);
    }

}

if (!function_exists("get_tchest_count")) {

    function get_tchest_count() {
        global $_SESSION;
        $val = $_SESSION["tchests"];
        $count = 0;

        for ($i = 0; $i < 4; $i++) {
            if ((($val >> $i) & 1) == 1) {
                $count++;
            }
        }
        return $count;
    }

}

if (!function_exists("create_tchest_string")) {

// The correct format for tchest query is as follows
// md5([LevelNo][QuestionNo][TChestType][Salt])
// take first 8 characters of the hash

    function create_tchest_string($type, $salt) {
        global $_SESSION;
        $hash = md5($_SESSION["level"] . $_SESSION["question"] . $type . $salt);
        return substr($hash, 0, 12);
    }

}

if (!isset($CONST)) {

    $CONST["advance"] = 6;
    $CONST["tchest-tries"] = 5;
    $CONST["tchest-keyword"] = "ilovenjath";
    $CONST["njath-home"] = "www.anwesha.info/njath/";
        
    require_once './support/dbcon.php';
    global $db_connection;
    $query = "SELECT COUNT(DISTINCT SUBSTRING(`Question ID`,1,1)) AS `C` FROM `Questions`";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $CONST["levels"] = $query["C"];
    $query = "SELECT COUNT(DISTINCT SUBSTRING(`Question ID`,2,1)) AS `C` FROM `Questions`";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $CONST["questions"] = $query["C"];
    $query = "SELECT COUNT(DISTINCT SUBSTRING(`Question ID`,3,1)) AS `C` FROM `Questions`";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $CONST["buffer"] = $query["C"];
    
    if (!function_exists("load_constants")) {
	 function load_constants() {
	 	global $CONST;
	 	global $_SESSION;
	 	
	 	$l = $_SESSION["level"];
    		$CONST["advance-bonus"] = 40 * $l;	 	
	        $CONST["question-cost"] = 20 * $l;
	 	$CONST["question-score"] = 30 * $l;
	 	$CONST["question-hinted-score"] = 20 * $l;
	        $CONST["tchest-bonus"] = 10 * $l;
    		$CONST["question-penalty"] = 30 * $l;
    		$CONST["bonus-quest"] = 50 * $l;
	 }
    }
}

function checkFromVariable_Account($from) {
    return ($from === "questionpage") || $from === "profilepage" || $from === "tchestpage" || $from === "logoutpage";
}

function checkFromVariable_Outside($from) {
    return $from === "homepage" || $from === "registerpage";
}

function checkFromVariable_Common($from) {
    return $from === "rulespage" || $from === "leaderboardpage";
}

//VARIABLES
//username
//level
//question
//increase[]
//level-score
//total-score
//advance-level
//tchests
//salt
//prev-salt
//die();
if (!isset($_SESSION["username"], $_SESSION["level"], $_SESSION["question"], $_SESSION["level-score"], $_SESSION["total-score"], $_SESSION["salt"], $_SESSION["prev-salt"], $_SESSION["advance-level"], $_SESSION["tchests"])) {
    //Either just logged in or didnt log in.

    if (!isset($user)) {
        destroy_session();

        //Redirection for not logged in
        if (isset($from)) {
            if (checkFromVariable_Account($from)) {
                header("Location: ./index.php?msg=Please%20log%20in%20first...");
                die();
            } else if (checkFromVariable_Outside($from) || checkFromVariable_Common($from)) {
                //Nothing to do... 
            }
        } else {
            header("Location: ./index.php");
            die();
        }
        return;
    }

    //Just logged in   
    $query = "SELECT * FROM `ContestantsData` WHERE `Username` = '{$user}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $_SESSION["username"] = $user;
    $_SESSION["level"] = $query["Level"];
    $_SESSION["question"] = "";
    $_SESSION["level-score"] = $query["Level Score"];
    $_SESSION["total-score"] = $query["Total Score"];
    $_SESSION["tchests"] = $query["TChests Unlocked"];
    $_SESSION["increase"] = array();
    $_SESSION["prev-salt"] = "";
    $_SESSION["salt"] = "";
    $query = "SELECT COUNT(*) FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "WHERE `Q-U`.`Question Number` LIKE '{$_SESSION["level"]}_' "
            .       "AND `Q-U`.`Time Answered` != '-1'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $_SESSION["advance-level"] = intval($query["COUNT(*)"]) >= $CONST["advance"];
    unset($user);
}

load_constants();

$_SESSION["prev-salt"] = $_SESSION["salt"];
$_SESSION["salt"] = sha1(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM));

$query = "SELECT `Disqualified` FROM `Contestants` WHERE `Username` = '{$_SESSION["username"]}'";
$query = mysqli_fetch_array(mysqli_query($db_connection, $query));
if (!isset($query["Disqualified"]) || $query["Disqualified"] == 1) {
    destroy_session();
    header("Location: ./index.php?msg=You%20have%20been%20disqualified...");
    die();
}
unset($query);

if (isset($from)) {
    if (checkFromVariable_Outside($from)) {
        header("Location: ./profile.php");
        die();
    } else if (checkFromVariable_Account($from) || checkFromVariable_Common($from)) {
        //Nothing to do 
    }
} else {
    header("Location: ./profile.php");
    die();
}

return;

