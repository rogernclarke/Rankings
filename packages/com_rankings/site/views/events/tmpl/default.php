<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('COM_RANKINGS_EVENTS'); ?></h1>
<! -- Form -->
<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="tt-list-form">
		<! –– Display filters -->
		<div class="form-horizontal tt-list-filters">
			<fieldset class="adminform">
				<div>
					<?php
					// Set filter values for display and render
					$this->form->setValue('filter_district_code', null, $this->escape($this->state->get('filter.district_code')));
					$this->form->setValue('filter_distance', null, $this->escape($this->state->get('filter.distance')));
					$this->form->setValue('filter_year', null, $this->escape($this->state->get('filter.year')));
					$this->form->setValue('filter_course_code', null, $this->escape($this->state->get('filter.course_code')));
					$this->form->setValue('filter_event_name', null, $this->escape($this->state->get('filter.event_name')));
					echo $this->form->renderFieldset('event_list_toolbar'); ?>
				</div>
			</fieldset>
			<div class="btn-group pull-right">
				<button type="submit" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('jform_district_code_filter').value='All';document.getElementById('jform_distance_filter').value='All';document.getElementById('jform_year_filter').value='All';document.getElementById('jform_course_code_filter').value='All';document.getElementById('jform_event_name_filter').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="btn-group pull-right tt-list-limit hidden-phone">
			<label for="com_rankings.events.limit">
				<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<! -- Display Events table -->
	<table class="table-hover tt-table">
		<thead>
			<tr>
				<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
				<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
				<th class="tt-col-event-course hidden-phone"><?php echo JText::_('COM_RANKINGS_COURSE'); ?></th>
				<th class="tt-col-event-distance hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
				<th class="tt-col-event-distance visible-phone"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
			</tr>
		</thead>
		<tbody>
			<! -- Display Event row -->
			<?php for ($i = 0, $n = count($this->events); $i < $n; $i++) : ?>

				<?php if (($i == 0) || ($i > 0 && date('Y', strtotime($this->events[$i]->event_date)) != date('Y', strtotime($this->events[$i - 1]->event_date)))) : ?>
					<tr class="tt-table-year-row">
						<td colspan="4"><?php echo date('Y', strtotime($this->events[$i]->event_date)); ?></td>
					</tr>
				<?php endif ?>
				
				<?php $this->event = $this->events[$i]; ?>
				<?php echo $this->loadTemplate('event'); ?>		
			<?php endfor ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="4">
					<div class="tt-table-counters">
						<div class="tt-pages-counter pull-left"><?php echo $this->pagination->getPagesCounter(); ?></div>
						<div class="tt-results-counter pull-right"><?php echo $this->pagination->getResultsCounter(); ?></div>
					</div>
					<div class="pagination tt-pagination"><?php echo $this->pagination->getPagesLinks(); ?></div>
				</td>
			</tr>
		</tfoot>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
