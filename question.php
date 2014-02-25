<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$from = "questionpage";

require_once './support/check.php';
require_once './support/dbcon.php';

function startsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function check_hint($hint) {
    if ($hint != $_SESSION["prev-salt"] || $_SESSION["prev-salt"] === "") {
        return NULL;
    }

    global $db_connection;
    global $_SESSION;

    $query = "SELECT `Hints` FROM `ContestantsData` AS `C` "
            . "WHERE `C`.`Username` = '{$_SESSION["username"]}'; ";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $hints = intval($query["Hints"]);
    if ($hints <= 0) {
        return NULL;
    }
    $query = "SELECT `Hinted`, `Hint` FROM `Questions` AS `Q` "
            . "LEFT JOIN `Questions-{$_SESSION["username"]}` AS `Q-U` ON `Q`.`Question ID` = `Q-U`.`Question ID` "
            . "WHERE `Q-U`.`Question ID` = '{$_SESSION["question"]}'; ";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ((intval($query["Hinted"]) == 1) || strlen($query["Hint"]) == 0) {
        return NULL;
    }

    $hints--;

    $query = "UPDATE `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "SET `Hinted` = '1' "
            . "WHERE `Q-U`.`Question ID` = '{$_SESSION["question"]}'; ";
    mysqli_query($db_connection, $query);

    $query = "UPDATE `ContestantsData` AS `C` "
            . "SET `Hints` = '{$hints}' "
            . "WHERE `C`.`Username` = '{$_SESSION["username"]}'; ";
    mysqli_query($db_connection, $query);

    return NULL;
}

function check_answer($ans) {
    $quesFor = $_SESSION["question"];
    $browserOfuser = NULL;
    if (startsWith($quesFor, "76")) {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strlen($_SERVER["HTTP_USER_AGENT"]) > 0) {
            return "Your browser is not allowed to submit the answer!";
        }
    }
    if (startsWith($quesFor, "75")) {
        if (!isset($_COOKIE["user"]) || $_COOKIE["user"] != "admin") {
            return "You must be admin to submit this answer!";
        }
    }

    global $db_connection;
    global $CONST;

    $query = "SELECT `Q`.*,`Q-U`.* FROM `Questions` AS `Q` "
            . "LEFT JOIN `Questions-{$_SESSION["username"]}` AS `Q-U` ON `Q`.`Question ID` = `Q-U`.`Question ID` "
            . "LEFT JOIN `QuestionSolves` AS `S` ON `Q`.`Question ID` = `S`.`Question ID` "
            . "WHERE `Q`.`Question ID` = '{$_SESSION["question"]}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));

    if ($query["Time Answered"] != "-1") {
        $_SESSION["question"] = "";
        header("Location: ./profile.php");
        die();
    }

    $query["Attempts"] ++;
    if (intval($query["Hinted"]) == 1 && isset($query["Answer Hinted"]) && strlen($query["Answer Hinted"]) > 0) {
        $query["Check Answer"] = $query["Answer Hinted"];
    } else {
        $query["Check Answer"] = $query["Answer Regular"];
    }
    if (!filter_var($ans, FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[a-z0-9]+$/')))) {
        return "Ooops! Wrong Answer! Keep Trying...";
    }
    if ($CONST["tchest-keyword"] == $ans && !check_tchest(1)) {
        return "Try visiting " . $CONST["njath-home"] . "tchest.php?q=" . create_tchest_string(1, $_SESSION["salt"]);
    }
    if ($query["Check Answer"] != $ans) {
        $result = "UPDATE `Questions-{$_SESSION["username"]}` "
                . "SET `Attempts` = '{$query["Attempts"]}' "
                . "WHERE `Question ID` = '{$_SESSION["question"]}' ";
        mysqli_query($db_connection, $result);
        return "Ooops! Wrong Answer! Keep Trying...";
    }

    if (startsWith($_SESSION["question"], "72") && (!isset($_POST["pass"]) || $_POST["pass"] != $_SESSION["prev-salt"])) {
        require "./questioneWZ.php";
        die();
    }

    $timeAnsw = intval((time() + 59) / 60);

    if (intval($query["Hinted"]) == 0) {
        $incr = intval($CONST["question-score"]);
        push_increase("Question Answered", $incr);
    } else {
        $incr = intval($CONST["question-hinted-score"]);
        push_increase("Question Answered with hint", $incr);
    }
    if ($_SESSION["advance-level"]) {
    	push_increase("Bonus Question", $CONST["bonus-quest"]);
        $incr += $CONST["bonus-quest"];
    }
    $tchests = get_tchest_count();
    if ($tchests > 0) {
        push_increase("Treasure Chest Bonus!", get_tchest_count() * $CONST["tchest-bonus"], false);
    }
    sync_scores();

    $result = "UPDATE `Questions-{$_SESSION["username"]}` "
            . "SET `Time Answered`='{$timeAnsw}', `Obtained Score`='{$incr}', `Attempts`='{$query["Attempts"]}' "
            . "WHERE `Question ID` = '{$_SESSION["question"]}';";
    mysqli_query($db_connection, $result);

    $query = "SELECT COUNT(*) FROM `Questions-{$_SESSION["username"]}` AS `Q-U` "
            . "WHERE `Q-U`.`Question Number` LIKE '{$_SESSION["level"]}_'"
            . "AND `Q-U`.`Time Answered` != '-1' ";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if (intval($query["COUNT(*)"]) >= $CONST["advance"]) {
        $_SESSION["advance-level"] = TRUE;
    }

    $query = "SELECT * FROM `QuestionSolves` AS `Q-U` "
            . "WHERE `Q-U`.`Question ID` = '{$_SESSION["question"]}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    $query["Solves"] ++;
    if ($query["First Solve"] == -1) {
        $query["First Solve"] = $timeAnsw;
    }
    $result = "UPDATE `QuestionSolves` "
            . "SET `Solves` = '{$query["Solves"]}', `First Solve`='{$query["First Solve"]}' "
            . "WHERE `Question ID` = '{$_SESSION["question"]}';";
    mysqli_query($db_connection, $result);
    $_SESSION["question"] = "";
    header("Location: ./profile.php");
    die();
}

function check() {
    global $_POST;
    if (isset($_POST["answer"])) {
        return check_answer($_POST["answer"]);
    } else if (isset($_POST["hint"]) && filter_var($_POST["hint"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => "/^[a-z\d]+/")))) {
        return check_hint($_POST["hint"]);
    } else {
        return NULL;
    }
}

$wrong_msg = check();
unset($_POST);

if (startsWith($_SESSION["question"], "75")) {
    setcookie("user", "non-admin");
}

$query = "SELECT * FROM `Questions` AS `Q` "
        . "LEFT JOIN `Questions-{$_SESSION["username"]}` AS `Q-U` ON `Q-U`.`Question ID`=`Q`.`Question ID` "
        . "WHERE `Q`.`Question ID` = '{$_SESSION["question"]}';";
$question = mysqli_fetch_array(mysqli_query($db_connection, $query));
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
            <h2 id="level"><?php echo substr_replace($question["Question Number"], ".", 1, 0); ?></h2> 
        </div>

        <div id="question-div" class="<?php
        switch ($question["Type"]) {
            case 1: echo "question-text";
                break;
            case 2: echo "question-image";
                break;
            case 3: echo "question-both";
                break;
        }
        ?>"><?php
                 if ((intval($question["Type"]) & 1) == 1) {
                     ?>
                <div id="question-text">
                    <h2><?php
                        if (startsWith($_SESSION["question"], "73")) {
                            echo "Download the file to solve the question";
                        } else {
                            echo $question["Question Text"];
                        }
                        ?></h2>
                    <?php
                    if (startsWith($_SESSION["question"], "73")) {
                        ?>
                        <a href="./images/q_win32.exe">Windows 32bit</a>
                        <a href="./images/q_win64.exe">Windows 64bit</a>
                        <a href="./images/q_lin32">Linux 32bit</a>
                        <a href="./images/q_lin64">Linux 64bit</a>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            if ((intval($question["Type"]) & 2) == 2) {
            	if (!check_tchest(2)) {
            		?>
            		<img alt="Question Image" src="./images/image.php?q=<?php echo create_tchest_string(2, $_SESSION["salt"]); ?>"/> 
            		<?php
            	}
            	
            	if (!check_tchest(3)) {
            		?>
	                <img alt="Question Image" id="cropped" src="./images/image_exp.php?<?php echo "q=" . create_tchest_string(3, $_SESSION["salt"]) 
	                	. "&amp;img=" . $question["Question Picture"]; ?>"/> 
        	        <?php
            	} else {
                	?>
	                <img src="./images/<?php echo $question["Question Picture"]; ?>"/> 
        	        <?php
                }
            }
            ?>
        </div>


        <div id="form-wrapper">
            <?php
            $query = "SELECT * FROM `ContestantsData` WHERE `Username` = '{$_SESSION["username"]}';";
            $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
            if (intval($question["Hinted"]) == 0 && intval($query["Hints"]) > 0 && isset($question["Hint"]) && strlen($question["Hint"]) > 0) {
                ?>

                <form method="POST" id="form-hint" action="question.php">
                    <input id="hint" type="hidden" name="hint" value="<?php echo $_SESSION["salt"]; ?>"/>
                    <a href='javascript:;' onclick="document.getElementById('form-hint').submit();" id="hint-btn">
                        Show a hint
                        <span><?php echo intval($query["Hints"]); ?> hints left</span>
                    </a>
                </form>
                <?php
            }
            ?>

            <form method="POST" onkeypress="return event.keyCode != 13;" id="form-answer" action="question.php">
                <?php if (startsWith($_SESSION["question"], "71")) { ?>
                    <script language="javascript">
                        function unecape(string) {
                            string = "% 23 ^ 13 % 56(0x34, 0x37, 0x38, 0x41, 0x42)";
                            string = "+char+" == "+pass+" + " " ? string + pass : string;
                            char = document.getElementById("password").value;
                            return string;
                        }
                        function check() {
                            var userInput = document.getElementById("ans").value;
                            var pass = unescape('%34%32');
                            if (userInput == pass) {
                                document.getElementById('form-answer').submit();
                            } else {
                                alert("Wrong password.");
                            }
                        }
                    </script>
                    <input id="ans" name="answer" placeholder="Your answer here..." autocomplete="off"/>
                <?php } else if (startsWith($_SESSION["question"], "74")) {
                    ?>
                    <select name="answer" id="ans"  action="question.php">
                        <option value="1" selected>Pasty Cline</option>
                        <option value="2">Lauren Oliver</option>
                        <option value="3">Sehun</option>   
                        <option value="4">Kai</option>
                    </select>
                <?php } else { ?>
                    <input id="ans" name="answer" placeholder="Your answer here..." autocomplete="off"/>
                <?php }
                ?>
                <a href='javascript:;' onclick="<?php
                if (startsWith($_SESSION["question"], "71")) { 
                    echo "check();";
                } else {
                    echo "document.getElementById('form-answer').submit();";
                }
                ?>" id="submit-btn">
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
        if (intval($question["Attempts"]) >= intval($CONST["tchest-tries"]) && !check_tchest(0)) {
            echo '<!-- ';
            echo "Try visiting " . $CONST["njath-home"] . "tchest.php?q=" . create_tchest_string(0, $_SESSION["salt"]);
            echo ' -->';
        }
        ?>
        
        <?php
        if (intval($question["Hinted"]) == 1) {
            echo '<!-- ';
            echo " Hint: " . $question["Hint"];
            echo ' -->';
        }
        ?>


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