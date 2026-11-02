<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class CanadianDataContextTest extends TestCase {
 public function test_ca_location_context_comes_from_queried_location_not_ca_path_segment(): void {
  require_once dirname(__DIR__).'/data.php';
  $post=new \WP_Post(['ID'=>20105,'post_type'=>'location','post_status'=>'publish']);
  Functions\when('get_queried_object_id')->justReturn(20105);
  Functions\when('get_post')->alias(fn($id)=>$id===20105?$post:null);
  Functions\when('get_post_type')->justReturn('location');
  Functions\when('get_post_meta')->alias(fn($id,$key)=>['location_name'=>'London','location_state'=>'Ontario','location_phone_number'=>'226-547-0332','google_place_id'=>'canadian-place'][$key]??'');
  Functions\when('get_the_title')->justReturn('London');
  Functions\when('get_permalink')->justReturn('https://koalainsulation.com/ca/london');
  Functions\when('trailingslashit')->alias(fn($s)=>rtrim($s,'/').'/');
  Functions\when('get_page_by_path')->justReturn(null);
  Functions\when('sanitize_title')->alias(fn($s)=>$s);
  Functions\when('wp_unslash')->alias(fn($s)=>$s);
  Functions\when('wp_parse_url')->alias('parse_url');
  $_SERVER['REQUEST_URI']='/ca/london';
  $context=koala_beanstalk_get_location_context();
  self::assertSame(20105,$context['id']);
  self::assertSame('https://koalainsulation.com/ca/london',$context['home_url']);
  self::assertSame('226-547-0332',$context['phone']);
  self::assertSame('canadian-place',$context['google_place_id']);
 }
}
