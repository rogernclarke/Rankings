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
	<td class="tt-col-event-date"><?php echo date('d M', strtotime($this->event->event_date)); ?></td>
	<td class="tt-table-event-link tt-col-event-name">
		<div class="tt-flex-container">
			<div class="tt-event-name">
				<a href="<?php echo $this->event->link; ?>"><?php echo $this->event->event_name; ?></a>
			</div>
			<?php if ($this->event->results_ind) : ?>
				<div class="tt-tag-container hidden-phone">
					<div class="tt-tag tt-tag-very-small tt-results"><?php echo JText::_('COM_RANKINGS_RESULTS'); ?></div>
				</div>
			<?php elseif ($this->event->startsheet_ind) : ?>
				<div class="tt-tag-container hidden-phone">
					<div class="tt-tag tt-tag-very-small tt-startsheet"><?php echo JText::_('COM_RANKINGS_STARTSHEET'); ?></div>
				</div>
			<?php endif; ?>
		</div>
	</td>
	<td class="tt-col-event-course hidden-phone"><?php echo $this->event->course_code; ?></td>
	<td class="tt-col-event-distance hidden-phone">
		<?php if ($this->event->duration_event_ind) : ?>
			<?php echo abs($this->event->distance) . ' hours'; ?>
		<?php else : ?>
			<?php if ($this->event->distance > 0) : ?>
				<?php echo round($this->event->distance, 1) . ' miles'; ?>
			<?php else : ?>
				<?php echo '-'; ?>
			<?php endif; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-event-distance visible-phone"><?php echo round($this->event->distance, 1); ?></td>
</tr>
