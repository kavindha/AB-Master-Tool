<?php  

    class AB_Tests {

        public function get_all_tests($status){
            global $dbi;
            if($status == 0){
                $query = sprintf("SELECT *, e.id as exp_id FROM experiment e LEFT JOIN %s a ON e.id = a.experiment_id ",TBL_AB_TESTS);
            } else {
                $query = sprintf("SELECT *, e.id as exp_id FROM experiment e LEFT JOIN %s a ON e.id = a.experiment_id WHERE a.status = %s",TBL_AB_TESTS,$status);
            }
            return $dbi->query_to_multi_array( $query );
        }
        
        public function get_all_tests_by_variation(){
            global $dbi;
            $query = sprintf( "SELECT e.id experiment_id, e.name experiment_name, e.`key` experiment_key, e.created_on experiment_created_on, v.id variation_id, v.caption variation_caption, v.`key` variation_key, v.traffic_distribution traffic_distribution, v.instances instance_count, GROUP_CONCAT(d.category_key SEPARATOR 0x00 ) category_keys, GROUP_CONCAT(d.category_caption SEPARATOR 0x00 ) category_captions, GROUP_CONCAT(d.event_keys SEPARATOR 0x00 ) event_keys, GROUP_CONCAT(d.event_captions SEPARATOR 0x00 ) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x00 ) trigger_count FROM %s v INNER JOIN %s e ON v.experiment_id = e.id LEFT OUTER JOIN (SELECT d.variation_id, d.category_key, d.category_caption, GROUP_CONCAT(d.event_key SEPARATOR 0x01 ) event_keys, GROUP_CONCAT(d.event_caption SEPARATOR 0x01) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x01) trigger_count FROM (SELECT c.* FROM (SELECT v.id variation_id, IFNULL(c.`key`, 'uncategorized') category_key, IFNULL(c.caption, 'Uncategorized') category_caption, ev.`key` event_key, ev.caption event_caption, SUM(IF(l.id IS NULL, 0, 1)) trigger_count FROM %s e RIGHT OUTER JOIN %s v ON v.experiment_id = e.id RIGHT OUTER JOIN %s ev ON ev.experiment_id = e.id LEFT OUTER JOIN %s c ON ev.event_category_id = c.id LEFT OUTER JOIN %s i ON i.variation_id = v.id LEFT OUTER JOIN %s l ON l.event_id = ev.id AND l.instance_id = i.id  GROUP BY v.id, c.id, ev.id) c GROUP BY c.variation_id, c.category_key, c.event_key) d GROUP BY d.variation_id, d.category_key) d ON v.id = d.variation_id  GROUP BY v.id", DB_TBL_VARIATION, DB_TBL_EXPERIMENT, DB_TBL_EXPERIMENT, DB_TBL_VARIATION, DB_TBL_EVENT, DB_TBL_EVENT_CATEGORY, DB_TBL_INSTANCE, DB_TBL_EVENT_LOG);
            $results = $dbi->query_to_multi_array( $query );
            $query = sprintf("SELECT * FROM experiment e");
            $experiments =   $dbi->query_to_multi_array( $query );
            $tests=[];
            foreach($experiments as $experiment){
                $index= 0;
                foreach($results as $result){
                    if($experiment["name"] == $result["experiment_name"] ){
                        $tests[$experiment["name"]][$index] =$result;
                        $index++;
                    }
                }              
            }
            $result=[];
            if ( $results ) {

                $field_list = [ "category_keys", "category_captions", "event_keys", "event_captions", "trigger_count" ];
                $categories = [];
                foreach($tests as $test){
                    
                foreach ( $test as $index => &$variation ) {                 
                    foreach ( $field_list as $field ) $variation[ $field ] = explode( "\x00", $variation[ $field ] );

                    if ( $index == 0 ) {

                        $experiment = array_intersect_key( $variation, array_fill_keys( [ "experiment_id", "experiment_name", "experiment_key", "experiment_created_on" ], "" ) );

                    }
                   
                    $variation = array_diff_key( $variation, $experiment );                   
                    $variation["events"] = [];                  
                    foreach ( $variation["category_keys"] as $_index => $_key ) {

                        $event_keys = explode( "\x01", $variation["event_keys"][ $_index ] );
                        $event_captions = explode( "\x01", $variation["event_captions"][ $_index ] );
                        $trigger_count = explode( "\x01", $variation["trigger_count"][ $_index ] );

                        if ( $index == 0 ) {

                            $categories[ $_key ] = [
                                "caption" => $variation["category_captions"][ $_index ],
                                "events" => array_combine( $event_keys, $event_captions ),
                            ];

                        }

                        foreach ( $event_keys as $_event_key_index => $_event_key ) {

                            $variation["events"][ $_key . "_" . $_event_key ] = $trigger_count[ $_event_key_index ];

                        }

                    }                   
                    unset( $_index, $_key, $_event_key_index, $_event_key );
                    foreach ( $field_list as $field ) unset( $variation[ $field ] );                    
                                     
                    }
                    $result[$experiment["experiment_name"]] =[ "experiment" => $experiment, "variations" => $test, "categories" => $categories ];   
                    unset($categories);             
                }                      
                return $result;
            } else return false;
        }

        public function get__tests_by_id($id){
            global $dbi;
            $query = sprintf("SELECT e.id experiment_id, e.name experiment_name, e.`key` experiment_key, e.created_on experiment_created_on, v.id variation_id, v.caption variation_caption, v.`key` variation_key, v.traffic_distribution traffic_distribution, v.instances instance_count, GROUP_CONCAT(d.category_key SEPARATOR 0x00 ) category_keys, GROUP_CONCAT(d.category_caption SEPARATOR 0x00 ) category_captions, GROUP_CONCAT(d.event_keys SEPARATOR 0x00 ) event_keys, GROUP_CONCAT(d.event_captions SEPARATOR 0x00 ) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x00 ) trigger_count FROM variation v INNER JOIN experiment e ON v.experiment_id = e.id LEFT OUTER JOIN (SELECT d.variation_id, d.category_key, d.category_caption, GROUP_CONCAT(d.event_key SEPARATOR 0x01 ) event_keys, GROUP_CONCAT(d.event_caption SEPARATOR 0x01) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x01) trigger_count FROM (SELECT c.* FROM (SELECT v.id variation_id, IFNULL(c.`key`, 'uncategorized') category_key, IFNULL(c.caption, 'Uncategorized') category_caption, ev.`key` event_key, ev.caption event_caption, SUM(IF(l.id IS NULL, 0, 1)) trigger_count FROM experiment e RIGHT OUTER JOIN variation v ON v.experiment_id = e.id RIGHT OUTER JOIN event ev ON ev.experiment_id = e.id LEFT OUTER JOIN event_category c ON ev.event_category_id = c.id LEFT OUTER JOIN instance i ON i.variation_id = v.id LEFT OUTER JOIN event_log l ON l.event_id = ev.id AND l.instance_id = i.id WHERE e.`key`= 'payment_page' GROUP BY v.id, c.id, ev.id) c GROUP BY c.variation_id, c.category_key, c.event_key) d GROUP BY d.variation_id, d.category_key) d ON v.id = d.variation_id WHERE e.id= %s GROUP BY v.id ",$id);           
            return $dbi->query_to_multi_array( $query );
        }

        public function archive_test($id){
            global $dbi;
            $dbi->update(TBL_AB_TESTS,[ "status" => "2" ], "id = " . intval( $id ));
            return ( $dbi->connection->affected_rows > 0 );
        }

        public function get_experiment_ids(){
            global $dbi;
            $query=sprintf("SELECT * FROM %s",TBL_AB_EXPERIMENTS);
            return $dbi->query_to_multi_array($query);
        }

        public function add_test_info($data){
            global $dbi;
            $data = [
                "experiment_id" => $data['experiment_id'],
                "element" => $data["element"],
                "description" => $data["decription"],
                "details" => $data["details"],
                "justification" => $data["justification"],                
                "jira_id" => $data["jira_id"],
                "start_date" => $data["start_date"],
                "traffic_allocation" => $data["traffic_allocation"],
                "objective" => $data["objective"],
                "force_link" => $data["force_link"],
                "status" => "1"
            ];

           return $dbi->insert( TBL_AB_TESTS, $data );
          
        }

        public function add_test_info_new($data){
            global $dbi;
            $data = [
                "experiment_id" => $data['experiment_id'],
                "element" => $data["element"],
                "description" => $data["decription"],
                "details" => $data["details"],
                "justification" => $data["justification"],                
                "jira_id" => $data["jira_id"],
                "start_date" => date("Y-m-d H:i:s"),
                "traffic_allocation" => $data["traffic_allocation"],
                "objective" => $data["objective"],
                "force_link" => $data["force_link"],
                "status" => "1"
            ];

           return $dbi->insert( TBL_AB_TESTS, $data );
          
        }

        public function add_gut_check($data){
            global $dbi;
            $dbi->update(TBL_AB_TESTS,[ "gut_check_3" => $data['GC3D'], "gut_check_14" => $data['GC14D']], "id = " . intval($data["id"] ));
            return ( $dbi->connection->affected_rows > 0 );
        }

        public function add_sample_size($data){
            global $dbi;
            $dbi->update(TBL_AB_TESTS,[ "sample_size" => $data['sample_size']], "id = " . intval($data["id"] ));
            return ( $dbi->connection->affected_rows > 0 );
        }

        public function add_adjustment($data){
            global $dbi;
            $converions = self::get__tests_by_id($data["exp_id"]);     
            $data= [
                "experiment_id" => $data['exp_id'],
                "date" => date("Y-m-d H:i:s"),
                "adjustment" => $data["adjustment"],
                "jira_id" => $data["jira_id"],
                "other_details" => $data["details"],                
                "traffic_allocation" => $data["traffic_allocation"],
                "current_conversions" => serialize( $converions)                
            ];
            $dbi->insert(TBL_AB_ADJUSTMENTS, $data);
        }

        public function get_adjustments(){
            global $dbi;
            $query = sprintf("SELECT * FROM %s", TBL_AB_ADJUSTMENTS);
            return $dbi->query_to_multi_array($query);
        }

        public function get_experiment_report( $experiment_key ) {

            global $dbi;

            $sql = sprintf( "SELECT e.id experiment_id, e.name experiment_name, e.`key` experiment_key, e.created_on experiment_created_on, v.id variation_id, v.caption variation_caption, v.`key` variation_key, v.traffic_distribution traffic_distribution, v.instances instance_count, GROUP_CONCAT(d.category_key SEPARATOR 0x00 ) category_keys, GROUP_CONCAT(d.category_caption SEPARATOR 0x00 ) category_captions, GROUP_CONCAT(d.event_keys SEPARATOR 0x00 ) event_keys, GROUP_CONCAT(d.event_captions SEPARATOR 0x00 ) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x00 ) trigger_count FROM %s v INNER JOIN %s e ON v.experiment_id = e.id LEFT OUTER JOIN (SELECT d.variation_id, d.category_key, d.category_caption, GROUP_CONCAT(d.event_key SEPARATOR 0x01 ) event_keys, GROUP_CONCAT(d.event_caption SEPARATOR 0x01) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x01) trigger_count FROM (SELECT c.* FROM (SELECT v.id variation_id, IFNULL(c.`key`, 'uncategorized') category_key, IFNULL(c.caption, 'Uncategorized') category_caption, ev.`key` event_key, ev.caption event_caption, SUM(IF(l.id IS NULL, 0, 1)) trigger_count FROM %s e RIGHT OUTER JOIN %s v ON v.experiment_id = e.id RIGHT OUTER JOIN %s ev ON ev.experiment_id = e.id LEFT OUTER JOIN %s c ON ev.event_category_id = c.id LEFT OUTER JOIN %s i ON i.variation_id = v.id LEFT OUTER JOIN %s l ON l.event_id = ev.id AND l.instance_id = i.id WHERE e.`key`= '%s' GROUP BY v.id, c.id, ev.id) c GROUP BY c.variation_id, c.category_key, c.event_key) d GROUP BY d.variation_id, d.category_key) d ON v.id = d.variation_id WHERE e.`key`= '%s' GROUP BY v.id", DB_TBL_VARIATION, DB_TBL_EXPERIMENT, DB_TBL_EXPERIMENT, DB_TBL_VARIATION, DB_TBL_EVENT, DB_TBL_EVENT_CATEGORY, DB_TBL_INSTANCE, DB_TBL_EVENT_LOG, $dbi->escape( $experiment_key ), $dbi->escape( $experiment_key ) );
            var_dump($sql);die;
            $results = $dbi->query_to_multi_array( $sql );
            
            if ( $results ) {

                $field_list = [ "category_keys", "category_captions", "event_keys", "event_captions", "trigger_count" ];
                $categories = [];

                foreach ( $results as $index => &$variation ) {                                       
                    foreach ( $field_list as $field ) $variation[ $field ] = explode( "\x00", $variation[ $field ] );

                    if ( $index == 0 ) {

                        $experiment = array_intersect_key( $variation, array_fill_keys( [ "experiment_id", "experiment_name", "experiment_key", "experiment_created_on" ], "" ) );

                    }
                    
                    $variation = array_diff_key( $variation, $experiment );

                    $variation["events"] = [];
                    foreach ( $variation["category_keys"] as $_index => $_key ) {

                        $event_keys = explode( "\x01", $variation["event_keys"][ $_index ] );
                        $event_captions = explode( "\x01", $variation["event_captions"][ $_index ] );
                        $trigger_count = explode( "\x01", $variation["trigger_count"][ $_index ] );

                        if ( $index == 0 ) {

                            $categories[ $_key ] = [
                                "caption" => $variation["category_captions"][ $_index ],
                                "events" => array_combine( $event_keys, $event_captions ),
                            ];

                        }
                        
                        foreach ( $event_keys as $_event_key_index => $_event_key ) {

                            $variation["events"][ $_key . "_" . $_event_key ] = $trigger_count[ $_event_key_index ];

                        }
                        var_dump($results);
                    }
                    unset( $_index, $_key, $_event_key_index, $_event_key );
                    foreach ( $field_list as $field ) unset( $variation[ $field ] );

                }
                
                return [ "experiment" => $experiment, "variations" => $results, "categories" => $categories ];

            } else return false;

        }

        public function restart_test($id) {
            global $dbi;
            $dbi->update(DB_TBL_VARIATION,[ "instances" => 0],"experiment_id = " . $id);
        }

        public function set_traffic_distribution($variations, $id) {
            global $dbi;
            foreach($variations as $key => $variation){
                $key = str_replace("_"," ",$key);
                $dbi->update(DB_TBL_VARIATION,[ "traffic_distribution" =>$variation],"experiment_id = " . $id." AND caption ='".$key."'");
            }
          
        }

        public function create_experiment($data) {
            // global $dbi;
            // $data= [
            //     "name" => $data['experiment_name'],
            //     "created_on" => date("Y-m-d H:i:s"),
            //     "key" => $data["key"],                            
            //     "traffic_distribution" => $data["traffic_distribution"],                          
            // ];
            // $dbi->insert(DB_TBL_EXPERIMENT, $data);
            // return $dbi->connection->insert_id;
            return 48;
        }

        public function create_variations($variations,$id) {
            global $dbi;
            $date = date("Y-m-d H:i:s");
            foreach($variations as $variation){
                $data= [
                    "experiment_id" => $id,
                    "created_on" => $date,
                    "caption" => $variation["caption"],    
                    "key" => $variation["key"],                            
                    "traffic_distribution" => $variation["traffic_distribution"],                          
                ];
                // $dbi->insert(DB_TBL_VARIATION, $data);               
            }
            
        }  
        
        public function create_event($event_data, $id)
        {
            $events= [[
                "experiment_id" => $id,                
                "caption" => "Desktop",    
                "key" => "desktop",                            
                                         
            ], [
                "experiment_id" => $id,                
                "caption" => "Mobile",    
                "key" => "mobile",                            
                                         
            ]];
            $i =0;
            foreach($event_data as $event){
             $events[$i]["event_caption"]  =$event["caption"];
             $events[$i]["event_key"]  =$event["key"];
             $i++;
            }
            self::create_event_category($events);
        }

        public function create_event_category($events) {
            global $dbi;
            foreach($events as $event){
                $insert_data=[
                    "experiment_id" => $event["experiment_id"],                
                    "caption" => $event["caption"],    
                    "key" => $event["key"],  
                ];
                $dbi->insert(DB_TBL_EVENT_CATEGORY, $insert_data);
                $id = $dbi->connection->insert_id;
                $data= [
                    "experiment_id" => $event["experiment_id"],    
                    "event_category_id" => $id,            
                    "caption" => $event["event_caption"],    
                    "key" => $event["event_key"],                                                      
                ];
                $dbi->insert(DB_TBL_EVENT, $data);  
            }
           
        }

        public function get_daily_conversions($exp_id,$start_date,$end_date)
        {
            global $dbi;
            $query = sprintf("SELECT Date, 
                sum(case when cat = 'Mobile' AND variant='standard' then Conversions end) as Standard_Mobile,
                sum(case when cat = 'Desktop' AND variant='standard' then Conversions  end) as Standard_Desktop,
				sum(case when cat = 'Mobile' AND variant !='standard' then Conversions end) as Variant_Mobile,
                sum(case when cat = 'Desktop'  AND variant !='standard'then Conversions  end) as Variant_Desktop
             FROM 
            (SELECT  
              c.caption AS `cat`,
              CAST(l.date AS DATE) AS Date,
              v.key as  Variant,
              COUNT(*) AS Conversions  
            FROM event e
              INNER JOIN event_category c
                ON e.event_category_id = c.id
              INNER JOIN event_log l
                ON l.event_id = e.id
              INNER JOIN instance i 
	            ON l.instance_id = i.id
              INNER JOIN variation v
	            ON v.id = i.variation_id
            WHERE e.experiment_id = %d AND DATE(l.date) BETWEEN '%s' AND '%s'
            GROUP BY DATE(l.date), e.id, variant ) results  
            group by date
            Order by date",$exp_id,$start_date,$end_date);            
            return $dbi->query_to_multi_array($query);
        }
    }
