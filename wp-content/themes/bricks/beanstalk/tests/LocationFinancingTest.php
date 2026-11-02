<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class LocationFinancingTest extends TestCase {
 public function test_financing_is_hidden_by_default_and_reads_the_location_link(): void {
  Functions\when('add_action')->justReturn();
  Functions\when('add_filter')->justReturn();
  require_once dirname(__DIR__).'/location-financing.php';
  Functions\when('get_post_meta')->alias(static function($id,$key){return $key==='show_financing'?'0':'https://example.com/apply';});
  self::assertFalse(koala_beanstalk_location_financing_enabled(4923));
  self::assertSame('https://example.com/apply',koala_beanstalk_location_financing_link(4923));
 }
 public function test_pattern_preserves_editable_copy_and_disclosure_link(): void {
  $source=file_get_contents(dirname(__DIR__).'/patterns/location-financing.php');
  self::assertStringContainsString('<!-- wp:heading',$source);
  self::assertStringContainsString('<!-- wp:button',$source);
  self::assertStringContainsString('href="#footnote"',$source);
  self::assertStringContainsString('Apply Now',$source);
  self::assertStringContainsString('See If You Prequalify',$source);
 }
}
