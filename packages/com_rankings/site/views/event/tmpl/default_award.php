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

<?php if (!$this->award->team_ind) : ?>
	<tr>
		<td class="tt-col-award-position"><?php echo $this->award->position; ?></td>
		<td class="tt-table-rider-link tt-col-award-rider-name">
			<div class="tt-rider-name-container">
				<div class="tt-rider-name">
					<?php if(!$this->award->blacklist_ind) : ?>
						<a href="<?php echo $this->award->link; ?>"rel="nofollow"><?php echo $this->award->name; ?></a>
					<?php else : ?>
						<?php echo $this->award->name; ?>
					<?php endif; ?>
				</div>
				<?php if ($this->award->category_on_day != '' && !$this->award->blacklist_ind) : ?>
					<div class="tt-rider-category hidden-small-phone">
						<div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->award->category_on_day, 0, 1) ;?>"><?php echo $this->award->category_on_day; ?></div>
					</div>
				<?php endif; ?>
			</div>
		</td>
		<td class="tt-col-award-club-name hidden-phone"><?php echo $this->award->club_name; ?></td>
		<td class="tt-col-award-result">
			<?php switch ($this->award->award_basis)
			{
				case "Standard":
					echo $this->award->vets_standard_result;
					break;
				case "Handicap":
					echo $this->award->handicap_result;
					break;
				default:
					if ($this->event->duration_event_ind)
					{
						echo $this->award->ride_distance;
					}
					else
					{
						echo $this->award->ride_time;
					}
					break;
			} ?>
		</td>
	</tr>
	<?php else : ?>
	<tr>
		<?php if ($this->award->team_counter == 1) : ?>
			<td class="tt-col-award-position" rowspan="3"><?php echo $this->award->position; ?></td>
			<td class="tt-col-award-club-name" rowspan="3"><?php echo $this->award->club_name; ?></td>
		<?php endif; ?>
			<td class="tt-table-rider-link tt-col-award-riders hidden-small-phone">
				<div class="tt-rider-name-container">
					<div class="tt-rider-name">
						<?php if (!$this->award->blacklist_ind) : ?>
							<a href="<?php echo JRoute::_('index.php?Itemid=816&option=com_rankings&task=rider.display&cid=' . $this->award->rider_id); ?>"rel="nofollow"><?php echo $this->award->name; ?></a>
						<?php else : ?>
							<?php echo $this->award->name; ?>
						<?php endif; ?>
					</div>
					<?php if ($this->award->category_on_day != '' && !$this->award->blacklist_ind) : ?>
						<div class="tt-rider-category hidden-phone">
							<div class="tt-tag tt-tag-very-small tt-rider-category-<?php echo substr($this->award->category_on_day, 0, 1); ?>"><?php echo $this->award->category_on_day; ?></div>
						</div>
					<?php endif; ?>
				</div>
			</td>
			<td class="tt-col-award-individual-result hidden-phone">
				<?php if($this->event->duration_event_ind) : ?>
					<?php echo $this->award->ride_distance; ?>
				<?php else : ?>
					<?php echo $this->award->ride_time; ?>
				<?php endif; ?>
			</td>
		<?php if($this->award->team_counter == 1) : ?>
			<td class="tt-col-award-team-result" rowspan="3">
				<?php echo $this->award->team_result; ?>
			</td>
		<?php endif; ?>
	</tr>
<?php endif; ?>
