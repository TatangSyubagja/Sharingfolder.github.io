<?php
@session_start();

session_destroy();

header("location: /file_manager/login.php");
?>