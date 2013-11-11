<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require './support/dbcon.php';

function calcTotal($user) {
    global $db_connection;
    $query = "SELECT `Q-U` . * , `Q`.`Cost` "
            . "FROM `Questions-{$user}` AS `Q-U` "
            . "LEFT JOIN `Questions` AS `Q` ON `Q`.`Question ID` = `Q-U`.`Question ID` "
            . "WHERE `Q-U`.`Time Opened` != -1";
    $query = mysqli_query($db_connection, $query);
    if (!$query) {
        echo $user;
        return 0;
    }
    $sum = 100;
    while ($result = mysqli_fetch_array($query)) {
        $sum = $sum + $result["Obtained Score"] - $result["Cost"];
    }
    return $sum;
}
?>


<html>
    <body>
        <div id="table">
            <?php
            //put your own user_name and password here

            $query = "SELECT * FROM `Contestants` ORDER BY `Score` DESC";
            $result = mysqli_query($db_connection, $query);
            $count = 1;
            while ($row = mysqli_fetch_array($result)) {
                //$user = isset($_SESSION["username"]) && $row["Username"] == $_SESSION["username"];
                $row["Rechecked Score"] = calcTotal($row["Username"]);
                if ($count == 1) {
                    ?>
                    <table id="data-table">
                        <thead>
                        <th>Sl. No.</th>
                        <th>Username</th>
                        <th>Year</th>
                        <th>Score</th>
                        <th>Score - 2</th>
                        </thead>
                        <?php
                    }
                    ?>
                    <tr<?php /*if ($user) echo 'class="user"'*/ ?>>
                        <td><?php echo($count); ?></td>
                        <td><?php echo($row["Username"]); ?></td>
                        <td><?php echo($row["Year"]); ?></td>
                        <td><?php echo($row["Score"]); ?></td>
                        <td><?php echo($row["Rechecked Score"]); ?></td>
                    </tr>
                    <?php
                    
                    //$query = 
                    
                    
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
