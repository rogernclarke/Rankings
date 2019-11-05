<?php 
/**
 * Rankings Top Riders Module for Joomla 3.x
 * 
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<table class="table-hover tt-table">
	<thead>
		<tr>
  	    	<th class="tt-modtr-col-rank"><?php echo JText::_('COM_RANKINGS_RIDER_RANK'); ?></th>
        	<th class="tt-modtr-col-rider-name"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
            <th class="tt-modtr-col-club-name hidden-phone"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
            <th class="tt-modtr-col-score"><?php echo JText::_('COM_RANKINGS_RIDER_SCORE'); ?></th>
        </tr>
	</thead>
	<tbody>
		<?php for($i=0, $n = count($riders); $i<$n; $i++) 
    	{ ?>
    		<tr class="row<?php echo $i % 2; ?>">
				<td class="tt-modtr-col-rank"><?php echo $riders[$i]->gender_rank; ?></td>
				<td class="tt-modtr-col-rider-name tt-table-rider-link"><a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid='.$riders[$i]->rider_id); ?>"><?php echo $riders[$i]->name; ?></a></td>
                <td class="tt-modtr-col-club-name hidden-phone"><?php echo $riders[$i]->club_name; ?></td>
                <td class="tt-modtr-col-score"><?php echo $riders[$i]->score; ?></td>
    		</tr>
    	<?php
    	} ?>
    </tbody>       
</table>