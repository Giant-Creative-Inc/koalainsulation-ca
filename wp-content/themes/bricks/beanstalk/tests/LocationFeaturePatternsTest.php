<?php
namespace Koala\Beanstalk\Tests;
final class LocationFeaturePatternsTest extends TestCase {
 public function test_three_editable_feature_patterns_preserve_current_bricks_content(): void {
  foreach(['location-benefits','location-energy','location-expect'] as $name)self::assertFileExists(dirname(__DIR__).'/patterns/'.$name.'.php');
  $benefits=file_get_contents(dirname(__DIR__).'/patterns/location-benefits.php');
  $energy=file_get_contents(dirname(__DIR__).'/patterns/location-energy.php');
  $expect=file_get_contents(dirname(__DIR__).'/patterns/location-expect.php');
  self::assertSame(5,substr_count($benefits,'<!-- wp:details'));
  self::assertSame(5,substr_count($expect,'<!-- wp:details'));
  self::assertStringNotContainsString('Post Installation Inspection',$expect);
  self::assertStringContainsString('Proper insulation increases your',$energy);
  self::assertStringNotContainsString('Inflation Reduction Act',$energy);
  self::assertStringContainsString('/homeowner-incentives',$energy);
  foreach([$benefits,$energy,$expect] as $html){
   self::assertStringContainsString('<!-- wp:image',$html);
   self::assertStringNotContainsString('figma.com',$html);
   self::assertStringNotContainsString('get_field(',$html);
  }
 }
 public function test_all_new_raster_assets_are_compressed_webp(): void {
  $files=glob(dirname(__DIR__).'/assets/location-features/*.webp');
  self::assertCount(19,$files);
  foreach($files as $file){self::assertSame('image/webp',getimagesize($file)['mime']);self::assertLessThan(250000,filesize($file));}
 }
 public function test_adjacent_patterns_do_not_double_the_section_spacing(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  self::assertStringContainsString('.koala-location-feature+.koala-location-energy,.koala-location-energy+.koala-location-feature{padding-top:0}',$css);
 }
 public function test_energy_arrows_stack_behind_the_house(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  self::assertStringContainsString('.koala-location-energy__layer{position:absolute;inset:0;margin:0;width:100%;height:100%;z-index:0}',$css);
  self::assertStringContainsString('.koala-location-energy__layer:nth-child(-n+5){z-index:1}',$css);
 }
 public function test_feature_rows_have_the_reference_icons(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  foreach(['lightning-square','shield-heart','money-sack','club','benefit','phone-voice','list-order','calendar-check','building','couch'] as $icon){
   self::assertFileExists(dirname(__DIR__).'/assets/location-features/'.$icon.'.svg');
   self::assertStringContainsString('location-features/'.$icon.'.svg',$css);
  }
  self::assertStringContainsString('.koala-location-feature__item summary::before',$css);
 }
 public function test_energy_learn_button_has_compact_wrapped_lines(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  preg_match('/energy__learn \.wp-block-button__link\{([^}]+)\}/',$css,$rule);
  self::assertStringContainsString('line-height:24px',$rule[1]);
 }
 public function test_benefit_icons_include_their_interior_details(): void {
  foreach(['lightning-square'=>'lightning-detail','shield-heart'=>'heart-detail'] as $icon=>$detail){
   $svg=file_get_contents(dirname(__DIR__).'/assets/location-features/'.$icon.'.svg');
   self::assertStringContainsString('id="'.$detail.'"',$svg);
  }
 }
 public function test_feature_accordions_show_open_and_closed_chevrons(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  self::assertStringContainsString('.koala-location-feature__item summary::after',$css);
  self::assertStringContainsString('.koala-location-feature__item[open] summary::after{transform:rotate(225deg)}',$css);
  self::assertStringContainsString('margin-left:auto',$css);
 }
 public function test_energy_learn_button_width_is_capped(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  self::assertStringContainsString('.koala-location-energy__learn{width:340px;max-width:100%}',$css);
 }
 public function test_feature_accordion_animation_handles_cancellation_and_reduced_motion(): void {
  $js=file_get_contents(dirname(__DIR__).'/assets/location-features.js');
  self::assertStringContainsString('prefers-reduced-motion: reduce',$js);
  self::assertStringContainsString('item.animate(',$js);
  self::assertStringContainsString('animation?.cancel()',$js);
  self::assertStringContainsString('event.preventDefault()',$js);
 }
 public function test_mobile_accordion_rows_have_compact_padding(): void {
  $css=file_get_contents(dirname(__DIR__).'/assets/location-features.css');
  self::assertStringContainsString('@media(max-width:991px){.koala-location-feature__item{padding:12px 0 16px}}',$css);
 }
}
