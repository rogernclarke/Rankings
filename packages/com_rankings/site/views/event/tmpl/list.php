<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.6
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1><?php echo JText::_('COM_RANKINGS_EVENTS'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="tt-list-form">
		<div class="form-horizontal tt-list-filters">
			<fieldset class="adminform">
				<div>
					<?php
						$this->form->setValue('filter_district_code', null, $this->escape($this->state->get('filter.district_code')));
						$this->form->setValue('filter_distance', null, $this->escape($this->state->get('filter.distance')));
						$this->form->setValue('filter_year', null, $this->escape($this->state->get('filter.year')));
						$this->form->setValue('filter_course_code', null, $this->escape($this->state->get('filter.course_code')));
						$this->form->setValue('filter_event_name', null, $this->escape($this->state->get('filter.event_name')));
						echo $this->form->renderFieldset('event_list_toolbar');
					?>
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
    <table id="tt-table-events" class="table-hover tt-table">
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
			<?php for($i=0, $n = count($this->events); $i<$n; $i++) 
    		{
    			$this->_eventsListView = $this->events[$i];

    			If (($i==0) or ($i>0 && date('Y', strtotime($this->events[$i]->event_date)) != date('Y', strtotime($this->events[$i-1]->event_date))))
                { ?>
                    <tr class="tt-table-year-row">
                        <td colspan="4"><?php echo date('Y', strtotime($this->_eventsListView->event_date)); ?></td>
                    </tr>
                <?php 
                } ?>

    			<tr class="row<?php echo $i % 2; ?>">
    				<td class="tt-col-event-date"><?php echo date('d M', strtotime($this->_eventsListView->event_date)); ?></td>
    				<td class="tt-table-event-link tt-col-event-name">
    					<div class="tt-flex-container">
                            <div class="tt-event-name">
								<a href="<?php echo JRoute::_('index.php?option=com_rankings&task=event.display&cid='.$this->_eventsListView->event_id); ?>"><?php echo $this->_eventsListView->event_name; ?></a>
							</div>
							<?php if ($this->_eventsListView->results_ind)
                            { ?>
                                <div class="tt-tag-container hidden-phone">
                                    <div class="tt-tag tt-tag-very-small tt-results"><?php echo "Results"; ?></div>
                                </div>
                            <?php
                            } else if ($this->_eventsListView->startsheet_ind)
                            { ?>
                                <div class="tt-tag-container hidden-phone">
                                    <div class="tt-tag tt-tag-very-small tt-startsheet"><?php echo "Startsheet"; ?></div>
                                </div>
                            <?php
                            } ?>
						</div>
    				</td>
    				<td class="tt-col-event-course hidden-phone"><?php echo $this->_eventsListView->course_code; ?></td>
					<td class="tt-col-event-distance hidden-phone"><?php if($this->_eventsListView->duration_event_ind)
                        {
                            echo abs($this->_eventsListView->distance) . ' hours';
                        } else {
                            echo round($this->_eventsListView->distance, 1) . ' miles';
                        } ?></td>
					<td class="tt-col-event-distance visible-phone"><?php echo round($this->_eventsListView->distance, 1); ?></td>
				</tr>
    		<?php
    		} ?>
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
<?php
?>