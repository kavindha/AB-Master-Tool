<?php
include_once( "config.php" ); 
require_once("DBI.php");
include_once("AB_Tests.php");
$dbi = new DBI();
$dbi->connect( SCF_DB_HOST, SCF_DB_USER, SCF_DB_PASSWORD, SCF_DB_NAME );
$dbi->log_query_execution_time = false;
$dbi->auto_retries_on_connection_lost = 5;
$dbi->connection_lost_retry_interval = 1000000;
$tests = AB_Tests::get_all_tests();
$test_var = AB_Tests::get_all_tests_by_variation();
