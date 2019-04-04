<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Load the routes.
 */
require CONFIG_DIR . '/routes.php';

return Amber\Framework\Application::getInstance();
