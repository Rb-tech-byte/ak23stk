<?php
session_start();
session_unset();
session_destroy();
header('Location: op_auth_signin.php');
exit;
