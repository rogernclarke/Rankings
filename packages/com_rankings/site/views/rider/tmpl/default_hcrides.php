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

<div role="tabpanel" class="tab-pane" id="hillclimbs">
	<div class="tt-nav-tab-content">
		<?php if (count($this->hcRides) == 0) : ?>
			<div class="tt-list-heading">
				<div class="tt-list-title">
					<h2><?php echo "No results since 1st January 2017"; ?></h2>
				</div>
			</div>
		<?php else : ?>
			<div class="tt-list-heading">
				<div class="tt-list-title">
					<h2><?php echo JText::_('COM_RANKINGS_RIDER_RESULTS'); ?></h2>
				</div>
			</div>
			<div class="tt-tab-panel">
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
					<?php foreach($this->hcRides as $i => $ride) : ?> 
						<?php if(($i == 0) or ($i > 0 && date('Y', strtotime($ride->event_date)) != date('Y', strtotime($this->hcRides[$i-1]->event_date)))) : ?>
							<tr class="tt-table-year-row">
								<td colspan="6"><?php echo date('Y', strtotime($ride->event_date)); ?></td>
							</tr>
						<?php endif; ?>
						<?php $this->ride = $ride; ?>
						<?php echo $this->loadTemplate('ride'); ?>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="6">
							<div class="tt-table-counters">
								<div class="tt-pages-counter pull-left"><?php echo $this->hcRidesPagination->getPagesCounter(); ?></div>
								<div class="tt-results-counter pull-right"><?php echo $this->hcRidesPagination->getResultsCounter(); ?></div>
							</div>
							<div class="pagination tt-pagination"><?php echo $this->hcRidesPagination->getPagesLinks(); ?></div>
						</td>
					</tr>
				</tfoot>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>