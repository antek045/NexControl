<?php
session_start();
session_destroy();
header("Location: ../public/html/index.html");
