<?php
session_start();
session_unset();
session_destroy();
header('Location: index.php?message=You+have+been+logged+out');
exit;
?>