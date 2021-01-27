<?php
    /**
    * Database Interface Class (DBI Class)
    * Author : Savindra Marasinghe
    * Author Email : info@webappend.com
    * Author URL : www.webappend.com
    * Version : 2.9
    * Released Date : 6/12/2020
    */

    class DBI {

        public
            $connection,
            $log_errors = true,
            $log_path = '',
            $auto_retries_on_connection_lost = 0,
            $connection_lost_retry_interval = 10000,
            $auto_retries_on_deadlock = 0,
            $deadlock_retry_interval = 2000,
            $log_query_execution_time = true,
            $reconnect_status,
            $log_format_csv = false,
            $on_connection_failure,
            $load_data_from_remote = false;

        protected
            $host,
            $username,
            $password,
            $database;

        /**
        * Constructor
        *
        */
        public function __construct() {

            $this->log_path = getcwd();
            if ( substr( $this->log_path, -1 ) != DIRECTORY_SEPARATOR ) $this->log_path .= DIRECTORY_SEPARATOR;

        }

        /**
        * Connect to the database
        *
        * @param string $host
        * @param string $username
        * @param string $password
        * @param string $database
        */
        public function connect( $host, $username, $password, $database, $log_errors = true, $log_query_execution_time = true ) {

            $this->reconnect_status = ini_get( "mysqli.reconnect" );
            $this->host = $host;
            $this->username = $username;
            $this->password = $password;
            $this->database = $database;

            $this->_connect();
            if ( $this->connection->connect_error ) {

                if ( $this->log_errors ) {

                    $fp = fopen( $this->log_path . "db_connection_err_log.txt", "a" );
                    fputcsv( $fp, [ date("Y-m-d H:i:s"), trim( $this->connection->connect_error ) ], "\t" );
                    fclose( $fp );

                }

                if ( is_callable( $this->on_connection_failure ) ) {

                    return call_user_func( $this->on_connection_failure );

                } else {

                    die( "<h2>Error: Database connection failed! Please try again in a few minutes.</h2>" );

                }

            }

            $this->connection->set_charset( "utf8" );
            $this->log_errors = $log_errors;
            $this->log_query_execution_time = $log_query_execution_time;

            return true;

        }

        protected function _connect() {

            @$this->connection = new mysqli( $this->host, $this->username, $this->password, $this->database );

        }

        /**
        * Execute query
        *
        * @param string $sql
        * @return mixed
        */
        public function query( $sql ) {

            $deadlock_retry_count = $this->auto_retries_on_deadlock;
            $connection_lost_retry_count = $this->auto_retries_on_connection_lost;

            if ( $this->log_errors || $this->log_query_execution_time ) {

                $trace = debug_backtrace();
                array_shift( $trace );
                $trace = array_reverse( $trace );

                $trace_steps = array_map( function( $value ) {

                    $replace_path = defined("ABS_PATH") ? ABS_PATH : __DIR__;
                    return str_replace( $replace_path, "", "{$value["file"]} ({$value["line"]})" );

                }, $trace );
                $trace_steps = implode( " > ", $trace_steps );
                $current_time = date("Y-m-d H:i:s");

            }

            while ( true ) {

                @$this->connection->ping();
                $start_time = microtime( true );
                $return = $this->connection->query( $sql );
                $execution_time = round( microtime( true ) - $start_time, 5 );

                if ( ! $this->connection->error ) break;

                if ( $connection_lost_retry_count && ( 2006 == $this->connection->errno ) ) {

                    usleep( $this->connection_lost_retry_interval );
                    $connection_lost_retry_count--;
                    if ( empty( $this->reconnect_status ) ) {

                        $this->_connect();

                    }

                } else if ( $deadlock_retry_count && ( 1213 == $this->connection->errno || 1205 == $this->connection->errno ) ) {

                    usleep( $this->deadlock_retry_interval );
                    $deadlock_retry_count--;

                } else break;

            }

            if ( $this->log_query_execution_time ) {

                $fp = fopen( $this->log_path . "db_query_log" . ( $this->log_format_csv ? ".csv" : "" ), "a" );
                if ( $this->log_format_csv ) fputcsv( $fp, [ $current_time, str_replace( '\"', '\""', $sql ), $trace_steps, $execution_time ], ",", '"', '"' );
                else fputs( $fp, $current_time . " : {$sql}\r\nTrace: {$trace_steps}\r\nExecution Time: {$execution_time}\r\n\r\n" );
                fclose( $fp );

            }

            if ( $this->log_errors && ! empty( $this->connection->error ) ) {

                $fp = fopen( $this->log_path . "db_err_log" . ( $this->log_format_csv ? ".csv" : "" ), "a" );
                if ( $this->log_format_csv ) fputcsv( $fp, [ $current_time, $this->connection->errno, $this->connection->error, str_replace( '\"', '\""', $sql ), $trace_steps ], ",", '"', '"' );
                else fputs( $fp, $current_time . " : {$this->connection->error} : {$sql}\r\nError Code: {$this->connection->errno}\r\nTrace: {$trace_steps}\r\n\r\n" );
                fclose( $fp );

            }

            return $return;

        }

        /**
        * Execute query and get the result as an multi-dimensional associative array
        *
        * @param string $sql
        * @return mixed
        */
        public function query_to_multi_array( $sql ) {

            $result = $this->query( $sql );

            if ( ! $result ) return array();

            $result_array = array();

            while ( $data = $result->fetch_assoc() ) {

                $result_array[] = $data;

            }

            $result->free();

            return $result_array;

        }

        /**
        * Execute query and get the result as a single row associative array
        *
        * @param string $sql
        * @return mixed
        */
        public function query_to_array( $sql ) {

            if ( ! $result = $this->query_to_multi_array( $sql ) ) return array();
            else return $result[0];

        }

        /**
        * Insert row to the specified table.
        *
        * @param string $table
        * @param mixed $data
        * @return bool
        */
        public function insert( $table, $data ) {

            $sql = "INSERT INTO `{$table}` ( `" . implode( "`, `", array_keys( $data ) ) . "` ) VALUES ( ";

            foreach ( $data as $key => $value ) $sql .= "'" . $this->escape( $value ) . "', ";

            $sql = substr( $sql, 0, -2 ) . " );";

            if ( $result = $this->query( $sql ) ) return true;
            else return false;

        }

        /**
        * Update row data.
        *
        * @param string $table
        * @param mixed $data
        * @param string $where_condition
        * @return bool
        */
        public function update( $table, $data, $where_condition = "" ) {

            $sql = "UPDATE `{$table}` SET ";

            $set_fields = [];
            foreach ( $data as $key => $value ) {

                if ( is_null( $value ) ) $set_fields[] = "`{$key}` = null";
                else $set_fields[] = "`{$key}` = '" . $this->escape( $value ) . "'";

            }
            $where_condition = ( ! empty( $where_condition ) ) ? "WHERE {$where_condition}" : "";
            $sql .= implode( ", ", $set_fields ) . " {$where_condition}";

            if ( $result = $this->query( $sql ) ) return true;
            else return false;

        }

        /**
        * Insert or Update on duplicate key.
        *
        * @param string $table
        * @param mixed $data
        * @param string $where_condition
        * @return bool
        */
        public function insert_update( $table, $data, $update_data ) {

            $sql = "INSERT INTO `{$table}` ( `" . implode( "`, `", array_keys( $data ) ) . "` ) VALUES ( ";

            foreach ( $data as $key => $value ) $sql .= "'" . $this->escape( $value ) . "', ";

            $sql = substr( $sql, 0, -2 ) . " ) ON DUPLICATE KEY UPDATE ";

            foreach ( $update_data as $key => $value ) $sql .= "`{$key}` = '" . $this->escape( $value ) . "', ";

            $sql = substr( $sql, 0, -2 ) . ";";

            if ( $result = $this->query( $sql ) ) return true;
            else return false;

        }

        /**
        * Create a URI safe string
        *
        * @param string $string
        * @return string
        */
        public function uri_safe_string( $string ) {

            return preg_replace( "/[-]+/", "-", preg_replace( "/[^a-z0-9]+/i", "-", strtolower( $string ) ) );

        }

        /**
        * Escape a String
        *
        * @param string $string
        * @return string
        */
        public function escape( $string ) {

            $string = ( get_magic_quotes_gpc() ) ? stripslashes( $string ) : $string;
            return $this->connection->real_escape_string( $string );

        }

        /**
        * Load Data into Table
        *
        * @param string $table_name
        * @param string $filename
        * @param array $col_names
        * @param int $ignore_lines
        * @param char $fields_terminated_by
        * @param char $fields_optionally_enclosed_by
        * @param boolean $ignore_duplicates
        * @param string $charset
        */
        public function load_data( $table_name, $filename, $col_names, $ignore_lines = 0, $fields_terminated_by = ",", $fields_optionally_enclosed_by = '"', $ignore_duplicates = true, $charset = "utf8" ) {

            $ignore_lines = is_null( $ignore_lines ) ? 0 : $ignore_lines;
            $fields_terminated_by = is_null( $fields_terminated_by ) ? "," : $fields_terminated_by;
            $fields_optionally_enclosed_by = is_null( $fields_optionally_enclosed_by ) ? '"' : $fields_optionally_enclosed_by;
            $ignore_duplicates = is_null( $ignore_duplicates ) ? true : $ignore_duplicates;
            $charset = is_null( $charset ) ? "utf8" : $charset;

            $option_replace_ignore = ( $ignore_duplicates ) ? "IGNORE" : "REPLACE";
            $option_ignore_lines = ( $ignore_lines ) ? "IGNORE {$ignore_lines} LINES" : "";
            $column_names = implode( ", ", array_map( function( $value ) {

                return "`{$value}`";

            }, $col_names ) );

            $sql = sprintf( "LOAD DATA %s INFILE '%s' %s INTO TABLE `%s` CHARACTER SET %s FIELDS TERMINATED BY '%s' OPTIONALLY ENCLOSED BY '%s' %s ( %s )", ( ! $this->load_data_from_remote ? "LOCAL" : "" ) , $this->escape( $filename ), $option_replace_ignore, $table_name, $charset, $this->escape( $fields_terminated_by ), $this->escape( $fields_optionally_enclosed_by ), $option_ignore_lines, $column_names );
            return $this->query( $sql );

        }

        /**
        * Create Table
        *
        * @param string $table_name
        * @param array $fields [ type, unsigned, null, default, index ]
        * @param string $engine
        * @param string $charset
        * @param string $collate
        * @param array $combined_indexes
        * @param boolean $temporary
        */
        public function create_table( $table_name, $fields, $engine = "INNODB", $charset = "utf8", $collate = "utf8_general_ci", $temporary = 1, $combined_indexes = [] ) {

            $engine = is_null( $engine ) ? "INNODB" : $engine;
            $charset = is_null( $charset ) ? "utf8" : $charset;
            $collate = is_null( $collate ) ? "utf8_general_ci" : $collate;
            $temporary = is_null( $temporary ) ? 1 : $temporary;
            $combined_indexes = is_null( $combined_indexes ) ? [] : $combined_indexes;

            $temporary = ( $temporary ) ? "TEMPORARY" : "";
            $indexes = [];

            foreach ( $fields as $col_name => $field ) {

                unset( $fields[ $col_name ] );
                $col_name = trim( $col_name );

                if ( is_array( $field ) ) {

                    if ( empty( $field["type"] ) ) continue;
                    $field["unsigned"] = ! empty( $field["unsigned"] ) ? "UNSIGNED" : "";
                    $field["null"] = ! empty( $field["null"] ) ? "" : "NOT NULL";
                    $field["default"] = ! empty( $field["default"] ) ? ( empty( $field["null"] ) ? "NULL" : "DEFAULT {$field["default"]}" ) : "";
                    $fields[ $col_name ] = trim( "`{$col_name}` " . strtoupper( $field["type"] ) . " {$field["unsigned"]} {$field["null"]} {$field["default"]}" );

                } else $fields[ $col_name ] = trim( "`{$col_name}` " . $field );

                if ( isset( $field["index"] ) && ! empty( $field["index"] ) ) $indexes[] = "INDEX `idx_{$col_name}` (`{$col_name}`)";

            }
            $fields = implode( ", ", $fields );
            if ( ! empty( $indexes ) ) $fields = $fields . ", " . implode( ", ", $indexes );

            $multi_column_indexes = [];
            if ( ! empty( $combined_indexes ) && is_array( $combined_indexes ) ) {

                foreach ( $combined_indexes as $index => $_combined_indexes ) {

                    if ( empty( $_combined_indexes["fields"] ) ) continue;

                    $index_fields = implode( ", ", array_map( function ( $field ) {

                        return "`{$field}`";

                    }, $_combined_indexes["fields"] ) );

                    $index_tbl_name = preg_replace( [ "/[^a-z0-9]/", "/[_]+/" ], "_", strtolower( $table_name ) );
                    $index_unique = ( ! empty( $_combined_indexes["unique"] ) );
                    $index_name = ( $index_unique ? "uk" : "idx" ) . "_{$index_tbl_name}_" . strtolower( implode( "_", $_combined_indexes["fields"] ) );

                    $multi_column_indexes[] = ( $index_unique ? "UNIQUE " : "" ) . "INDEX {$index_name} ( {$index_fields} )";

                }

            }
            $multi_column_indexes = ! empty( $multi_column_indexes ) ? ", " . implode( ", ", $multi_column_indexes ) : "";

            $sql = sprintf( "CREATE %s TABLE `%s` ( %s %s ) ENGINE = %s CHARACTER SET %s COLLATE %s", $temporary, $this->escape( $table_name ), $fields, $multi_column_indexes, $engine, $charset, $collate );
            return $this->query( $sql );

        }

        /**
        * Create Table Copy
        *
        * @param string $source_table_name
        * @param string $destination_table_name
        * @param boolean $temporary
        */
        public function create_table_copy( $source_table_name, $destination_table_name, $temporary = 1 ) {

            $temporary = ( $temporary ) ? "TEMPORARY" : "";
            $sql = sprintf( "CREATE %s TABLE `%s` LIKE `%s`", $temporary, $destination_table_name, $source_table_name );
            return $this->query( $sql );

        }

        /**
        * Create Table Copy with Data
        *
        * @param string $source_table_name
        * @param string $destination_table_name
        * @param boolean $with_indexes
        * @param boolean $temporary
        */
        public function create_table_copy_with_data( $source_table_name, $destination_table_name, $with_indexes = 1, $temporary = 1 ) {

            $temporary = ( $temporary ) ? "TEMPORARY" : "";

            if ( $with_indexes ) {

                $this->create_table_copy( $source_table_name, $destination_table_name, $temporary );
                $sql = sprintf( "INSERT INTO `%s` SELECT * FROM `%s`", $destination_table_name, $source_table_name );
                return $this->query( $sql );

            } else {

                $sql = sprintf( "CREATE %s TABLE `%s` SELECT * FROM `%s`", $temporary, $destination_table_name, $source_table_name );
                return $this->query( $sql );

            }

        }

        /**
        * Get Table Fields
        *
        * @param string $table_name
        */
        public function get_table_fields( $table_name, $return_type = false ) {

            $sql = sprintf( "DESCRIBE %s;", $table_name );
            $results = $this->query_to_multi_array( $sql );

            $table_fields = [];
            foreach ( $results as $result ) {

                if ( $return_type ) $table_fields[ $result["Field"] ] = $result["Type"];
                else $table_fields[] = $result["Field"];

            }

            return $table_fields;

        }

        /**
        * Flatten an array
        *
        * @param array $array
        * @param string $prefix
        */
        public static function array_flatten( &$array, $prefix = "" ) {

            $return = [];

            if ( is_array( $array ) ) {

                foreach ( $array as $key => $value ) {

                    if ( is_array( $value ) ) $return = $return + self::array_flatten( $value, $prefix . ( ( $prefix ) ? "/" : "" ) . $key );
                    else {

                        $return[ $prefix . ( $prefix ? "/" : "" ) . $key ] = $value;

                    }

                }

            }

            return $return;

        }

        public static function xml_to_array( &$xml ) {

            $array = [];

            foreach ( (array) $xml as $key => $value ) {

                if ( is_object( $value ) || is_array( $value ) ) $array[ $key ] = self::xml_to_array( $value );
                else $array[ $key ] = $value;

            }

            return empty( $array ) ? "" : $array;

        }

        /**
        * Map Data
        *
        * @param mixed $mapper
        * @param mixed $data
        */
        public static function map_data( &$mapper, &$data, $nullify_empty_values = false, $return_all_keys = false ) {

            $data_source = self::array_flatten( $data );

            $mapper_filtered = array_filter( $mapper );
            $return = array_combine( array_keys( $mapper_filtered ), array_replace( array_fill_keys( $mapper_filtered, "" ), array_intersect_key( $data_source, array_fill_keys( $mapper_filtered, "" ) ) ) );

            $return = ( $nullify_empty_values ) ? array_map( function( $data ) {

                return ( $data !== "" && $data !== null ) ? $data : "\\N";

            }, $return ) : $return;

            return ( $return_all_keys ) ? array_replace( $mapper, $return ) : $return;

        }

    }