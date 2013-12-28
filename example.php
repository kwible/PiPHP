<?php

$pi = require('autoload.php');
$pi->open('ADDRESS', 'USERNAME', 'PASSWORD');

// Get all pids and view them in the browser

echo $pi->getPIDS();



