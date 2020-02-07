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

<tr class="tt-rankings-<?php if ($this->ride->counting_ride_ind) { echo "counting-ride";} else { echo "non-counting-ride";} ?>">
	<td class="tt-col-event-date"><?php echo date('d M', strtotime($this->ride->event_date)); ?></td>
	<td class="tt-table-event-link tt-col-event-name">
		<div class="tt-flex-container">
			<div class="tt-event-name-container">
				<div class="tt-event-name">
					<a href="<?php echo $this->ride->link; ?>"><?php echo $this->ride->event_name; ?></a>
				</div>
				<?php if (count($this->ride->awards) > 0) : ?>
					<div class="tt-rider-awards hidden-phone" style="min-width: <?php echo count($this->ride->awards) * 20; ?>px;">
						<?php for ($j = 1; $j < 4; $j++) : ?>
							<?php foreach ($this->ride->awards as $award) : ?>
								<?php if (substr($award->position, 0, 1) == $j) : ?>
									<?php if ($j == 1) : ?>
										<i class="fas fa-trophy tt-award-position-<?php echo substr($award->position, 0, 1);?>" title="<?php echo $award->position . ' ' . $award->awardName; ?>" aria-hidden="true"></i>
									<?php else : ?>
										<i class="fas fa-award tt-award-position-<?php echo substr($award->position, 0, 1);?>" title="<?php echo $award->position . ' ' . $award->awardName; ?>" aria-hidden="true"></i>
									<?php endif; ?>
								<?php endif; ?>
							<?php endforeach; ?>
						<?php endfor; ?>
					</div>
				<?php endif; ?>
			</div>
			<?php if ($this->ride->category_after_day != '') : ?>
				<div class="tt-rider-category hidden-small-phone">
					<div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->ride->category_after_day, 0, 1);?>"><?php echo $this->ride->category_after_day; ?></div>
				</div>
			<?php endif; ?>
		</div>
	</td>
	<td class="tt-col-ride-distance visible-desktop">
		<?php if ($this->ride->duration_event_ind) : ?>
			<?php echo abs($this->ride->event_distance) . ' hours'; ?>
		<?php elseif ($this->ride->event_distance > 0) : ?>
			<?php echo abs($this->ride->event_distance) . ' miles'; ?>
		<?php else : ?>
			<?php echo '-' ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-distance hidden-desktop">
		<?php if ($this->ride->duration_event_ind) : ?>
			<?php echo abs($this->ride->event_distance); ?>
		<?php elseif ($this->ride->event_distance > 0) : ?>
			<?php echo floor($this->ride->event_distance); ?>
		<?php else : ?>
			<?php echo '-' ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-position hidden-phone"><?php echo $this->ride->position; ?></td>
	<td class="tt-col-ride-result hidden-phone">
		<?php if ($this->ride->duration_event_ind) : ?>
			<?php echo abs($this->ride->ride_distance); ?>
		<?php else : ?>
			<?php echo $this->ride->time; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-rider-ride-points"><?php echo $this->ride->ranking_points; ?><i class="fa fa-<?php echo $this->ride->improved_ride; ?>"></i></td>
</tr>
