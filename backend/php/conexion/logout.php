<?php
session_start();
session_unset();
session_destroy();
header("Location: ../../../frontend/templates/index.php");
exit;
?>
