<?php

if (!file_exists("./support/check.php")) {
    header("Location: ./index.php");
    die();
}

if (!isset($db_username) && !isset($db_password)) {
    $file = $_SERVER["DOCUMENT_ROOT"] . "/../njath.anwesha2014.properties";
    if (!file_exists($file)) {
        die("Database cannot be opened. Credentials missing....");
    }

    $handle = fopen($file, 'r');
    $cred = fscanf($handle, "%s %s");
    $db_username = $cred[0];
    $db_password = $cred[1];

    fclose($handle);
}

if (!isset($db_connection)) {

    if (!function_exists("db_disconnect")) {

        function db_disconnect() {
            if (isset($databaseMain)) {
                mysqli_close($databaseMain);
                unset($databaseMain);
            }
        }

    }

    global $db_connection;
    $db_connection = mysqli_connect("localhost", $db_username, $db_password, "anwesha_njath");
    // Check connection
    if (mysqli_connect_errno()) {
        throw new Exception("Failed to connect to MySQL: " . mysqli_connect_error());
        unset($db_connection);
    }
}
?>