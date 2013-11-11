
<?php

function startsWith($haystack, $needle) {
    return $needle === "" || strpos($haystack, $needle) === 0;
}

function endsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function getYear($mail) {
    $mail_end = substr($mail, strpos($mail, ".") + 1);
    if (endsWith($mail_end, "10")) {
        $year = 4;
    } else if (endswith($mail_end, "11")) {
        $year = 3;
    } else if (endsWith($mail_end, "12")) {
        if (startsWith($mail_end, "mc") || startsWith($mail_end, "mt") ||
                startsWith($mail_end, "nt")) {
            $year = 5;
        } else {
            $year = 2;
        }
    } else if (endsWith($mail_end, "13")) {
        if (startsWith($mail_end, "mt")) {
            $year = 5;
        } else {
            $year = 1;
        }
    }
    return $year;
}

function check() {
    if (!filter_var($_POST["usernamesignup"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[\w]+$/')))) {
        $error["msg"] = "Inappropriate username";
        $error["component"] = "username";
    }
    if (!filter_var($_POST["emailsignup"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[a-z]+\.(((cs|ee|me)1(0|1|2|3))|(c(e|h)13)|((mc|mt|nt)12)|(mt(cs|ee|mc|mt|nt)13))$/')))) {
        $error["msg"] = "Inappropriate email id";
        $error["component"] = "email";
    }
    if ($_POST["passwordsignup"] != $_POST["passwordsignup_confirm"]) {
        $error["msg"] = "Passwords dont match!!";
        $error["component"] = "password";
    }

    require_once './support/dbcon.php';
    global $db_connection;

    $query = "SELECT COUNT(*) FROM `Contestants` WHERE `Email` = '{$_POST["emailsignup"]}'";
    $result = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($result["COUNT(*)"] != 0) {
        $error["msg"] = "You have already signed up! Use password recovery if you have forgotten your password!";
        $error["component"] = "email";
    }

    $query = "SELECT COUNT(*) FROM `Contestants` WHERE `Username` = '{$_POST["usernamesignup"]}'";
    $result = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if ($result["COUNT(*)"] != 0) {
        $error["msg"] = "Username already taken! Please choose another!";
        $error["component"] = "username";
    }
    if (isset($error)) {
        return $error;
    }

    $user = $_POST["usernamesignup"];
    $mail = $_POST["emailsignup"];
    $year = getYear($mail);
    $pass = $_POST["passwordsignup"];
    $cost = 10;

    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2y$%02d$", $cost) . $salt;
    $hash = crypt($pass, $salt);

    $query = "INSERT INTO `Contestants` (`Username` ,`Email` ,`Hash` ,`Year`)
                    VALUES ('{$user}', '{$mail}', '{$hash}', '{$year}')";
    mysqli_query($db_connection, $query);

    $query = "CREATE TABLE `Questions-{$user}` ("
            . "`Pseudo ID` varchar(2) NOT NULL, "
            . "`Question ID` varchar(2) NOT NULL, "
            . "`Time Opened` int(11) DEFAULT '-1', "
            . "`Time Answered` int(11) DEFAULT '-1', "
            . "`Obtained Score` int(11) NOT NULL DEFAULT '0', "
            . "`Attempts` int(11) DEFAULT '0', "
            . "PRIMARY KEY (`Pseudo ID`), "
            . "UNIQUE KEY `Pseudo ID` (`Pseudo ID`), "
            . "UNIQUE KEY `Question ID` (`Question ID`))";
    $val = mysqli_query($db_connection, $query);

    $query = "";
    $ar = array(1, 2, 3, 4, 5, 6);
    for ($d = 'A'; $d <= 'C'; $d++) {
        shuffle($ar);
        for ($q = 1; $q <= 6; $q++) {
            $query = "INSERT INTO `Questions-{$user}` (`Pseudo ID`, `Question ID`) "
                    . "VALUES ('{$d}{$q}', '{$d}{$ar[$q - 1]}');";
            mysqli_query($db_connection, $query);
        }
    }
    unset($q);
    unset($d);
    return FALSE;
}

if (isset($_POST["usernamesignup"]) && isset($_POST["emailsignup"]) &&
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
        <title>NJATH - Celesta 2k13 Registration</title>
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
                <p id="email-input"> 
                    <label for="emailsignup" class="youmail" data-icon="e" > Your college email</label>
                    <input id="emailsignup" name="emailsignup" required="required" 
                           type="text" placeholder="eg. batman.cs12"
                           value="<?php if (isset($error_msg["component"]) && $error_msg["component"] != "username") echo $_POST["emailsignup"]; ?>"
                           class="<?php if (isset($error_msg["component"]) && $error_msg["component"] == "email") echo 'error-component'; ?>"/>
                    <span> @ iitp.ac.in </span>
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
