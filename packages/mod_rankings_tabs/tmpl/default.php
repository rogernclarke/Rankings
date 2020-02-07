<?php 
/**
 * Rankings Tabs Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die; ?>

<h2><?php echo $title; ?></h2>
<div class="tt-tabs-stage">
	<ul class="nav nav-tabs tt-tabs" role="tablist">
		<?php foreach ($modules as $i => $module) : ?>
			<li role="presentation" class="<?php if ($i == 0) : echo "active"; endif; ?>" style="width:<?php echo 100 / count($modules); ?>%">
				<a href="#<?php echo strtolower(str_replace(' ', '', $title . '-' . $module->title)); ?>" aria-controls="<?php echo $module->title; ?>" role="tab" data-toggle="tab" data-context=""><h3><?php echo $module->title; ?></h3></a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php foreach($modules as $i => $module) : ?>
			<div role="tabpanel" class="tab-pane <?php if ($i == 0) : echo "active"; endif; ?>" id="<?php echo strtolower(str_replace(' ', '', $title . '-' . $module->title)); ?>">
				<?php echo $module->content; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
