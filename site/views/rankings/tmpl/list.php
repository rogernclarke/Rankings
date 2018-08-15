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

<h1 class="tt-header"><?php echo JText::_('COM_RANKINGS_RANKINGS'); ?></h1>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right hidden-phone">
			<label for="com_rankings.rankings.limit" ><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
			<?php echo $this->pagination->getLimitBox(); ?>
		</div>
		<div id="tt-rankings-pages-counter" class="pagination tt-pages-counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</div>
	</div>
    <table class="table tt-rankings-table" width="100%">
		<thead>
			<tr>
				<th width="30px">
	            </th>
				<th>
	                <?php echo JText::_('COM_RANKINGS_RANK'); ?>
    	    	</th>
    	    	<th>
	                <?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?>
    	    	</th>
    	    	<th class="visible-desktop">
	                <?php echo JText::_('COM_RANKINGS_RIDER_AGE_CATEGORY'); ?>
    	    	</th>
    	    	<th class="hidden-phone">
	                <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
    	    	</th>
        		<th>
            	    <?php echo JText::_('COM_RANKINGS_RIDER_POINTS'); ?>
        		</th>
        	</tr>
		</thead>
		<tbody>
			<?php for($i=0, $n = count($this->rankings); $i<$n; $i++) 
    		{
    			$this->_rankingsListView->rider = $this->rankings[$i]; ?>
    			<tr id="tt-rankings-row-<?php echo $i+1; ?>" class="tt-rankings-row-<?php echo $i % 2; ?>">
    				<td>
    					<button type="button" onclick="ttToggleRides(<?php echo $i+1; ?>)" class="btn">
    						<i id="tt-rankings-row-<?php echo $i+1; ?>-icon" class="fa fa-angle-right fa-lg"></i>
    					</button>
    				</td>
    				<td>
    					<?php echo $i+1; ?>
    				</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option=com_rankings&task=rider.display&cid='.$this->_rankingsListView->rider->rider_id); ?>"><?php echo $this->_rankingsListView->rider->name; ?>
						</a>
					</td>
					<td class="visible-desktop">
						<?php echo $this->_rankingsListView->rider->age_category; ?>
					</td>
					<td class="hidden-phone">
						<?php echo $this->_rankingsListView->rider->club_name; ?>
					</td>
					<td>
						<?php echo $this->_rankingsListView->rider->ranking_points; ?>
					</td>
				</tr>
				<tr id="tt-rankings-<?php echo $i+1; ?>-rides" style="display:none">
					<td colspan="6">
						<div class="tt-rides-box">
							<table class="table tt-rankings-rides">
								<thead>
									<tr>
										<th>
											<?php echo JText::_('COM_RANKINGS_EVENT_DATE'); ?>
										</th>
										<th>
											<?php echo JText::_('COM_RANKINGS_EVENT_NAME'); ?>
										</th>
										<th>
											<?php echo JText::_('COM_RANKINGS_EVENT_DISTANCE'); ?>
										</th>
										<th>
											<?php echo JText::_('COM_RANKINGS_RIDE_RANKING_POINTS'); ?>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php for($j=0, $m = count($this->rankings[$i]->rides); $j<$m; $j++)
    								{
    									$this->_ridesListView->ride = $this->rankings[$i]->rides[$j]; ?>
										<tr id="tt-rankings-<?php echo $i+1; ?>-rides-<?php echo $j+1; ?>">
											<td>
												<?php echo $this->_ridesListView->ride->event_date; ?>
											</td>
											<td>
												<?php echo $this->_ridesListView->ride->event_name; ?>
											</td>
											<td>
												<?php echo $this->_ridesListView->ride->distance; ?>
											</td>
											<td>
												<?php echo $this->_ridesListView->ride->ranking_points; ?>
											</td>
										</tr>
									<?php
    								} ?>
								</tbody>
							</table>
						</div>
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
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php
?>