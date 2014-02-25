<?php

$from = "questionpage";
require_once "./support/check.php";

if (!isset($_POST["answer"])) {
	header("Location: ./profile.php");
	die();
}

?>


<!DOCTYPE html>
<html>
    <head>
        <title>Authorized Entry Only</title>
        <meta charset="UTF-8">
       
        <meta name="viewport" content="width=device-width">
    </head>
    <body>
    
     
         
        <div id="panel">
        
        <h2> Click on the submit button to submit the answer. </h2>
        <form method="POST" submit="question.php">
        	<input id="pass" type="hidden" name="pass" value="<?php echo $_SESSION["salt"]; ?>" />
	        <input id="answer" type="hidden" name="answer" value="<?php echo $_POST["answer"]; ?>" />
        	<input type="submit" />
        </form>
        
        </div>
        
        <script language="javascript">
         	document.getElementById("panel").style.display = "none";
                var p = prompt('Authorized Access Only.\n Enter Password: ','Enter the password');
                window.location.reload();
        </script>
        
    </body>
</html>