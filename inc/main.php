<?php

session_start();

$conn = getConn();
$categories = getCategories($conn);

