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

<tr>
	<td class="tt-col-event-date"><?php echo date('d M', strtotime($this->entry->event_date)); ?></td>
	<td class="tt-table-event-link tt-col-event-name">
		<div class="tt-flex-container">
			<div class="tt-event-name-container">
				<div class="tt-event-name">
					<a href="<?php echo $this->entry->link; ?>"><?php echo $this->entry->event_name; ?></a>
				</div>
			</div>
		</div>
	</td>
	<td class="tt-col-ride-distance visible-desktop">
		<?php if ($this->entry->duration_event_ind) : ?>
			<?php echo abs($this->ride->event_distance) . ' hours'; ?>
		<?php elseif ($this->entry->event_distance > 0) : ?>
			<?php echo abs($this->entry->event_distance) . ' miles'; ?>
		<?php else : ?>
			<?php echo '-' ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-distance hidden-desktop">
		<?php if ($this->entry->duration_event_ind) : ?>
			<?php echo abs($this->entry->event_distance); ?>
		<?php elseif ($this->entry->event_distance > 0) : ?>
			<?php echo floor($this->entry->event_distance); ?>
		<?php else : ?>
			<?php echo '-' ?>
		<?php endif; ?>
	</td>
</tr>
