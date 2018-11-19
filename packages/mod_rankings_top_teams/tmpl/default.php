<?php 
/**
 * Rankings Top Teams Module for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<table class="tt-table">
	<thead>
		<tr>
  	    	<th class="tt-modtt-col-rank">
                <?php echo JText::_('COM_RANKINGS_TEAM_POSITION'); ?>
   	    	</th>
        	<th class="tt-modtt-col-club-name">
                <?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?>
        	</th>
            <th class="tt-modtt-col-total-score">
                <?php echo JText::_('COM_RANKINGS_TOTAL_SCORE'); ?>
            </th>
        </tr>
	</thead>
	<tbody>
		<?php for($i=0, $n = count($teams); $i<$n; $i++) 
    	{ ?>
    		<tr class="row<?php echo $i % 2; ?>">
				<td class="tt-modtt-col-rank">
					<?php echo $teams[$i]->position; ?>
				</td>
				<td class="tt-modtt-col-club-name">
                    <?php echo $teams[$i]->club_name; ?>
                            </a>
                    </span>
				</td>
                <td class="tt-modtt-col-total-score">
                    <?php echo $teams[$i]->total_score; ?>
                </td>
    		</tr>
    	<?php
    	} ?>
    </tbody>       
</table>