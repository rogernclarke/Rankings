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
	<?php if ($this->newEvent) : ?>
		<td class="tt-col-event-date" rowspan="<?php echo $this->eventAwardCount; ?>"><?php echo date('d M', strtotime($this->award->event_date)); ?></td>
		<td class="tt-table-event-link tt-col-event-name" rowspan="<?php echo $this->eventAwardCount; ?>">
			<a href="<?php echo $this->award->link; ?>"><?php echo $this->award->event_name; ?></a>
		</td>
			<td class="tt-col-ride-distance visible-desktop" rowspan="<?php echo $this->eventAwardCount; ?>"><?php if ($this->award->duration_event_ind) : ?>
				<?php echo abs($this->award->distance) . ' hours'; ?>
			<?php elseif ($this->award->distance > 0) : ?>
				<?php echo abs($this->award->distance) . ' miles'; ?>
			<?php else : ?>
				<?php echo '-'; ?>
			<?php endif; ?>
		</td>
		<td class="tt-col-ride-distance hidden-desktop" rowspan="<?php echo $this->eventAwardCount; ?>">
			<?php if ($this->award->duration_event_ind) : ?>
				<?php echo abs($this->award->distance); ?>
			<?php elseif ($this->award->distance > 0) : ?>
				<?php echo floor($this->award->distance); ?>
			<?php else : ?>
				<?php echo '-'; ?>
			<?php endif; ?>
		</td>
	<?php endif; ?>
	<td class="tt-col-award-name"><?php echo $this->award->position . ' ' . $this->award->awardName; ?></td>
	<td class="tt-col-award-result">
		<?php switch ($this->award->award_basis) {
			case "Standard": ?>
				<?php echo $this->award->vets_standard_result; ?>
				<?php break; ?>
			<?php case "Handicap": ?>
				<?php echo $this->award->handicap_result; ?>
				<?php break; ?>
			<?php default: ?>
				<?php if ($this->award->team_ind) : ?>
					<?php echo $this->award->team_result; ?>
				<?php elseif (!empty($this->award->ride_distance)) : ?>
					<?php echo $this->award->ride_distance; ?>
				<?php else : ?>
					<?php echo $this->award->ride_time; ?>
				<?php endif; ?>
		<?php } ?>
	</td>
</tr>
