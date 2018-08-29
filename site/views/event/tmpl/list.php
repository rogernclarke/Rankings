<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<h1 class="page-header"><?php echo JText::_('COM_RANKINGS_EVENTS'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="form-horizontal">
		<fieldset class="adminform">
			<div>
				<?php
					$this->form->setValue('event_district_code_filter', null, $this->escape($this->state->get('com_rankings.event.district_code.filter')));
					$this->form->setValue('event_distance_filter', null, $this->escape($this->state->get('com_rankings.event.distance.filter')));
					$this->form->setValue('event_year_filter', null, $this->escape($this->state->get('com_rankings.event.year.filter')));
					$this->form->setValue('event_event_name_filter', null, $this->escape($this->state->get('com_rankings.event.event_name.filter')));
					echo $this->form->renderFieldset('event_list_toolbar');
				?>
			</div>
		</fieldset>
	</div>
	<div class="btn-group pull-left">
		<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
		<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('event_event_name_filter').value='';this.form.submit();"><i class="icon-remove"></i></button>
	</div>
	<div class="btn-group pull-right hidden-phone">
			<label for="com_rankings.events.limit" ><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<div id="tt-event-pages-counter" class="tt-pages-counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
	<div id="tt-event-results-counter" class="tt-results-counter">
		<?php echo $this->pagination->getResultsCounter(); ?>
	</div>
    <table id="tt-events-table" class="table tt-table" width="100%">
		<thead>
			<tr>
				<th width="10%" align="left">
	                <?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?>
    	    	</th>
    	    	<th width="60%" align="left">
	                <?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?>
    	    	</th>
    	    	<th width="15%" align="left">
	                <?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?>
    	    	</th>
    	    	<th width="15%" align="left">
	                <?php echo JText::_('COM_RANKINGS_COURSE'); ?>
    	    	</th>
        	</tr>
		</thead>
		<tbody>
			<?php for($i=0, $n = count($this->events); $i<$n; $i++) 
    		{
    			$this->_eventsListView = $this->events[$i];

    			If (($i==0) or ($i>0 && date('Y', strtotime($this->events[$i]->event_date)) != date('Y', strtotime($this->events[$i-1]->event_date))))
                { ?>
                    <tr class="tt-table-year-row">
                        <th colspan="4">
                            <?php echo date('Y', strtotime($this->_eventsListView->event_date)); ?>
                        </th>
                    </tr>
                <?php 
                } ?>

    			<tr class="row<?php echo $i % 2; ?>">
    				<td>
    					<?php echo $this->_eventsListView->event_date; ?>
    				</td>
    				<td>
    					<span class="editlinktip hasTip" title="<?php echo JText::_('COM_RANKINGS_EVENT_DISPLAY'); ?>">
							<a href="<?php echo JRoute::_('index.php?option=com_rankings&task=event.display&cid='.$this->_eventsListView->event_id); ?>"><?php echo $this->_eventsListView->event_name; ?>
							</a>
						</span>
    				</td>
					<td>
						<?php echo $this->_eventsListView->distance; ?>
					</td>
					<td>
						<?php echo $this->_eventsListView->course_code; ?>
					</td>
				</tr>
    		<?php
    		} ?>
    	</tbody>
    </table>
    <div class="pagination tt-pagination">
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php
?>