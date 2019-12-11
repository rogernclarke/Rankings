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
	<td class="tt-col-district-code"><?php echo $this->districtCount->district_code; ?></td>
	<td class="tt-col-district-name"><?php echo $this->districtCount->district_name; ?></td>
	<td class="tt-col-count"><?php echo $this->districtCount->total; ?></td>
</tr>
