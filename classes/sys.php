<?php 
    class SYS {
        
        public static function get_request_value( $name, $default = "", $type = "GET" ) {

            $array = isset( $GLOBALS["_{$type}"] ) ? $GLOBALS["_{$type}"] : array();
            $value = ( ! empty( $array[ $name ] ) ) ? $array[ $name ] : $default;
            return is_array( $value ) ? $value : ( is_null( $value ) ? null : trim( $value ) );

        }

        public static function request_get( $name, $default = "" ) {
            return self::get_request_value( $name, $default, "GET" );
        }

        public static function request_post( $name, $default = "" ) {
            return self::get_request_value( $name, $default, "POST" );
        }

        public static function request( $name, $default = "" ) {
            $post = self::request_post( $name, NULL );
            $get = self::request_get( $name, NULL );

            if ( NULL != $post ) return $post;
            elseif ( NULL != $get ) return $get;
            else return $default;        }

        public static function redirect( $relative_path = "", $permanent_redirect = false ) {
            header( "Location: " . $relative_path, true, $permanent_redirect ? 301 : 302 );
            die();
        }

        public static function is_ajax_request() {            
            return ( isset( $_SERVER["HTTP_X_REQUESTED_WITH"] ) && strtolower( $_SERVER["HTTP_X_REQUESTED_WITH"]) == "xmlhttprequest" );
        }

        public static function flush_ajax_response() {
            global $ajax_status;

            die( json_encode( $ajax_status ) );
        }
}