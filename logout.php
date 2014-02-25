<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$from = "logoutpage";

require './support/check.php';

destroy_session();
header("Location: ./index.php?msg=Logged%20out...");
die();
?>