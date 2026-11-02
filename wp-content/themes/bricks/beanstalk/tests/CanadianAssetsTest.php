<?php
namespace Koala\Beanstalk\Tests;
final class CanadianAssetsTest extends TestCase {
 public function test_popup_styles_target_canadian_form_three(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-estimate-popup.css');
  self::assertStringContainsString('#field_3_1',$css);
  self::assertStringNotContainsString('#field_13_',$css);
 }
 public function test_canadian_location_navigation_retains_canadian_franchise_destination(): void {
  foreach(['location-header.php','location-footer.php'] as $file) {
   $html=file_get_contents(dirname(__DIR__).'/'.$file);
   self::assertStringContainsString('https://koalainsulationfranchise.ca/', $html);
   self::assertStringNotContainsString('https://koalainsulationfranchise.com/', $html);
  }
 }
 public function test_location_footer_preserves_canadian_social_and_citation_links(): void {
  $html=file_get_contents(dirname(__DIR__).'/location-footer.php');
  foreach(['energystar.gov/saveathome/seal_insulate/methodology','facebook.com/koalainsulationcanada','instagram.com/koalainsulationcanada/','linkedin.com/company/koala-insulation-canada/','youtube.com/channel/UCEYUbnu82sHwLIRzsY6qtPQ','naimacanada.ca/for-homeowners/why-insulate/value-savings','natural-resources.canada.ca/maps-tools-publications/publications/energy-efficiency-trends-canada-1990-2013#a2','energy.gov/sites/prod/files/2013/11/f5/insulation_guide.pdf'] as $url) self::assertStringContainsString($url,$html);
 }
 public function test_popup_attribution_uses_canadian_hidden_fields(): void {
  $js=file_get_contents(dirname(__DIR__).'/assets/location-estimate-popup.js');
  self::assertStringContainsString('23:trigger.textContent.trim()', $js);
  self::assertStringContainsString('24:trigger.closest(', $js);
 }
 public function test_canadian_tracking_exposes_shared_loader_and_preserves_ga_destinations(): void {
  $source=file_get_contents(dirname(__DIR__).'/../functions.php');
  self::assertStringContainsString('window.koalaLoadLocationGtm = function',$source);
  foreach(['G-9RQMD52K78','G-RFK3ZB6M00','G-1HN84YS8QG'] as $id) self::assertStringContainsString($id,$source);
 }
 public function test_canadian_removal_template_preserves_pr_seven_routing(): void {
  $source=file_get_contents(dirname(__DIR__).'/../functions.php');
  self::assertMatchesRegularExpression("/'insulation-removal-service'\\s*=>\\s*20483/",$source);
  self::assertStringNotContainsString("'insulation-removal-services'",$source);
 }
}
