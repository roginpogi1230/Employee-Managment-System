<?php
session_start();
 
// Unset ng lahat ng session variables
$_SESSION = array();
 
// Destroy ang session
session_destroy();
 
// I-redirect sa login page
header("location: login.php");
exit;
?>