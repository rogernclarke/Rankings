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

// Position column is only displayed if at least one filter or search is active - determine and store whether to display the position column and the number of table columns
if (($this->state->get('filter.age_category') != 'All'
	&& !empty($this->state->get('filter.age_category')))
	|| ($this->state->get('filter.district_code') != 'All'
		&& !empty($this->state->get('filter.district_code')))
	|| $this->state->get('filter.name') != ''
	|| $this->state->get('filter.club_name') != '')
{
	$this->displayPosition = true;
	$this->columnCount = 7;
}
else
{
	$this->displayPosition = false;
	$this->columnCount = 6;
}
?>

<h1><?php echo $this->params->get('page_title'); ?></h1>
<! -- Form -->
<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
	<div class="tt-list-form">
		<! –– Display filters -->
		<div class="form-horizontal tt-list-filters">
			<fieldset class="adminform">
				<div>
					<?php
					// Set filter values for display and render
					$this->form->setValue('filter_year', null, $this->escape($this->state->get('filter.year')));
					$this->form->setValue('filter_gender', null, $this->escape($this->state->get('filter.gender')));
					$this->form->setValue('filter_age_category', null, $this->escape($this->state->get('filter.age_category')));
					$this->form->setValue('filter_district_code', null, $this->escape($this->state->get('filter.district_code')));
					$this->form->setValue('filter_club_name', null, $this->escape($this->state->get('filter.club_name')));
					$this->form->setValue('filter_name', null, $this->escape($this->state->get('filter.name')));
					$this->form->setValue('check_status', null, $this->escape($this->state->get('check.status')));
					echo $this->form->renderFieldset('ranking_list_toolbar'); ?>
				</div>
			</fieldset>
			<div class="btn-group pull-right">
				<button type="submit" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('jform_gender_filter').value='All';document.getElementById('jform_age_category_filter').value='All';document.getElementById('jform_district_code_filter').value='All';document.getElementById('jform_name_filter').value='';document.getElementById('jform_club_name_filter').value='';document.getElementById('jform_status_check').value=0;document.getElementById('reset_check').value=1;this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="btn-group pull-right tt-list-limit hidden-phone">
			<label for="com_rankings.ranking.limit">
				<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<! -- Display Rankings table -->
	<table class="table-hover tt-table">
		<thead>
			<tr>
				<?php if ($this->displayPosition) : ?>
					<th class="tt-col-position"><?php echo JText::_('COM_RANKINGS_EVENT_POSITION_SHORT'); ?></th>
				<?php endif; ?>
				<th class="tt-col-selector"></th>
				<th class="tt-col-rank-status"><?php echo JText::_('COM_RANKINGS_RANK'); ?></th>
				<th class="tt-col-rider-name"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
				<th class="tt-col-club-name hidden-phone"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
				<th class="tt-col-age-gender-category hidden-tablet hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDER_AGE_GENDER_CATEGORY'); ?></th>
				<th class="tt-col-score"><?php echo JText::_('COM_RANKINGS_RIDER_SCORE'); ?></th>
			</tr>
		</thead>
		<tbody>
			<! -- Display Ranking row -->
			<?php foreach ($this->rankings as $i => $ranking) : ?>
				<?php $this->ranking 	= $ranking; ?>
				<?php $this->rowNumber  = $i + 1; ?>
				<?php echo $this->loadTemplate('ranking'); ?>
				<tr id="tt-rankings-<?php echo $i + 1; ?>-rides" style="display:none">
					<td colspan="<?php echo $this->columnCount; ?>">
						<div class="tt-rides-box">
							<! -- Display Rides table for Ranking -->
							<table class="table-hover tt-table tt-rankings-rides">
								<thead>
									<tr>
										<th class="tt-col-event-date"><?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?></th>
										<th class="tt-col-event-name"><?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?></th>
										<th class="tt-col-event-distance hidden-phone"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?></th>
										<th class="tt-col-event-distance visible-phone"><?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE_SHORT'); ?></th>
										<th class="tt-col-ranking-ride-points hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?></th>
										<th class="tt-col-ranking-ride-points hidden-desktop hidden-tablet"><?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS_SHORT'); ?></th>
									</tr>
								</thead>
								<tbody>
									<! -- Display Ride row -->
									<?php foreach ($ranking->rides as $ride) : ?>
										<?php $this->ride = $ride; ?>
										<?php echo $this->loadTemplate('ride'); ?>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>       
	</table>
	<div class="pagination tt-pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="check_reset" id="reset_check" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
