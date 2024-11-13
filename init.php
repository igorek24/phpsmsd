<?php

declare(strict_types=1);

$root = pathinfo($_SERVER['SCRIPT_FILENAME']);
if (!defined("DS")) define("DS", DIRECTORY_SEPARATOR);
if (!defined('APP_ROOT')) define('APP_ROOT', realpath(dirname(__FILE__)) . DS);
if (!defined('APP_SRC_ROOT')) define('APP_SRC_ROOT', APP_ROOT.'src' . DS);
