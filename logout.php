<?php 
session_start();
session_unset();
session_destroy();
session_start();
session_regenerate_id();
$_SESSION['SUCCESS_MSG'] = "You have successfully logged out.";
header("location: login.php");
exit();

?>