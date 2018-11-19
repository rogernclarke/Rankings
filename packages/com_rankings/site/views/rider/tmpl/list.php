<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('COM_RANKINGS_RIDERS'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
	<div class="tt-list-form">
		<div class="form-horizontal tt-list-filters">
			<fieldset class="adminform">
				<div>
					<?php
						$this->form->setValue('filter_district_code', null, $this->escape($this->state->get('filter.district_code')));
						$this->form->setValue('filter_club_name', null, $this->escape($this->state->get('filter.club_name')));
						$this->form->setValue('filter_name', null, $this->escape($this->state->get('filter.name')));
						echo $this->form->renderFieldset('rider_list_toolbar');
					?>
				</div>
			</fieldset>
			<div class="btn-group pull-right">
				<button type="submit" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button type="button" class="btn hasTooltip tt-list-filter-btn" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('jform_district_code_filter').value='All';document.getElementById('jform_club_name_filter').value='';document.getElementById('jform_name_filter').value='';this.form.submit();"><i class="icon-remove"></i></button>
			</div>
		</div>
		<div class="btn-group pull-right tt-list-limit hidden-phone">
			<label for="com_rankings.events.limit">
				<?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?>
			</label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
    <table id="tt-table-riders" class="table-hover tt-table">
		<thead>
			<tr>
    	    	<th class="tt-col-rider-name">
	                <?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?>
    	    	</th>
    	    	<th class="tt-col-club-name">
	                <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
    	    	</th>
    	    	<th class="tt-col-age-gender-category hidden-phone">
	                <?php echo JText::_('COM_RANKINGS_RIDER_CATEGORY'); ?>
    	    	</th>
        	</tr>
		</thead>
		<tbody>
			<?php for($i=0, $n = count($this->riders); $i<$n; $i++) 
    		{
    			$this->_ridersListView->rider = $this->riders[$i]; ?>
    			<tr class="row<?php echo $i % 2; ?>">
					<td class="tt-col-rider-name">
						<a href="<?php echo JRoute::_('index.php?option=com_rankings&task=rider.display&cid='.$this->_ridersListView->rider->rider_id); ?>">
						<?php echo $this->_ridersListView->rider->name; ?>
					</td>
					<td class="tt-col-club-name">
						<?php echo $this->_ridersListView->rider->club_name; ?>
					</td>
					<td class="tt-col-age-gender-category hidden-phone">
						<?php echo $this->_ridersListView->rider->age_gender_category; ?>
					</td>
				</tr>
    		<?php
    		} ?>
    	</tbody>       
    	<tfoot>
    		<tr>
    			<td colspan="3">
    				<div class="tt-table-counters">
    					<div class="tt-pages-counter pull-left">
							<?php echo $this->pagination->getPagesCounter(); ?>
						</div>
						<div class="tt-results-counter pull-right">
							<?php echo $this->pagination->getResultsCounter(); ?>
						</div>
					</div>
					<div class="pagination tt-pagination">
						<?php echo $this->pagination->getPagesLinks(); ?>
					</div>
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
<?php
?>