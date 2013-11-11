<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$from = "questionpage";

require './support/check.php';
require_once './support/dbcon.php';

/*function getScore($maxMarks, $solves, $reductionStep) {
    return $maxMarks;
}*/

function check() {
    global $_POST;
    if (!isset($_POST["answer"])) {
        return NULL;
    }
    global $db_connection;
    $query = "SELECT `Q`.*,`Q-U`.* FROM `Questions` AS `Q` "
            . "LEFT JOIN `Questions-{$_SESSION["username"]}` AS `Q-U` ON `Q`.`Question ID` = `Q-U`.`Question ID` "
            . "WHERE `Q`.`Question ID` = '{$_SESSION["question"]}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($query["Time Answered"] != "-1") {
        $_SESSION["question"] = "";
        header("Location: " . dirname($_SERVER["PHP_SELF"]) . "/profile.php");
        die();
    }
    $query["Attempts"] ++;
    if (!filter_var($_POST["answer"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[a-z0-9]+$/'))) || $query["Answer"] != $_POST["answer"]) {
        $result = "UPDATE `Questions-{$_SESSION["username"]}` "
                . "SET `Attempts` = '{$query["Attempts"]}' "
                . "WHERE `Question ID` = '{$_SESSION["question"]}' ";
        mysqli_query($db_connection, $result);
        return "Ooops! Wrong Answer! Keep Trying...";
    }
    $timeAnsw = intval((time() + 59) / 60);
    $timeOpen = $query["Time Opened"];

    $_SESSION["score-increase"] = intval($query["Max Score"]);
    $_SESSION["score"] += $_SESSION["score-increase"];

    $result = "UPDATE `Contestants` SET `Score` = '{$_SESSION["score"]}' WHERE `Username` = '{$_SESSION["username"]}';";
    mysqli_query($db_connection, $result);
    $result = "UPDATE `Questions-{$_SESSION["username"]}` "
            . "SET `Time Answered`='{$timeAnsw}', `Obtained Score`='{$_SESSION["score-increase"]}', `Attempts`='{$query["Attempts"]}' "
            . "WHERE `Question ID` = '{$_SESSION["question"]}';";
    mysqli_query($db_connection, $result);
    $query["Solves"]++;
    $query["Max Score"] -= $query["Reduction Step"];
    $result = "UPDATE `Questions` SET `Solves` = '{$query["Solves"]}', `Max Score`='{$query["Max Score"]}' "
        . "WHERE `Question ID` = '{$_SESSION["question"]}';";
    mysqli_query($db_connection, $result);
    $_SESSION["question"] = "";
    header("Location: " . dirname($_SERVER["PHP_SELF"]) . "/profile.php");
}

$wrong_msg = check();
global $_POST;

$query = "SELECT * FROM `Questions` AS `Q` "
        . "LEFT JOIN `Questions-{$_SESSION["username"]}` AS `Q-U` ON `Q-U`.`Question ID`=`Q`.`Question ID` "
        . "WHERE `Q`.`Question ID` = '{$_SESSION["question"]}';";
$question = mysqli_fetch_array(mysqli_query($db_connection, $query));
if (!isset($_GET) || empty($_GET["hint"])) {
    if ($question["Attempts"] >= $question["Least Attempts"] && !empty($question["Hint Link"])) {
        header("Location: " . dirname($_SERVER["PHP_SELF"]) . "/question.php?hint={$question["Hint Link"]}");
    }
}
?>


<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - Question</title>
        <link href="question.css" rel="stylesheet" type="text/css" />
        <link href="navbar.css" rel="stylesheet" type="text/css" />
    </head>

    <body>

        <nav class="cl-effect-9">
            <a href="./profile.php">
                <span>Profile</span>
                <span>Your homepage</span>
            </a>
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
            <a href="./logout.php">
                <span>Logout</span>
                <span>Is it getting too difficult?</span>
            </a>
        </nav>


        <div id="user-info">
            <h2 id="user"><?php echo($_SESSION['username']); ?></h2>
            <h2 id="level" style="background-color: <?php echo $question_colors["{$question["Difficulty"]}"]
                ?>"><?php echo $_SESSION['question']; ?></h2>
        </div>

        <div id="question-div" class="<?php 
            switch ($question["Text Picture"]) {
                case 1: echo "question-text"; break;
                case 2: echo "question-image"; break;
                case 3: echo "question-both"; break;
            }
        ?>">
            <?php
            if ((intval($question["Text Picture"]) & 1) == 1) {
                ?>
                <div id="question-text">
                    <h2><?php echo $question["Question Text"]; ?></h2>
                </div>
                <?php
            }
            if ((intval($question["Text Picture"]) & 2) == 2) {
                ?>
                <img src="./images/<?php echo $question["Picture"] ?>"/> 
                <?php
            }
            ?>
        </div>


        <div id="form-wrapper">
            <form method="POST" id="form-answer" action="question.php">
                <input id="ans" name="answer" placeholder="Your answer here..."/>
                <?php
                //Hint
                if ($question["Attempts"] >= $question["Least Attempts"] && !empty($question["Hint Page"])) {
                    echo "<!-- Hint : {$question["Hint Page"]} -->";
                }
                ?>
                <a href='javascript:;' onclick="document.getElementById('form-answer').submit();" class="btn">
                    <span class="btn-text">Submit</span> 
                    <span class="btn-expandable"><span class="btn-slide-text"><?php
                            $msg[0] = "Are you sure??";
                            $msg[1] = "May I lock it?";
                            $msg[2] = "Double check!!";
                            $msg[3] = "Easy, aint it?";
                            $msg[4] = "Very peculiar!";
                            $idx = rand(0, 4);
                            echo $msg[$idx];
                            ?></span>
                        <span class="btn-icon-right"><span></span></span></span>
                </a>
            </form>
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