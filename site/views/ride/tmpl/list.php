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

<h2 class="page-header"><?php echo JText::_('COM_RANKINGS_RIDERS'); ?></h2>

<form action="<?php echo JRoute::_('index.php?option=com_rankings'); ?>" method="post" name="AdminForm" id="AdminForm">
    <table class="table table-tt-riders"" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr>
				<th width="5%" align="left">
	            </th>
				<th width="5%" align="left">
	                <?php echo JText::_('COM_RANK'); ?>
    	    	</th>
    	    	<th width="25%" align="left">
	                <?php echo JText::_('COM_NAME'); ?>
    	    	</th>
    	    	<th width="20%" align="left">
	                <?php echo JText::_('COM_AGE_CATEGORY'); ?>
    	    	</th>
    	    	<th width="25%" align="left">
	                <?php echo JText::_('COM_CLUB_NAME'); ?>
    	    	</th>
        		<th width="20%" align="left">
            	    <?php echo JText::_('COM_POINTS'); ?>
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
    				</td>
    				<td>
    					<?php echo $i+1; ?>
    				</td>
					<td>
						<?php echo $this->_ridersListView->rider->first_name . ' ' . $this->_ridersListView->rider->last_name; ?>
							</a>
						</span>
					</td>
					<td>
						<?php echo $this->_ridersListView->rider->age_category; ?>
					</td>
					<td>
						<?php echo $this->_ridersListView->rider->club_name; ?>
					</td>
					<td>
					</td>
				</tr>
    		<?php
    		} ?>
    	</tbody>       
    </table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<?php
?>