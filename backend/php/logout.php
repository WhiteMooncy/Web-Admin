<?php
session_start();
session_destroy();
header("Location: ../../frontend/templates/index.html");
exit();
