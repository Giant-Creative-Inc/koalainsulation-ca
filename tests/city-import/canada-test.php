<?php
use GiantCreative\BeanstalkContentEngine\CityPageCsv;
require __DIR__ . '/csv-test.php';
$GLOBALS['matches'] = [];
$GLOBALS['legacy'] = [];
$row = ['title'=>'Oakville','slug'=>'oakville-on','related_location_id'=>'4931','province_name'=>'Ontario','province_abbreviation'=>'ON'];
$result = CityPageCsv::normalize($row);
check(!is_wp_error($result) && $result['state_name']==='Ontario' && $result['state_abbreviation']==='ON','Canadian province headers must map to the shared city contract.');
$path = tempnam(sys_get_temp_dir(),'ca-cities-');
file_put_contents($path,"title,slug,related_location_id,province_abbreviation,province_name\nOakville,oakville-on,4931,ON,Ontario\n");
$parsed = CityPageCsv::read($path);unlink($path);
check(!is_wp_error($parsed) && $parsed[0]['state_name']==='Ontario','Canadian CSV headers must parse.');
$row['state_name']='Quebec';
check(is_wp_error(CityPageCsv::normalize($row)),'Conflicting province/state aliases must be rejected.');
unset($row['state_name']);
$GLOBALS['attachment_match_url']='https://koalainsulation.local/wp-content/uploads/2026/10/known.jpg';
$row['commercial_services_image_1']='https://koalainsulation.com/ca/wp-content/uploads/2026/10/known.jpg';
check(!is_wp_error(CityPageCsv::normalize($row)),'Canadian /ca uploads must map to existing Local attachments without downloads.');
echo "Canadian province CSV tests passed\n";
