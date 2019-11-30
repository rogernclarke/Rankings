<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<tr class="tt-rankings-<?php if ($this->ride->counting_ride_ind) : echo "counting-ride"; else : echo "non-counting-ride"; endif; ?>">
	<td class="tt-col-event-date"><?php echo date('d M', strtotime($this->ride->event_date)); ?></td>
	<td class="tt-col-event-name tt-table-event-link"><a href="<?php echo $this->ride->link; ?>"><?php echo $this->ride->event_name; ?></a></td>
	<td class="tt-col-event-distance hidden-phone">
		<?php if ($this->ride->duration_event_ind) : ?>
			<?php echo abs($this->ride->distance) . ' hours'; ?>
		<?php elseif ($this->ride->distance > 0) : ?>
			<?php echo abs($this->ride->distance) . ' miles'; ?>
		<?php else : ?>
			<?php echo '-'; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-event-distance visible-phone"><?php echo round($this->ride->distance); ?></td>
	<td class="tt-col-ranking-ride-points"><?php echo $this->ride->ranking_points; ?></td>
</tr>
