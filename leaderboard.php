
<?php
require_once './support/dbcon.php';
$from = "leaderboard";
require_once './support/check.php';
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>NJATH - Celesta 2k13 Leaderboard</title>
        <link href="leaderboard.css" rel="stylesheet" type="text/css" />
        <link href="navbar.css" rel="stylesheet" type="text/css"/>
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


        <div id="hn">Leader Board</div>

        <div id="table">
            <?php
            //put your own user_name and password here

            $query = "SELECT * FROM `Contestants` ORDER BY `Score` DESC";
            $result = mysqli_query($db_connection, $query);
            $count = 1;
            while ($row = mysqli_fetch_array($result)) {
                $user = isset($_SESSION["username"]) && $row["Username"] == $_SESSION["username"];
                if ($count == 1) {
                    ?>
                    <table id="data-table">
                        <thead>
                            <th>Sl. No.</th>
                            <th>Username</th>
                            <th>Year</th>
                            <th>Score</th>
                        </thead>
                        <?php
                    }
                    ?>
                    <tr<?php if ($user) echo 'class="user"'?>>
                        <td><?php echo($count); ?></td>
                        <td><?php echo($row["Username"]); ?></td>
                        <td><?php echo($row["Year"]); ?></td>
                        <td><?php echo($row["Score"]); ?></td>
                    </tr>
                    <?php
                    $count++;
                }
                if ($count > 1) {
                    ?>
                </table>
                <?php
            }
            ?>
        </div>
    </body>
</html>
