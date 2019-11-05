<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.8
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */
 
// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<tr class="tt-rankings-<?php if ($this->ride->counting_ride_ind) { echo "counting-ride";} else { echo "non-counting-ride";} ?>">
	<td class="tt-col-event-date"><?php echo date('d M', strtotime($this->ride->event_date)); ?></td>
	<td class="tt-col-event-name tt-table-event-link"><a href="<?php echo JRoute::_('index.php?Itemid=454&option=com_rankings&task=event.display&cid=' . $this->ride->event_id); ?>" rel="nofollow"><?php echo $this->ride->event_name; ?></a></td>
	<td class="tt-col-event-distance hidden-phone"><?php if($this->ride->duration_event_ind)
	    {
			echo abs($this->ride->distance) . ' hours';
		} else {
			if($this->ride->distance > 0)
			{
				echo abs($this->ride->distance) . ' miles';
			} else {
				echo '-';
			}
		} ?></td>
	<td class="tt-col-event-distance visible-phone"><?php echo round($this->ride->distance); ?></td>
	<td class="tt-col-ranking-ride-points"><?php echo $this->ride->ranking_points; ?></td>
</tr>