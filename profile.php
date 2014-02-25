<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$from = "profilepage";

require './support/check.php';
require_once './support/dbcon.php';

function check_question($question) {
    global $db_connection;
    global $CONST;
    $question = substr($question, 1);
    $query = "SELECT `Q-U`.*,COUNT(*) FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "LEFT JOIN `Questions` AS `Q` ON `Q`.`Question ID` = `Q-U`.`Question ID` "
            . "WHERE `Q-U`.`Question Number` = '{$_SESSION["level"]}{$question}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($query["COUNT(*)"] != 1) {
        return NULL;
    }

    $timeOpen = $query["Time Opened"];
    if ($query["Time Opened"] == -1) {
        if ($_SESSION["level-score"] < $CONST["question-cost"]) {
            return "You dont have enough level-score to open this question";
        } else {
            push_increase("Question Cost", -intval($CONST["question-cost"]));
            $timeOpen = intval((time() + 59) / 60);
        }
    }

    $_SESSION["question"] = $query["Question ID"];
    $result = "UPDATE `Questions-{$_SESSION["username"]}` "
            . "SET `Time Opened` = '{$timeOpen}' "
            . "WHERE `Question ID` = '{$_SESSION["question"]}'; ";
    mysqli_query($db_connection, $result);
    $result = "UPDATE `ContestantsData` "
            . "SET `Total Score` = '{$_SESSION["total-score"]}', "
            . "`Level Score` = '{$_SESSION["level-score"]}' "
            . "WHERE `Username` = '{$_SESSION["username"]}'; ";
    mysqli_query($db_connection, $result);
    header("Location: ./question.php");
    die();
}

function check_level($advance) {
    global $CONST;

    if (!$_SESSION["advance-level"] || $advance != $_SESSION["prev-salt"] || $_SESSION["prev-salt"] === "") {
        return NULL;
    }

    global $db_connection;

    $query = "SELECT COUNT(*) FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "WHERE `Q-U`.`Question Number` LIKE '{$_SESSION["level"]}_'; ";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if (intval($query["COUNT(*)"]) < 6) {
        return NULL;
    }

    $query = "SELECT COUNT(*) FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "WHERE `Q-U`.`Time Opened` != '-1' AND `Q-U`.`Time Answered` = '-1' "
            . "AND `Q-U`.`Question Number` LIKE '{$_SESSION["level"]}_'; ";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $query_count = intval($query["COUNT(*)"]);
    if ($query_count > 0) {
        push_increase("Unsolved Question Penalty", -$query_count * $CONST["question-penalty"]);
    }

    $_SESSION["question"] = "";
    $_SESSION["advance-level"] = FALSE;
    $_SESSION["level"] ++;
    $_SESSION["level-score"] = 0;
    load_constants();
    push_increase("Level Advanced", $CONST["advance-bonus"]);
    sync_scores();

    $query = "UPDATE `ContestantsData` "
            . "SET `Level` = '{$_SESSION["level"]}' "
            . "WHERE `Username` = '{$_SESSION["username"]}';";
    mysqli_query($db_connection, $query);

    return "Level Advanced";
}

function check() {
    global $_POST;
    global $CONST;
    if (isset($_POST["question"]) && filter_var($_POST["question"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => "/^{$_SESSION["level"]}[1-{$CONST["questions"]}]$/")))) {
        return check_question($_POST["question"]);
    } else if (isset($_POST["advance"]) && filter_var($_POST["advance"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => "/^[a-z\d]+$/i")))) {
        return check_level($_POST["advance"]);
    } else {
        return NULL;
    }
}

$wrong_msg = check();
unset($_POST);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - <?php echo $_SESSION["username"] ?> Profile</title>
        <link href="profile.css" rel="stylesheet" type="text/css" />
        <link href="navbar.css" rel="stylesheet" type="text/css" />
    </head>

    <body>

        <nav class="cl-effect-9">
            <a href="./rules.php">
                <span>Rules</span>
                <span>The law of the Land!!!</span>
            </a>
            <a href="./leaderboard.php" >
                <span>Leaderboard</span>
                <span>View the Leaderboard</span>
            </a>
            <a href="http://www.facebook.com/iit.njath/app_202980683107053">
                <span>Forum</span>
                <span>The Discussion Forum</span>
            </a>
            <a href="http://www.iitp.ac.in">
                <span>IIT Patna</span>
                <span>All about our college</span>
            </a>
            <a href="http://2014.anwesha.info">
                <span>Anwesha 2014</span>
                <span>The Anwesha website...</span>
            </a>
            <a href="./logout.php">
                <span>Logout</span>
                <span>Is it getting too difficult?</span>
            </a>
        </nav>


        <div id="user-info">
            <h2 id="user"><?php echo($_SESSION['username']); ?></h2>
            <ul id="tchest">
                <?php
                $n = get_tchest_count();
                for ($i = 0; $i < $n; $i++) {
                    ?>
                <li><img src="./images/tchest.png" alt="Treasure Chest" /></li>
                    <?php
                }
                ?>
            </ul>
            <ul id="increase">
                <?php
                if (isset($_SESSION["increase"])) {
                    $n = count($_SESSION["increase"]);
                    for ($i = 0; $i < $n; $i++) {
                        $incr = $_SESSION["increase"][$i];
                        ?>
                        <li><?php echo $incr["text"]; ?><span><?php echo $incr["value"]; ?></span></li>
                        <?php
                    }
                    unset($n);
                    unset($i);
                    unset($incr);
                    $_SESSION["increase"] = array();
                }
                ?>
            </ul>
            <h2 id="level-score">Level Score : <span><?php echo($_SESSION['level-score']); ?></span></h2>
            <h2 id="total-score">Total Score : <span><?php echo($_SESSION['total-score']); ?></span></h2>

        </div>


        <div id="button-wrapper">
            <?php 
            	if (intval($_SESSION["level"]) > 7) {
            	?>
            	<h3 class="type-label">NJATH COMPLETED!</h3>
            	<?php
                } else {
                ?>
            <div id="question-div">
                <?php
                $query = "SELECT `Q-U`.* FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
                        . "LEFT JOIN `Questions` AS `Q` ON `Q-U`.`Question ID` = `Q`.`Question ID` "
                        . "WHERE `Q-U`.`Question Number` LIKE '{$_SESSION["level"]}_' ORDER BY `Q-U`.`Question Number`";
                $query = mysqli_query($db_connection, $query);

                for ($i = 0; $i < $CONST["questions"]; $i++) {
                    $questions[$i] = mysqli_fetch_array($query);
                    $questions[$i]["Score"] = $CONST["question-score"];
                    $questions[$i]["Cost"] = $CONST["question-cost"];
                    if ($questions[$i]["Time Opened"] == -1) {
                        $questions[$i]["State"] = "unopened";
                    } else if ($questions[$i]["Time Answered"] == -1) {
                        $questions[$i]["State"] = "opened";
                    } else {
                        $questions[$i]["State"] = "answered";
                    }
                    if ($_SESSION["advance-level"]) {
                        $questions[$i]["Score"] = $CONST["bonus-quest"];
                    }
                }
                ?>
                <h3 class="type-label"><?php
                    echo "Level " . $_SESSION["level"] . "";
                    ?></h3>
                <ul>
                    <?php
                    $n = $CONST["questions"];
                    if (intval($_SESSION["level"]) == 7) {
                    	$n = $CONST["advance"];
                    }
                    for ($i = 0; $i < $n; $i++) {
                        ?>
                        <li>
                            <form id="form-<?php echo $i; ?>" action="profile.php" method="POST">
                                <input type="hidden" name="question" value="<?php echo $questions[$i]["Question Number"]; ?>" />
                            </form>
                            <a href="javascript:;" onclick="document.getElementById('form-<?php echo $i; ?>').submit();" 
                               class="a-btn <?php echo $questions[$i]["State"]; ?>">
                                <span class="a-btn-slide-text"><?php
                                    switch ($questions[$i]["State"]) {
                                        case "unopened": echo $questions[$i]["Cost"];
                                            break;
                                        case "opened": echo $questions[$i]["Score"];
                                            break;
                                        case "answered": echo $questions[$i]["Obtained Score"];
                                            break;
                                    }
                                    ?></span>
                                <img src="./images/<?php
                                switch ($questions[$i]["State"]) {
                                    case "unopened": echo "quest2.png";
                                        break;
                                    case "opened": echo "exclaim.png";
                                        break;
                                    case "answered": echo "tick.png";
                                        break;
                                }
                                ?>" alt="<?php
                                     switch ($questions[$i]["State"]) {
                                         case "unopened": echo $questions[$i]["Cost"];
                                             break;
                                         case "opened": echo $questions[$i]["Score"];
                                             break;
                                         case "answered": echo $questions[$i]["Obtained Score"];
                                             break;
                                     }
                                     ?>"/>
                                <span class="a-btn-text">
                                    <small><?php echo $questions[$i]["State"]; ?></small>
                                    Question <?php echo $i + 1; ?>
                                </span> 
                                <span class="a-btn-icon-right"><span></span></span>
                                <span class="a-btn-slide-icon"></span>
                            </a> </li>					
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <?php
            if ($_SESSION["advance-level"]) {
                ?>

                <div id="advance-div">
                    <a href="javascript:;" onclick="document.getElementById('form-advance').submit();" id="advance">
                        <img class="advance-symbol" src="./images/star.png">
                        <span class="advance-text">Advance Level</span> 
                        <span class="advance-slide-text">Move on to the next level...</span>
                    </a>
                    <form id="form-advance" action="profile.php" method="POST">
                        <input type="hidden" name="advance" value="<?php echo $_SESSION["salt"] ?>" />
                    </form>
                </div>
                <?php
            }
            }
            ?>

        </div>

        <?php
        if (isset($wrong_msg)) {
            ?>

            <div id="complete-hide">
                <div id="message-display">
                    <h2><?php echo $wrong_msg; ?></h2>
                </div>
            </div>

            <?php
            unset($wrong_msg);
        }
        ?>

    </body>
</html>  
