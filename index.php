<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
function checkLogin() {
    if (!filter_var($_POST["login"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[\w]+$/')))) {
        $error["msg"] = "Incorrect username";
        $error["type"] = "username";
        $error["error"] = TRUE;
        return $error;
    }

    $user = $_POST["login"];
    $pass = $_POST["password"];
    global $db_connection;
    $query = "SELECT * FROM `Contestants` WHERE `Username` = '{$user}'";
    $query = mysqli_fetch_array(mysqli_query($db_connection, $query));
    if (!isset($query["Hash"])) {
        $error["type"] = "username";
        $error["error"] = TRUE;
        return $error;
    }
    
    $hash = crypt($pass, $query["Hash"]);

    if ($hash != $query["Hash"]) {
        $error["type"] = "passwords";
        $error["error"] = TRUE;
        return $error;
    }

    $error["username"] = $user;
    $error["error"] = FALSE;
    return $error;
}

if (isset($_POST["login"]) && isset($_POST["password"])) {
    require_once './support/dbcon.php';
    $error = checkLogin();
    if (!$error["error"]) {
        $user = $error["username"];
        unset($error);
    }
}
$from = "homepage";

require './support/check.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - ANWESHA 2k14 - HOME</title>
        <link href="index.css" rel="stylesheet" type="text/css" />
        <link href="navbar.css" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
        <script type="text/javascript" src="js/g.js"></script>
        <script type="text/javascript" src="js/sliderman.1.3.7.js"></script>
        <link rel="stylesheet" type="text/css" href="sliderman.css" />
    </head>
    <body>

        <nav class="cl-effect-9">
            <a href="register.php">
                <span>Register</span>
                <span>New to the challenge?</span>
            </a>
            <a href="leaderboard.php" >
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


        <div class="login">
            <h2> NJATH Login : </h2>
            <form method="post" action="" class="loginform">
                <p>
                    <label for="login">Username:</label>
                    <input type="text" name="login" id="login" placeholder="Username" autofocus="true"/>
                </p>

                <p>
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" placeholder="Password"/>
                </p>

                <p class="login-submit">
                    <button type="submit" class="login-button">Login</button>
                </p>

                <?php
                if (isset($error) && $error["error"]) {
                    if ($error["type"] === "username") {
                        ?>
                        <p class = "error-display">Wrong username. New user? Register <a href = "register.php">here</a>.</p>
                        <?php
                    } else if ($error["type"] === "password") {
                        ?>
                        <p class = "error-display">Wrong password. Forgot Password? Send a mail to <a href = "mailto:ashutosh.ee12@iitp.ac.in">ashutosh.ee12</a>.</p>
                        <?php
                    }
                }
                ?>
            </form>
        </div>
        <!--login form completed-->



        <!--image slider started-->
        <div id="wrapper">

            <div id="examples_outer">

                <div id="slider_container_2">

                    <div id="SliderName_2" class="SliderName_2">
                        <img src="images/1.jpg" width="700" height="450" alt="Demo2 first" title="Demo2 first" usemap="#img1map" />
                        <map name="img1map">
                            <area href="#img1map-area1" shape="rect" coords="100,100,200,200" />
                            <area href="#img1map-area2" shape="rect" coords="300,100,400,200" />
                        </map>
                        <div class="SliderName_2Description">IIT PATNA PRESENTS :: <strong> NJATH</strong></div>
                        <img src="images/2.jpg" width="700" height="450" alt="Demo2 second" title="Demo2 second" />
                        <div class="SliderName_2Description"><strong>NOT JUST ANOTHER TREASURE HUNT </strong></div>
                        <img src="images/3.jpg" width="700" height="450" alt="Demo2 third" title="Demo2 third" />
                        <div class="SliderName_2Description"><strong>COMPLETE LEVELS TO ADVANCE</strong> </div>
                        <img src="images/4.jpg" width="700" height="450" alt="Demo2 fourth" title="Demo2 fourth" />
                        <div class="SliderName_2Description"><strong>CHALLENGE YOURSELF TO THE FULLEST</strong></div>
                    </div>
                    <div class="c"></div>
                    <div id="SliderNameNavigation_2"></div>
                    <div class="c"></div>

                    <script type="text/javascript">
                        effectsDemo2 = 'rain,stairs';
                        var demoSlider_2 = Sliderman.slider({container: 'SliderName_2', width: 700, height: 450, effects: effectsDemo2,
                            display: {
                                autoplay: 3000,
                                loading: {background: '#000000', opacity: 0.5, image: 'images/loading.gif'},
                                buttons: {hide: false, opacity: 1, prev: {className: 'SliderNamePrev_2', label: ''}, next: {className: 'SliderNameNext_2', label: ''}},
                                description: {hide: false, background: '#000000', opacity: 0.4, height: 50, position: 'bottom'},
                                navigation: {container: 'SliderNameNavigation_2', label: '<img src="images/clear.gif" />'}
                            }
                        });
                    </script>

                    <div class="c"></div>
                </div>
                <div class="c"></div>
            </div>

            <div class="c"></div>
        </div>
        <!--image slider finished-->


        

        <?php
        if (isset($_GET["msg"])) {
            ?>

            <div id="complete-hide">
                <div id="message-display">
                    <h2><?php echo $_GET["msg"]; ?></h2>
                </div>
            </div>

            <?php
            unset($_GET);
        }
        ?>
    </body>

</html>