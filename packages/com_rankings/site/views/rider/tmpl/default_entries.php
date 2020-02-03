<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<! -- Display Entries table -->
<table class="table-hover tt-table tt-rides">
<thead>
	<tr>
		<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
		<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
		<th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
		<th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
	</tr>
</thead>
<tbody>
	<! -- Display Entry row -->
	<?php foreach($this->entries as $i => $entry) : ?>
		<?php $this->entry = $entry; ?>
		<?php echo $this->loadTemplate('entry'); ?>
	<?php endforeach; ?>
</tbody>
</table>
