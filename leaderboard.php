<?php
$from = "leaderboard";
require './support/check.php';
require_once './support/dbcon.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - Anwesha 2k14 Leaderboard</title>
        <link href="leaderboard.css" rel="stylesheet" type="text/css" />
        <link href="navbar.css" rel="stylesheet" type="text/css"/>
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


        <div id="hn">Leader Board</div>

        <div id="table">
            <table class="data-table">
                <thead>
                    <th>Sl. No.</th>
                    <th>Username</th>
                    <th>College</th>
                    <th>Score</th>
                </thead>
                <?php
                include "../php/functions.php";
                $query = "SELECT * FROM `Contestants` AS `C` "
                        . "LEFT JOIN `ContestantsData` AS `CD` ON `C`.`Username` = `CD`.`Username` "
                        . "WHERE `C`.`Disqualified` = 0 "
                        . "ORDER BY `Total Score` DESC LIMIT 0, 20";
                $result = mysqli_query($db_connection, $query);
                
                $count = 1;
                $shown = 0;
                $user = "";
                while ($row = mysqli_fetch_array($result)) {
                    $ans_user = null;
                    if (strpos($row["Anwesha ID"], "I") === 0) {
                        $anw_user["college"] = "IIT Patna";
                    } else {
                        $anw_user = giv_participant($row["Anwesha ID"]);
                    }
                    $row["College"] = $anw_user["college"];
                    $user = isset($_SESSION["username"]) && $row["Username"] == $_SESSION["username"];
                    if ($user) {
                        $shown = 1;
                    }
                    ?>
                    <tr <?php if ($user) echo 'class="user"' ?>>
                        <td><?php echo($count); ?></td>
                        <td><?php echo($row["Username"]); ?></td>
                        <td><?php echo($row["College"]); ?></td>
                        <td><?php echo($row["Total Score"]); ?></td>
                    </tr>
                    <?php
                    $count++;
                }
                ?>
            </table>
            <?php
            if (!$shown && isset($_SESSION["username"])) {
                ?>
                <table class="data-table">
                    <?php
                    $query = "SELECT * FROM `Contestants` AS `C` "
                            . "LEFT JOIN `ContestantsData` AS `CD` ON `C`.`Username` = `CD`.`Username` "
                            . "WHERE `C`.`Username`='{$_SESSION["username"]}' ";
                    $result = mysqli_fetch_array(mysqli_query($db_connection, $query));
                    ?>
                    <tr<?php if ($user) echo 'class="user"' ?>>
                        <td><?php echo($count); ?></td>
                        <td><?php echo($row["Username"]); ?></td>
                        <td><?php echo($row["College"]); ?></td>
                        <td><?php echo($row["Total Score"]); ?></td>
                    </tr>
                </table>
                <?php
            }
            ?>
        </div>
    </body>
</html>
