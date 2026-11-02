<?php
require dirname(__DIR__,2).'/deployment/location-page-launch/migration.php';
class WP_Error { private $code; function __construct($code){$this->code=$code;} function get_error_code(){return $this->code;} }
function is_wp_error($x){return $x instanceof WP_Error;}
if(Koala_Location_Migration::feed_state([])!==[])throw new RuntimeException('Existing empty table must remain distinct');
if(Koala_Location_Migration::feed_state(new WP_Error('missing_table'))!==['_table'=>'absent'])throw new RuntimeException('Absent optional table must be fingerprinted');
$rejected=false;try{Koala_Location_Migration::feed_state(new WP_Error('database_error'));}catch(RuntimeException $e){$rejected=true;}if(!$rejected)throw new RuntimeException('Unexpected errors must stop migration');
echo "PASS: Canadian optional feed table state checks\n";
