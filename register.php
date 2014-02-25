<?php

$from = "registerpage";
require './support/check.php';

function startsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function verify_anw_id($id) {
    include '../php/db.php';
//    global $dbc;
    $query = "SELECT COUNT(*) FROM `participants` WHERE `user_id` = '$id'; ";
    $res1 = mysqli_fetch_array(mysqli_query($dbc,$query));

    require_once "./support/dbcon.php";
    global $db_connection;
    $query = "SELECT COUNT(*) FROM `stud` WHERE `anw_id` = '$id'; ";
    $res2 = mysqli_fetch_array(mysqli_query($db_connection,$query));
    
    return intval($res1["COUNT(*)"]) == 1 || intval($res2["COUNT(*)"]) == 1;
}

function check() {
    global $_POST;
    global $CONST;
    if (!filter_var($_POST["usernamesignup"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[\w]{1,15}$/')))) {
        $error["msg"] = "Inappropriate username";
        $error["component"] = "username";
    }
    if (!filter_var($_POST["anweshasignup"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^ANW[I]?[\d]{4,6}$/'))) ||   
                 !verify_anw_id($_POST["anweshasignup"])) {
        $error["msg"] = "Inappropriate Anwesha ID.";
        $error["component"] = "anwesha";
    }
    if ($_POST["passwordsignup"] != $_POST["passwordsignup_confirm"]) {
        $error["msg"] = "Passwords dont match!!";
        $error["component"] = "password";
    }
    if (isset($error)) {
        return $error;
    }

    require_once './support/dbcon.php';
    global $db_connection;

    $query = "SELECT COUNT(*) FROM `Contestants` "
            . "WHERE `Anwesha ID` = '{$_POST["anweshasignup"]}'";
    $result = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($result["COUNT(*)"] != 0) {
        $error["msg"] = "You have already signed up! "
                . "Use password recovery if you have forgotten your password!";
        $error["component"] = "anwesha";
    }

    $query = "SELECT COUNT(*) FROM `Contestants` "
            . "WHERE `Username` = '{$_POST["usernamesignup"]}'";
    $result = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($result["COUNT(*)"] != 0) {
        $error["msg"] = "Username already taken! Please choose another!";
        $error["component"] = "username";
    }
    if (isset($error)) {
        return $error;
    }

    $user = $_POST["usernamesignup"];
    $anws = $_POST["anweshasignup"];
    $pass = $_POST["passwordsignup"];
    $cost = 10;

    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2y$%02d$", $cost) . $salt;
    $hash = crypt($pass, $salt);

    $query = "INSERT INTO `Contestants` (`Username` ,`Anwesha ID` ,`Hash`)
                    VALUES ('{$user}', '{$anws}', '{$hash}')";
    mysqli_query($db_connection, $query);

    $query = "INSERT INTO `ContestantsData`(`Username`) VALUES ('{$user}')";
    mysqli_query($db_connection, $query);

    $query = "CREATE TABLE `Questions-{$user}` ("
            . "`Question Number` varchar(2) NOT NULL, "
            . "`Question ID` varchar(3) NOT NULL, "
            . "`Hinted` int(11) DEFAULT '0', "
            . "`Time Opened` int(11) DEFAULT '-1', "
            . "`Time Answered` int(11) DEFAULT '-1', "
            . "`Obtained Score` int(11) NOT NULL DEFAULT '0', "
            . "`Attempts` int(11) DEFAULT '0', "
            . "PRIMARY KEY (`Question Number`), "
            . "UNIQUE KEY `Question Number` (`Question Number`), "
            . "UNIQUE KEY `Question ID` (`Question ID`))";
    $val = mysqli_query($db_connection, $query);

    $query = "";
    $buffer_size = $CONST["buffer"];
    for ($l = 1; $l <= $CONST["levels"]; $l++) {
        for ($q = 1; $q <= $CONST["questions"]; $q++) {
            $random = rand(1, $buffer_size);
            $query = "INSERT INTO `Questions-{$user}` (`Question Number`, `Question ID`) "
                    . "VALUES ('{$l}{$q}', '{$l}{$q}{$random}');";
            mysqli_query($db_connection, $query);
        }
    }
    unset($q);
    unset($l);
    return FALSE;
}

if (isset($_POST["usernamesignup"]) && isset($_POST["anweshasignup"]) &&
        isset($_POST["passwordsignup"]) && isset($_POST["passwordsignup_confirm"])) {
    $error_msg = check();
}
?>


<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link href="navbar.css" type="text/css" rel="stylesheet" />
        <link href="register.css" type="text/css" rel="stylesheet" />
        <title>NJATH - Anwesha 2k14 Registration</title>
    </head>
    <body>
        <nav class="cl-effect-9">
            <a href="index.php" >
                <span>Login</span>
                <span>Start the Awesome</span>
            </a>
            <a href="leaderboard.php">
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
            <a href="rules.php">
                <span>Rules</span>
                <span>The law of the Land!!!</span>
            </a>
        </nav>


        <div id="wrapper">
            <form id="register" action="register.php" method="POST" autocomplete="on">
                <h1> Sign up </h1>
<?php if (isset($error_msg["component"])) {
    ?>
                    <p id="error-msg">
    <?php
    echo $error_msg["msg"];
    ?>
                    </p>
                        <?php
                    }
                    ?>
                <p>
                    <label for="usernamesignup" class="uname" data-icon="u">Your username</label>
                    <input id="usernamesignup" name="usernamesignup" required="required"
                           type="text" placeholder="eg. thejoker69"
                           value="<?php if (isset($error_msg["component"]) && $error_msg["component"] != "username") echo $_POST["usernamesignup"]; ?>"
                           class="<?php if (isset($error_msg["component"]) && $error_msg["component"] == "username") echo 'error-component'; ?>"/>
                </p>
                <p>
                    <label for="anweshasignup" class="anwesha" data-icon="a">Anwesha ID</label>
                    <input id="anweshasignup" name="anweshasignup" required="required"
                           type="text" placeholder="eg. ANW000000"
                           value="<?php if (isset($error_msg["component"]) && $error_msg["component"] != "anwesha") echo $_POST["anweshasignup"]; ?>"
                           class="<?php if (isset($error_msg["component"]) && $error_msg["component"] == "anwesha") echo 'error-component'; ?>"/>
                </p>
                <p>
                    <label for="passwordsignup" class="youpasswd" data-icon="p">Your password </label>
                    <input id="passwordsignup" name="passwordsignup" required="required"
                           type="password" placeholder="eg. X8df!90EO"
                           class="<?php if (isset($error_msg["component"]) && $error_msg["component"] == "password") echo 'error-component'; ?>"/>
                </p>
                <p>
                    <label for="passwordsignup_confirm" class="youpasswd" data-icon="p">Please confirm your password </label>
                    <input id="passwordsignup_confirm" name="passwordsignup_confirm"
                           required="required" type="password" placeholder="eg. X8df!90EO"
                           class="<?php if (isset($error_msg["component"]) && $error_msg["component"] == "password") echo 'error-component'; ?>"/>
                </p>
                <p class="signin button">
                    <input type="submit" value="Sign up"/>
                </p>
                <p class="change_link">Already a member ?<a href="index.php" class="to_register"> Go and log in </a>
                </p>
            </form>
        </div>
<?php
if (isset($error_msg) && $error_msg != TRUE) {
    ?>
            <div id="done-display">
                <div>
                    <h2> Registration SUCCESSFUL!! </h2>
                    <p>  Click <a href="rules.php">here</a> to continue. </p>
                </div>
            </div>
    <?php
}
?>
    </body>
</html>
