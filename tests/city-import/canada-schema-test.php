<?php
require __DIR__.'/../../wp-content/plugins/beanstalk-content-engine/src/ServiceAreaSchema.php';
function get_queried_object_id(){return 1;}
function get_post_meta($id,$key,$single){
 if($key==='_beanstalk_structured_data_profile')return 'koala/service-area';
 if($key==='_beanstalk_structured_data_values')return json_encode(['service_area_name'=>'Oakville','state_name'=>'Ontario','state_abbreviation'=>'ON','service_type'=>'Insulation services']);
 if($key==='rl_related_location')return [2];
 return '';
}
function get_post($id){return (object)['post_type'=>'location','post_status'=>'publish'];}
function trailingslashit($s){return rtrim($s,'/').'/';}
function home_url($path=''){return 'https://koalainsulation.ca'.$path;}
function get_permalink($id){return 'https://koalainsulation.ca/'.($id===1?'halton/oakville-on':'halton');}
function get_post_field(...$args){return '';}
function get_the_title($id){return $id===1?'Oakville':'Halton';}
function wp_strip_all_tags($s){return strip_tags($s);}
function parse_blocks($s){return [];}
function wp_json_encode($s,...$args){return json_encode($s);}
ob_start();(new GiantCreative\BeanstalkContentEngine\ServiceAreaSchema)->render();$html=ob_get_clean();
preg_match('~<script[^>]*>(.*?)</script>~s',$html,$m);$graph=json_decode($m[1]??'',true)['@graph']??[];
$business=array_values(array_filter($graph,fn($n)=>$n['@type']==='HomeAndConstructionBusiness'))[0]??[];
if(($business['address']['addressCountry']??'')!=='CA')throw new RuntimeException('Canadian city schema must default the business country to CA.');
echo "Canadian schema country test passed\n";
