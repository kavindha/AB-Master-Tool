<?php
include_once( "config.php" ); 
include_once( "constants.php" );
require_once("classes/DBI.php");
include_once("classes/sys.php");
include_once("classes/AB_Tests.php");

$dbi = new DBI();
$dbi->connect( SCF_DB_HOST, SCF_DB_USER, SCF_DB_PASSWORD, SCF_DB_NAME );
$dbi->log_query_execution_time = false;
$dbi->auto_retries_on_connection_lost = 5;
$dbi->connection_lost_retry_interval = 1000000;
$status=SYS::request("status");
$tests = AB_Tests::get_all_tests($status);
$test_var = AB_Tests::get_all_tests_by_variation();
$ajax_status = [ "status" => false ];
$cmd = SYS::request( "cmd" );
$adjustments = AB_Tests::get_adjustments();
