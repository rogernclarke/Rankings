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
	<?php if ($this->displayPosition) : ?>
		<td class="tt-col-position"><?php echo $this->ranking->position; ?></td>
	<?php endif; ?>
	<td class="tt-col-selector">
		<button type="button" onclick="ttToggleRides(<?php echo $this->rowNumber; ?>)" class="btn tt-btn-selector">
			<i id="tt-rankings-row-<?php echo $this->rowNumber; ?>-icon" class="fa fa-angle-right fa-lg"></i>
		</button>
	</td>
	<td class="tt-col-rank-status">
		<div class="tt-rank-status">
			<div class="tt-rank">
				<?php if ($this->state->get('filter.gender') === 'All') : ?>
					<?php echo $this->ranking->overall_rank; ?>
				<?php else : ?>
					<?php echo $this->ranking->gender_rank; ?>
				<?php endif; ?>
			</div>
			<?php if ($this->ranking->status === 'Provisional') : ?>
				<div class="tt-rider-status">
					<div class="tt-tag tt-tag-very-small hidden-phone"><?php echo JText::_('COM_RANKINGS_STATUS_PROVISIONAL'); ?></div>
					<div class="tt-tag tt-tag-very-small hidden-desktop hidden-tablet"><?php echo JText::_('COM_RANKINGS_STATUS_PROVISIONAL_SHORT'); ?></div>
				</div>
			<?php else : ?>
				<div class="tt-rank-change">
					<?php if ($this->state->get('filter.gender') === 'All') : ?>
						<i class="fa fa-<?php echo $this->ranking->change_in_overall_rank_ind; ?>"></i>
						<?php if (!$this->ranking->change_in_overall_rank_value == 0) : ?>
							<?php echo $this->ranking->change_in_overall_rank_value; ?>
						<?php endif; ?>
					<?php else : ?>
						<i class="fa fa-<?php echo $this->ranking->change_in_gender_rank_ind; ?>"></i>
						<?php if (!$this->ranking->change_in_gender_rank_value == 0) : ?>
							<?php echo $this->ranking->change_in_gender_rank_value; ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</td>
	<td class="tt-col-rider-name tt-table-rider-link">
		<div class="tt-rider-name-container">
			<div class="tt-rider-name">
				<a href="<?php echo $this->ranking->link; ?>"><?php echo $this->ranking->name; ?></a>
			</div>
			<div class="tt-rider-category hidden-small-phone">
				<div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->ranking->category, 0, 1) ;?>"><?php echo $this->ranking->category; ?></div>
			</div>
		</div>
	</td>
	<td class="tt-col-club-name hidden-phone"><?php echo $this->ranking->club_name; ?></td>
	<td class="tt-col-age-gender-category hidden-tablet hidden-phone"><?php echo $this->ranking->age_gender_category; ?></td>
	<td class="tt-col-score"><?php echo $this->ranking->score; ?></td>
</tr>
