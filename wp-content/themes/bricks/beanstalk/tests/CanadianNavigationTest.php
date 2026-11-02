<?php
namespace Koala\Beanstalk\Tests;
use Brain\Monkey\Functions;
final class CanadianNavigationTest extends TestCase {
 public function test_only_published_assigned_owned_and_routable_pages_are_linked(): void {
  require_once dirname(__DIR__).'/location-navigation.php';
  Functions\when('get_post_meta')->alias(static function($id,$key){
   if($id===10)return ['related_blog_page'=>[20,21,22,23],'related_ho_page'=>[24],'related_rl_page'=>[25,25]][$key]??[];
   return [20=>[10],21=>[10],22=>[99],23=>[10],24=>[10]][$id]??'';
  });
  Functions\when('get_post_status')->alias(fn($id)=>$id===21?'draft':'publish');
  Functions\when('get_permalink')->alias(fn($id)=>'https://example.test/'.$id);
  Functions\when('url_to_postid')->alias(fn($url)=>(int)basename($url)===23?0:(int)basename($url));
  Functions\when('get_the_title')->alias(fn($id)=>'Page '.$id);
  $links=koala_beanstalk_location_assigned_links(10);
  self::assertSame(['Blog','Homeowner Incentives','Page 25'],array_column($links,'title'));
  self::assertSame(['https://example.test/20','https://example.test/24','https://example.test/25'],array_column($links,'url'));
 }
 public function test_missing_assignments_do_not_invent_resource_urls(): void {
  require_once dirname(__DIR__).'/location-navigation.php';
  Functions\when('get_post_meta')->justReturn([]);
  self::assertSame([],koala_beanstalk_location_assigned_links(10));
 }
 public function test_resource_menu_and_social_links_have_scoped_layout_rules(): void {
  $header=file_get_contents(dirname(__DIR__).'/assets/header.css');
  $footer=file_get_contents(dirname(__DIR__).'/assets/footer.css');
  self::assertStringContainsString('.beanstalk-header__dropdown:last-child > .beanstalk-header__submenu',$header);
  self::assertStringContainsString('.beanstalk-footer__bottom nav[aria-label="Social media"]',$footer);
 }
 public function test_footer_social_links_remain_readable_before_deferred_css(): void {
  $footer=file_get_contents(dirname(__DIR__).'/location-footer.php');
  self::assertStringContainsString('aria-label="Social media" style="display:flex;flex-wrap:wrap;gap:12px;color:#fff"',$footer);
  self::assertSame(4,substr_count($footer,'style="color:inherit" href="https://www.'));
 }
}
