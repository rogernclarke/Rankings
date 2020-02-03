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

<table class="table-hover tt-table tt-rides">
<thead>
	<tr>
		<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
		<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
		<th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
		<th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
		<th class="tt-col-ride-position visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION'); ?></th>
		<th class="tt-col-ride-position hidden-desktop hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
		<th class="tt-col-ride-result hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RESULT'); ?></th>
		<th class="tt-col-rider-ride-points"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
	</tr>
</thead>
<tbody>
	<! -- Display Ride row -->
	<?php foreach($this->rides as $i => $ride) : ?>
		<?php $this->ride = $ride; ?>
		<?php echo $this->loadTemplate('ride'); ?>
	<?php endforeach; ?>
</tbody>
<!--
<tfoot>
	<tr>
		<td colspan="6">
			<div class="tt-table-counters">
				<div class="tt-pages-counter pull-left"><?php //echo $this->ttRidesPagination->getPagesCounter(); ?></div>
				<div class="tt-results-counter pull-right"><?php //echo $this->ttRidesPagination->getResultsCounter(); ?></div>
			</div>
			<div class="pagination tt-pagination"><?php //echo $this->ttRidesPagination->getPagesLinks(); ?></div>
		</td>
	</tr>
</tfoot>
-->
</table>
