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
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right hidden-phone">
			<label for="com_rankings.events.limit" ><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<div id="tt-event-pages-counter" class="tt-pages-counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
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
    			$this->_eventsListView = $this->events[$i]; ?>
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