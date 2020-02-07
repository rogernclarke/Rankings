<?php 
/**
 * Rankings Last Updated Module for Joomla 3.x
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

<h3 class="tt-last-updated-title"><?php echo JText::_('COM_RANKINGS_LAST_UPDATED'); ?></h3>
<div id="tt-last-updated-date">
	<?php echo date('jS F Y', strtotime($date)); ?>
</div>