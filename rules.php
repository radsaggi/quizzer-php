<?php
require_once './support/dbcon.php';
$from = "leaderboard";
require_once './support/check.php';
?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - Celesta 2k13 - RULES</title>
        <link href="rules.css" rel="stylesheet" type="text/css" />
        <link href="navbar.css" rel="stylesheet" type="text/css" />
        <style type="text/css">

        </style>
    </head>
    <body>
        <nav class="cl-effect-9">
            <?php
            if (isset($_SESSION["username"])) {
                ?>
                <a href="profile.php" >
                    <span>Questions</span>
                    <span>Continue the HUNT!</span>
                </a>
                <?php
            } else {
                ?>
                <a href="index.php" >
                    <span>Login</span>
                    <span>Start the Awesome</span>
                </a>
                <a href="register.php">
                    <span>Register</span>
                    <span>New to the challenge?</span>
                </a>
                <?php
            }
            ?>
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
        </nav>
        <div id="rules-content">
            <h2>Rules for the contest</h2>

            <ol id="rules-list">
                <li><p>NJATH is an online treasure-hunt contest. You will be given hints, and you have to decipher the solution. <span>There is only one correct answer for each question!</span> So be careful with spellings!!</p> </li>
                <li> <p>Look out for clues and hints anywhere and everywhere including the url of page, page source and all the details provided in the question. You can use Google, Wikipedia or anything else for help.</p> </li>
                <li> <p>Only answers as alpha-numeric characters in lower case (a-z, 0-9) are allowed.
                        Eg. if your answer is “I Love NJATH”, then it should be written as “ilovenjath”. </p> </li>
                <li> <p><span>Use only your IITP email id for registration.</span></p> </li>
                <li> <p>Discussion forum can be used for getting hints. Users providing answers or direct link in discussion forum will be disqualified.</p> </li>                
                <li> <p>Each player starts off with a score of 100. The questions are categorized by the difficulty level. 
                        You will be awarded scores for correct answers. The faster you solve it the more you score!!!</p> </li>
                <li> <p>You will have to pay score costs for opening questions so choose wisely! </p> </li>
                <li> <p>Your profile page will contain a list of all questions, each highlighted differently - unopened in blue, opened in yellow and solved in green.</p></li>
                <li> <p>Each question link also contains information about scoring system. </p>
                    <p><span>Unopened Questions</span> - The score cost of opening the question.</p>
                    <p><span>Opened Questions</span> - The maximum you can score on that question if answered correctly in time. </p>
                    <p><span>Solved Questions</span> - The actual score you were given for solving that question. </p></li>
                <li> <p>The one who finishes all the levels first is the winner or else the one who is at the top of the leaderboard at the finish is the winner. In case more than one persons are on the same level at the top, then winner will be decided by their completion times.</p> </li>
                <li> <p>Event starts from 12:00 hrs Saturday i.e 9th Nov 13 and finishes at 22:00 hrs Sunday i.e 10th Nov 13.</p> </li>
            </ol>
        </div>
    </body>
</html>