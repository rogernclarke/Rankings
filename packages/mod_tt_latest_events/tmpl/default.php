<?php 
/**
 * TT Latest Events Module for Joomla 3.x
 * 
 * @version    0.1
 * @package    TT
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<table class="tt_table_latest">
	<thead>
		<tr>
  	    	<th width="25%">
                <?php echo JText::_("Date"); ?>
   	    	</th>
        	<th width="75%">
                <?php echo JText::_("Event"); ?>
        	</th>
        </tr>
	</thead>
	<tbody>
		<?php for($i=0, $n = count($events); $i<$n; $i++) 
    	{ ?>
    		<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo $events[$i]->event_date; ?>
				</td>
				<td>
					<?php echo $events[$i]->event_name; ?>
				</td>
    		</tr>
    	<?php
    	} ?>
    </tbody>       
</table>