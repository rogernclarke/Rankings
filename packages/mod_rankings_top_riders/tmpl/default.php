<?php 
/**
 * Rankings Top Riders Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<table class="table-hover tt-table">
	<thead>
		<tr>
			<th class="tt-modtr-col-rank"><?php echo JText::_('COM_RANKINGS_RIDER_RANK'); ?></th>
			<th class="tt-modtr-col-rider-name"><?php echo JText::_('COM_RANKINGS_RIDER_NAME'); ?></th>
			<th class="tt-modtr-col-club-name"><?php echo JText::_('COM_RANKINGS_CLUB_NAME'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($riders as $rider) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="tt-modtr-col-rank"><?php echo $rider->gender_rank; ?></td>
				<td class="tt-modtr-col-rider-name tt-table-rider-link"><a href="<?php echo $rider->link; ?>"><?php echo $rider->name; ?></a></td>
				<td class="tt-modtr-col-club-name"><?php echo $rider->club_name; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>       
</table>
