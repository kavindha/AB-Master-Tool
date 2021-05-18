<?php
     include("init.php");      
    if (SYS::is_ajax_request()) {       
        switch( $cmd ) {            
            case "archive_test":
                 $input_data = $_POST;               
                AB_Tests::archive_test($input_data['id']);
                $ajax_status["status"] = true;
                break;
            case "add_test_info":
                $input_data = $_POST;
                //AB_Tests::add_test_info($input_data);
                $ajax_status["status"] = true;
                break;
            case "add_adjustment":
                $input_data = $_POST;
                AB_Tests::add_adjustment($input_data);
                $ajax_status["status"] = true;
                break;
            case "gut_check":
                $input_data = $_POST;
                AB_Tests::add_gut_check($input_data);
                $ajax_status["status"] = true;
                break;
            case "sample_size":
                $input_data = $_POST;
                AB_Tests::add_sample_size($input_data);
                $ajax_status["status"] = true;
                break;
            case "restart_test":
                $id = $_POST['exp_id'];
                AB_Tests::restart_test($id);
                $ajax_status["status"] = true;
                break;
            case "traffic_distribution":
                $input_data = $_POST;
                $id = $input_data["exp_id"];
                unset($input_data["exp_id"]); 
                AB_Tests::set_traffic_distribution($input_data,$id);
                $ajax_status["status"] = true;
                break;
            case "create_experiment":
                $input_data = $_POST;            
                $ajax_status["id"] = AB_Tests::create_experiment($input_data);
                $ajax_status["status"] = true;
                break;
            case "create_variations":
                $input_data = $_POST;         
                $ajax_status["status"] = true;
                $ajax_status["id"] = AB_Tests::create_variations($input_data["variations"], $input_data["id"]);
                break;
            case "create_events":
                $input_data = $_POST;         
                $ajax_status["status"] = true;
                $ajax_status["id"] = AB_Tests::create_event($input_data["events"], $input_data["id"]);
                break;
            case "add_test_info_new":
                $input_data = $_POST;
                AB_Tests::add_test_info_new($input_data);
                $ajax_status["status"] = true;
                break;
            case "daily_conversions":
                $input_data = $_POST;
                $ajax_status["status"] = true;
                $ajax_status["daily_conv"] = AB_Tests::get_daily_conversions($input_data["exp_id"], $input_data["start_date"], $input_data["end_date"]);
                break;
            default:
                $ajax_status["status"] = false;
        }
    }
    header( "Content-Type: application/json" );
    echo json_encode( $ajax_status );
    die();
?>