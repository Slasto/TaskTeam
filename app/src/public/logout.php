<?php
session_start();
unset($_SESSION["username"]);
unset($_SESSION['logged_in']);
unset($_SESSION['user_id']);
session_destroy();
header("location: /");
exit;
