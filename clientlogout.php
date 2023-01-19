<?php
// Starting session
session_start();
// remove all session variables
if(isset($_SESSION["loginpage"]) && $_SESSION["loginpage"]=='marelli')
{
	session_unset();
    // Destroying session
    session_destroy();
    header('Location: marelli-login.php');
}
else
{
	session_unset();
    // Destroying session
    session_destroy();
    header('Location: index.php');
}

?>