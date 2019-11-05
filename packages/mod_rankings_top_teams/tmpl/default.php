<?php 
/**
 * Rankings Top Teams Module for Joomla 3.x
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
  	    	<th class="tt-modtt-col-rank"><?php echo JText::_('COM_RANKINGS_TEAM_POSITION'); ?></th>
        	<th class="tt-modtt-col-club-name"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
            <th class="tt-modtt-col-riders hidden-phone"><?php echo JText::_('COM_RANKINGS_RIDERS'); ?></th>
            <th class="tt-modtt-col-score"><?php echo JText::_('COM_RANKINGS_TOTAL_SCORE'); ?></th>
        </tr>
	</thead>
	<tbody>
		<?php for($i=0, $n = count($teams); $i<$n; $i++) 
    	{ ?>
    		<tr class="row<?php echo $i % 2; ?>">
				<td class="tt-modtt-col-rank"><?php echo $teams[$i]->position; ?></td>
				<td class="tt-modtt-col-club-name"><?php echo $teams[$i]->club_name; ?></td>
                <td class="tt-modtt-col-riders tt-table-rider-link hidden-phone"><a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $teams[$i]->rider_id_1); ?>"rel="nofollow"><?php echo $teams[$i]->rider_name_1; ?></a>, <a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $teams[$i]->rider_id_2); ?>"rel="nofollow"><?php echo $teams[$i]->rider_name_2; ?></a>, <a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $teams[$i]->rider_id_3); ?>"rel="nofollow"><?php echo $teams[$i]->rider_name_3; ?></a></td>
                <td class="tt-modtt-col-score"><?php echo $teams[$i]->total_score; ?></td>
    		</tr>
    	<?php
    	} ?>
    </tbody>       
</table>