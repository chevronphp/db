<?php

require_once "vendor/autoload.php";

/**
 * in order to run the PDO tests a MySQL DB is required.
 *
 * DO NOT TRUST MY CODE.
 *
 * USE A SANDBOX DB
 *
 * USE A SANDBOX USER.
 *
 */

define("TEST_DB_DSN", "mysql:host=127.0.0.1;port=3306;dbname=chevron_tests;charset=utf8");
define("TEST_DB_USERNAME", "root");
define("TEST_DB_PASSWORD", "");
