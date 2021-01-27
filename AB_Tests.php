<?php  

    class AB_Tests {

        public function get_all_tests(){
            global $dbi;
            $query = sprintf("Select * from experiment");
            return $dbi->query_to_multi_array( $query );
        }
    }
?>