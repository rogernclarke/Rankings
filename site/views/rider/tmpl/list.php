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

<h1 class="page-header"><?php echo JText::_('COM_RANKINGS_RIDERS'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter.search" class="element-invisible"><?php echo JText::_('COM_RANKINGS_SEARCH_IN_NAME');?>
			</label>
			<input type="text" name="filter.search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('COM_RANKINGS_SEARCH_IN_NAME'); ?>" />
		</div>
		<div class="btn-group pull-right hidden-phone">
			<label for="com_rankings.riders.limit" ><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
	</div>
	<div id="tt-rider-pages-counter" class="tt-pages-counter">
		<?php echo $this->pagination->getPagesCounter(); ?>
	</div>
    <table class="table table-tt-riders"" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
    	    	<th>
	                <?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?>
    	    	</th>
    	    	<th>
	                <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
    	    	</th>
    	    	<th>
	                <?php echo JText::_('COM_RANKINGS_RIDER_AGE_CATEGORY'); ?>
    	    	</th>
        	</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php //echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php for($i=0, $n = count($this->riders); $i<$n; $i++) 
    		{
    			$this->_ridersListView->rider = $this->riders[$i]; ?>
    			<tr class="row<?php echo $i % 2; ?>">
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_rankings&task=rider.display&cid='.$this->_ridersListView->rider->rider_id); ?>">
						<?php echo $this->_ridersListView->rider->name; ?>
					</td>
					<td>
						<?php echo $this->_ridersListView->rider->club_name; ?>
					</td>
					<td>
						<?php echo $this->_ridersListView->rider->age_category; ?>
					</td>
					<td>
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