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

<tr class="row<?php echo $i % 2; ?>">
	<td class="tt-col-event-position">
		<div class="tt-event-position"><?php echo $this->result->position; ?></div>
		<div class="tt-event-position-variance">
			<?php if(!$this->result->blacklist_ind) : ?>
				<i class="fa fa-<?php echo $this->result->position_variance_ind; ?>"></i>
				<?php if (!$this->result->position_variance_value == 0) : ?>
					<?php echo $this->result->position_variance_value; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</td>
	<td class="tt-col-event-gender-position">
		<div class="tt-event-position">
			<?php echo $this->result->gender_position; ?>
		</div>
		<div class="tt-event-position-variance"></div>
	</td>
	<td class="tt-col-event-vets-position">
		<div class="tt-event-position"><?php echo $this->result->vets_position; ?></div>
		<div class="tt-event-position-variance"></div>
	</td>
	<td class="tt-table-rider-link tt-col-rider-name">
		<div class="tt-flex-container">
			<div class="tt-rider-name-container">
				<?php if(!$this->result->blacklist_ind && $this->result->form > 0) : ?>
					<div class = "tt-rider-form hidden-small-phone">
						<img src="/media/com_rankings/images/flame.png" alt="improving rider">
						<?php if($this->result->form == 2) : ?>
							<img src="/media/com_rankings/images/flame.png" alt="improving rider">
						<?php endif; ?>
					</div>
				<?php endif; ?>
				<div class="tt-rider-name">
					<?php if(!$this->result->blacklist_ind) : ?>
						<a href="<?php echo $this->result->link; ?>"rel="nofollow"><?php echo $this->result->name; ?></a>
					<?php else : ?>
						<?php echo $this->result->name; ?>
					<?php endif; ?>
				</div>
				<?php if ($this->result->category_on_day != '' && !$this->result->blacklist_ind) : ?>
					<div class="tt-rider-category hidden-small-phone">
						<div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->result->category_on_day, 0, 1);?>">
							<?php echo $this->result->category_on_day; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
			<?php if (count($this->result->awards) > 0) : ?>
				<div class="tt-rider-awards hidden-phone" style="min-width: <?php echo count($this->result->awards) * 20; ?>px;">
					<?php for($j = 1; $j < 4; $j++) : ?>
						<?php foreach ($this->result->awards as $award) : ?>
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
	</td>
	<td class="tt-col-club-name hidden-phone">
		<?php if (!empty($this->result->club_name)) : ?>
			<?php echo $this->result->club_name; ?>
			<?php else : ?>
				<?php echo $this->result->club_name; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-age-gender-category visible-large"><?php echo $this->result->age_gender_category; ?></td>
	<td class="tt-col-ride-predicted-result hidden-tablet hidden-phone">
		<?php if(!$this->result->blacklist_ind) : ?>
			<?php if($this->event->duration_event_ind) : ?>
				<?php echo $this->result->predicted_distance; ?>
			<?php elseif (!empty($this->result->predicted_time)) : ?>
				<?php echo $this->result->predicted_time; ?>
			<?php else : ?>
				<?php echo "-"; ?>
			<?php endif; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-result"><?php if($this->event->duration_event_ind) : ?>
		<?php echo $this->result->ride_distance; ?>
		<?php else : ?>
			<?php echo $this->result->time; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-event-ride-points">
		<?php if(!$this->result->blacklist_ind) : ?>
			<?php echo $this->result->ranking_points; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-vets-standard-time">
		<?php if($this->event->duration_event_ind) : ?>
			<?php echo $this->result->vets_standard_distance; ?>
		<?php else : ?>
			<?php echo $this->result->vets_standard_time; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-vets-standard-result"><?php echo $this->result->vets_standard_result; ?></td>
</tr>
