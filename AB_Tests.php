<?php  

    class AB_Tests {

        public function get_all_tests(){
            global $dbi;
            $query = sprintf("SELECT * FROM experiment e LEFT JOIN ab_tests a ON e.id = a.experiment_id WHERE a.status = 1");
            return $dbi->query_to_multi_array( $query );
        }
        
        public function get_all_tests_by_variation(){
            global $dbi;
            $query = sprintf("SELECT e.id experiment_id, e.name experiment_name, e.`key` experiment_key, e.created_on experiment_created_on, v.id variation_id, v.caption variation_caption, v.`key` variation_key, v.traffic_distribution traffic_distribution, v.instances instance_count, GROUP_CONCAT(d.category_key SEPARATOR 0x00 ) category_keys, GROUP_CONCAT(d.category_caption SEPARATOR 0x00 ) category_captions, GROUP_CONCAT(d.event_keys SEPARATOR 0x00 ) event_keys, GROUP_CONCAT(d.event_captions SEPARATOR 0x00 ) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x00 ) trigger_count FROM variation v INNER JOIN experiment e ON v.experiment_id = e.id LEFT OUTER JOIN (SELECT d.variation_id, d.category_key, d.category_caption, GROUP_CONCAT(d.event_key SEPARATOR 0x01 ) event_keys, GROUP_CONCAT(d.event_caption SEPARATOR 0x01) event_captions, GROUP_CONCAT(d.trigger_count SEPARATOR 0x01) trigger_count FROM (SELECT c.* FROM (SELECT v.id variation_id, IFNULL(c.`key`, 'uncategorized') category_key, IFNULL(c.caption, 'Uncategorized') category_caption, ev.`key` event_key, ev.caption event_caption, SUM(IF(l.id IS NULL, 0, 1)) trigger_count FROM experiment e RIGHT OUTER JOIN variation v ON v.experiment_id = e.id RIGHT OUTER JOIN event ev ON ev.experiment_id = e.id LEFT OUTER JOIN event_category c ON ev.event_category_id = c.id LEFT OUTER JOIN instance i ON i.variation_id = v.id LEFT OUTER JOIN event_log l ON l.event_id = ev.id AND l.instance_id = i.id WHERE e.`key`= 'payment_page' GROUP BY v.id, c.id, ev.id) c GROUP BY c.variation_id, c.category_key, c.event_key) d GROUP BY d.variation_id, d.category_key) d ON v.id = d.variation_id GROUP BY v.id");
            return $dbi->query_to_multi_array( $query );
        }
    }
