<?php
session_start();
session_destroy();
header("Location: ../voter/login.php");
exit();
?>
