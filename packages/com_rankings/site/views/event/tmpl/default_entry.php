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
	<td class="tt-col-rider-bib"><?php echo $this->entry->bib; ?></td>
	<td class="tt-col-rider-start-time hidden-phone"><?php echo $this->entry->start_time; ?></td>
	<td class="tt-table-rider-link tt-col-rider-name">
		<div class="tt-rider-name-container">
			<?php if(!$this->entry->blacklist_ind && $this->entry->form > 0) : ?>
				<div class = "tt-rider-form">
					<img src="/media/com_rankings/images/flame.png" alt="improving rider">
					<?php if ($this->entry->form == 2) : ?>
						<img src="/media/com_rankings/images/flame.png" alt="improving rider">
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<div class="tt-rider-name">
				<?php if(!$this->entry->blacklist_ind) : ?>
					<a href="<?php echo $this->entry->link; ?>"rel="nofollow"><?php echo $this->entry->name; ?></a>
				<?php else : ?> 
					<?php echo $this->entry->name; ?>
				<?php endif; ?>
			</div>
			<?php if ($this->entry->category_on_day != '' && !$this->entry->blacklist_ind) : ?>
				<div class="tt-rider-category hidden-small-phone">
					<div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->entry->category_on_day, 0, 1);?>">
						<?php echo $this->entry->category_on_day; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</td>
	<td class="tt-col-club-name hidden-phone"><?php echo $this->entry->club_name; ?></td>
	<td class="tt-col-age-gender-category hidden-tablet hidden-phone"><?php echo $this->entry->age_gender_category; ?></td>
	<td class="tt-col-predicted-time-at-finish hidden-tablet hidden-phone">
		<?php if(!$this->entry->blacklist_ind) : ?>
			<?php echo $this->entry->predicted_time_at_finish; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-predicted-position">
		<?php if(!$this->entry->blacklist_ind) : ?>
			<?php if (!empty($this->entry->predicted_position)) : ?>
				<?php echo trim($this->entry->predicted_position); ?>
			<?php else : ?>
				<?php echo "-"; ?>
			<?php endif; ?>
		<?php endif; ?>
	</td>
	<td class="tt-col-ride-result">
		<?php if(!$this->entry->blacklist_ind) : ?>
			<?php if($this->event->duration_event_ind) : ?>
				<?php echo $this->entry->predicted_distance; ?>
			<?php elseif (!empty($this->entry->predicted_time)) : ?>
				<?php echo $this->entry->predicted_time; ?>
			<?php else : ?>
				<?php echo "-"; ?>
			<?php endif; ?>
		<?php endif; ?>
	</td>
</tr>
