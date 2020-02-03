
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

<table class="table-hover tt-table tt-rider-awards-list">
	<thead>
		<tr>
			<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
			<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
			<th class="tt-col-ride-distance visible-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
			<th class="tt-col-ride-distance hidden-desktop"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
			<th class="tt-col-award-name"><?php echo JText::_('COM_RANKINGS_AWARD_NAME'); ?></th>
			<th class="tt-col-award-result hidden-phone"><?php echo JText::_('COM_RANKINGS_AWARD_RESULT'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $previousEventId = null; ?>
		<?php foreach($this->awards as $i => $award) : ?>
			<?php if($previousEventId !== $award->event_id) : ?>
				<?php $newEvent      	= true; ?>
				<?php $eventAwardCount  = 0; ?>
				<?php $previousEventId  = $award->event_id; ?>
				<?php for($j = $i; $j < count($this->awards) && $award->event_id === $this->awards[$j]->event_id; $j++) : ?>
					<?php $eventAwardCount++; ?>
				<?php endfor; ?>
			<?php else : ?>
				<?php $newEvent = false; ?>
			<?php endif; ?>
			<?php $this->award            = $award; ?>
			<?php $this->newEvent         = $newEvent; ?>
			<?php $this->eventAwardCount  = $eventAwardCount; ?>
			<?php echo $this->loadTemplate('award'); ?>
		<?php endforeach; ?>
	</tbody>
	<!--
	<tfoot>
		<tr>
			<td colspan="5">
				<div class="tt-table-counters">
					<div class="tt-pages-counter pull-left"><?php //echo $this->ttAwardsPagination->getPagesCounter(); ?></div>
					<div class="tt-results-counter pull-right"><?php //echo $this->ttAwardsPagination->getResultsCounter(); ?></div>
				</div>
				<div class="pagination tt-pagination"><?php //echo $this->ttAwardsPagination->getPagesLinks(); ?></div>
			</td>
		</tr>
	</tfoot>
	-->
</table>
