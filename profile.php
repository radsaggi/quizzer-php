<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$from = "profilepage";
require './support/check.php';
require_once './support/dbcon.php';

function check() {
    global $_POST;
    if (!isset($_POST["question"]) || !filter_var($_POST["question"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[A-C][1-6]$/')))) {
        return NULL;
    }
    global $db_connection;
    $query = "SELECT `Q-U`.*, `Q`.`Cost`,COUNT(*) FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "LEFT JOIN `Questions` AS `Q` ON `Q`.`Question ID` = `Q-U`.`Question ID` "
            . "WHERE `Pseudo ID` = '{$_POST["question"]}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($query["COUNT(*)"] != 1) {
        return NULL;
    }

    $timeOpen = $query["Time Opened"];
    if ($query["Time Opened"] == -1) {
        if ($_SESSION["score"] < $query["Cost"]) {
            return "You dont have enough score to open this question";
        } else {
            $_SESSION["score"] = intval($_SESSION["score"]) - intval($query["Cost"]);
            $timeOpen = intval((time() + 59) / 60);
        }
    }

    $_SESSION["question"] = $query["Question ID"];
    $result = "UPDATE `Questions-{$_SESSION["username"]}` "
            . "SET `Time Opened` = '{$timeOpen}' "
            . "WHERE `Question ID` = '{$_SESSION["question"]}'; ";
    mysqli_query($db_connection, $result);
    $result = "UPDATE `Contestants` SET `Score` = '{$_SESSION["score"]}' WHERE `Username` = '{$_SESSION["username"]}'; ";
    mysqli_query($db_connection, $result);
    header("Location: " . dirname($_SERVER["PHP_SELF"]) . "/question.php");
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
            <a href="./logout.php">
                <span>Logout</span>
                <span>Is it getting too difficult?</span>
            </a>
        </nav>


        <div id="user-info">
            <h2 id="user"><?php echo($_SESSION['username']); ?></h2>
            <h2 id="score"><?php echo($_SESSION['score']); ?></h2>
            <?php if ($_SESSION["score-increase"] != 0) {
                ?>
                <h3 id="score-increase">+<?php echo $_SESSION["score-increase"]; ?></h3>
                <?php
                $_SESSION["score-increase"] = 0;
            }
            ?>
        </div>


        <div id="button-wrapper">
            <?php
            for ($d = 'A'; $d <= 'C'; $d++) {
                $query = "SELECT `Q-U`.*, `Q`.`Cost`, `Q`.`Max Score` FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
                        . "LEFT JOIN `Questions` AS `Q` ON `Q-U`.`Question ID` = `Q`.`Question ID` "
                        . "WHERE `Pseudo ID` LIKE '{$d}%' ORDER BY `Pseudo ID`";
                $query = mysqli_query($db_connection, $query);

                for ($i = 0; $i < 6; $i++) {
                    $questions[$i] = mysqli_fetch_array($query);
                    if ($questions[$i]["Time Opened"] == -1) {
                        $questions[$i]["State"] = "unopened";
                    } else if ($questions[$i]["Time Answered"] == -1) {
                        $questions[$i]["State"] = "opened";
                    } else {
                        $questions[$i]["State"] = "answered";
                    }
                }
                ?>
                <h3 class="type-label"><?php
                    switch ($d) {
                        case 'A' : echo "Easy";
                            break;
                        case 'B' : echo "Medium";
                            break;
                        case 'C' : echo "Hard";
                            break;
                    }
                    ?></h3>
                <ul>
                    <?php
                    for ($i = 0; $i < 6; $i++) {
                        ?>
                        <li>
                            <form id="form-<?php echo $d . $i; ?>" action="profile.php" method="POST">
                                <input type="hidden" name="question" value="<?php echo $questions[$i]["Pseudo ID"]; ?>" />
                            </form>
                            <a href="javascript:;" onclick="document.getElementById('form-<?php echo $d . $i; ?>').submit();" 
                               class="a-btn <?php echo $questions[$i]["State"]; ?>">
                                <span class="a-btn-slide-text"><?php
                                    switch ($questions[$i]["State"]) {
                                        case "unopened": echo $questions[$i]["Cost"];
                                            break;
                                        case "opened": echo $questions[$i]["Max Score"];
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
                <?php
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
