<?php 
/**
 * Rankings Slider Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<div class="owl-carousel owl-theme">
	<?php foreach($modules as $module) : ?>
		<div class="item" data-merge="1">
			<h3><?php echo $module->title; ?></h3>
			<?php echo $module->content; ?></div>
	<?php endforeach; ?>
</div>

<script>jQuery('.owl-carousel').owlCarousel({
	autoplay:false,
	autoplayTimeout:5000,
	autoplayHoverPause:true,
	items:2,
	loop:true,
	margin:24,
	nav:true,
	slideBy:2,
	responsive:{
		0:{
			items:1,
			margin:12
		},
		608:{
			items:1,
			margin:16
		},
		1000:{
		}
	}
})</script>
