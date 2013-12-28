<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', getcwd() . DS);

define('DRIVER', ROOT . 'SSH' . DS);

require('SSH/Net/SSH2.php');
require('advanced.class.php');
require('piphp.class.php');

return new PiPHP;