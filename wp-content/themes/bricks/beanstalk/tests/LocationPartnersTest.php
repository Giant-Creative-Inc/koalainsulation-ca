<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationPartnersTest extends TestCase {
 protected function setUp(): void {
  parent::setUp();
  Functions\when('add_action')->justReturn(true);
  if(is_file(dirname(__DIR__).'/location-partners.php'))require_once dirname(__DIR__).'/location-partners.php';
 }
 public function test_partners_are_hidden_by_default_even_with_saved_logos(): void {
  self::assertTrue(function_exists('koala_beanstalk_render_location_partners'));
  Functions\when('get_the_ID')->justReturn(7);
  Functions\when('get_post_meta')->justReturn('');
  Functions\expect('get_field')->never();
  self::assertSame('',koala_beanstalk_render_location_partners());
 }
 public function test_enabled_partners_render_valid_logos_links_and_mobile_controls(): void {
  self::assertTrue(function_exists('koala_beanstalk_render_location_partners'));
  Functions\when('get_the_ID')->justReturn(7);
  Functions\when('get_post_meta')->alias(function($id,$key){return $key==='show_partners'?'1':'--';});
  Functions\when('get_field')->justReturn([
   ['partner_logo'=>['ID'=>51],'partner_link'=>'https://example.org'],
   ['partner_logo'=>false],
   ['partner_logo'=>['ID'=>52],'partner_link'=>''],
  ]);
  Functions\when('wp_get_attachment_image')->alias(function($id){return '<img data-id="'.$id.'">';});
  Functions\when('esc_url')->alias('htmlspecialchars');
  Functions\when('esc_html')->alias('htmlspecialchars');
  Functions\when('get_template_directory_uri')->justReturn('https://example.test/theme');
  $html=koala_beanstalk_render_location_partners();
  self::assertSame(2,substr_count($html,'class="koala-location-partners__item"'));
  self::assertStringContainsString('Your Trusted Insulation Contractor:',$html);
  self::assertStringContainsString('href="https://example.org" target="_blank" rel="noopener noreferrer"',$html);
  self::assertStringContainsString('data-partners-next',$html);
  self::assertStringContainsString('data-partners-prev',$html);
 }
 public function test_enabled_section_with_no_valid_logos_has_no_empty_spacing(): void {
  self::assertTrue(function_exists('koala_beanstalk_render_location_partners'));
  Functions\when('get_the_ID')->justReturn(7);
  Functions\when('get_post_meta')->justReturn('1');
  Functions\when('get_field')->justReturn([['partner_logo'=>false]]);
  self::assertSame('',koala_beanstalk_render_location_partners());
 }
 public function test_partner_logos_use_larger_responsive_bounds(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-partners.css');
  self::assertStringContainsString('max-width:200px;max-height:75px',$css);
  self::assertStringContainsString('max-width:100%;max-height:125px',$css);
 }
 public function test_desktop_logo_gap_is_fixed_at_64_pixels(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-partners.css');
  self::assertStringContainsString('justify-content:safe center;gap:64px',$css);
  self::assertStringContainsString('flex:0 0 auto',$css);
 }
}
