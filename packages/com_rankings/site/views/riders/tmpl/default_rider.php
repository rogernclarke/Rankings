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
	<td class="tt-col-rider-name tt-table-rider-link">
		<a href="<?php echo $this->rider->link; ?>"><?php echo $this->rider->name; ?></a>
	</td>
	<td class="tt-col-club-name"><?php echo $this->rider->club_name; ?></td>
	<td class="tt-col-age-gender-category hidden-phone"><?php echo $this->rider->age_gender_category; ?></td>
</tr>
