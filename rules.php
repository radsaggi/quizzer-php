<?php
require_once './support/dbcon.php';
$from = "leaderboard";
require_once './support/check.php';
?>

<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - Anwesha 2k14 - RULES</title>
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
                <a href="./profile.php" >
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
            <a href="http://2014.anwesha.info">
                <span>Anwesha 2014</span>
                <span>The Anwesha website...</span>
            </a>
            <a href="http://www.iitp.ac.in">
                <span>IIT Patna</span>
                <span>All about our college</span>
            </a>
        </nav>
        <div id="rules-content">
            <h2>Rules for the contest</h2>

            <ol id="rules-list">
                <li> <p> NJATH is an online treasure-hunt contest conducted during Anwesha. You will be given questions, and you have to decipher the solution. Be careful with spellings and answers to questions. </p> </li> 
                <li> <p> Look out for clues and hints anywhere and everywhere including the URL of the page, the page source and all the details provided in the question. You can use Google, Wikipedia or anything else for help. </p> </li> 
                <li> <p> Only answers as alpha-numeric characters in lower case (a-z, 0-9) are allowed. Eg. Suppose your answer is “I Love NJATH”, then write it as “ilovenjath”. In case of dates like 31st Jan 2014, write 31012014. </p> </li> 
                <li> <p> There are treasure chests hidden throughout the hunt, they provide additional points and bonus multipliers. So watch out for them!  </p> </li> 
                <li> <p> Remember treasure chests can be everywhere. So checkout every possible corner. </p> <img src="./images/tchest.png" alt="Treasure Chest" height="100" width="100" style="display: block;margin: auto;"/></li> 
                <li> <p> Discussion forum can be used for getting extra hints.  </p> </li> 
                <li> <p> Users providing answers or direct link in any form through any medium will be disqualified. </p> </li> 
                <li> <p> There are 7 levels in the hunt. Each level contains 8 questions of which at least  6 need to be answered to advance to the next level. </p> </li> 
                <li> <p> Your profile page will contain a list of all questions at the current level, each highlighted differently - unopened in blue, opened in yellow and solved in green. </p> </li> 
                <li> <p> Each question tag also contains information about scoring system: </p>
                    <ul>
                        <li> <p> Unopened Questions - The score cost of opening the question.</p>
                            <img src="./images/unopened.png" alt="Treasure Chest" height="100" width="400" style="display: block;margin: auto;"/>
                        </li>
                        <li> <p> Opened Questions - The maximum you can score on that question.</p>
                            <img src="./images/opened.png" alt="Treasure Chest" height="100" width="400" style="display: block;margin: auto;"/>
                        </li>
                        <li> <p> Solved Questions - The actual score you were given for solving that question.</p>
                            <img src="./images/answered.png" alt="Treasure Chest" height="100" width="400" style="display: block;margin: auto;"/>
                        </li>
                    </ul> 
                </li> 
                <li> <p> The 2 remaining questions serve as bonus questions. Bonus questions provide more points than ordinary questions of that level.  </p> </li> 
                <li> <p> Bonus questions if opened have to be solved in order to move to the next level, otherwise a heavy penalty will be imposed on the score. </p> </li> 
                <li> <p> Each player starts off with a score of 100, in each level. You will be awarded scores for correct answers.  </p> </li> 
                <li> <p> You will have to pay score costs for opening questions, which will be deducted from level score and not from total score so choose wisely! The level score at no point of time can fall below zero! </p> </li> 
                <li> <p> There are a limited number of hints to each question. There will be additional 5 trump card hints. You may use it at any point of the game. </p> </li> 
                <li> <p> The player who has the maximum points at the end of 24hrs is the winner. 
                        </ol>
                        </div>
                        </body>
                        </html>