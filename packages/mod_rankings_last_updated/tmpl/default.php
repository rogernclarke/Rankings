<?php 
/**
 * Rankings Last Updated Module for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
?>

<div id="tt-last-updated">
    <h3><?php echo JText::_('COM_RANKINGS_LAST_UPDATED'); ?></h3>
    <div id="tt-last-updated-date">
        <?php echo date('jS F Y', strtotime($date)); ?>
    </div>
</div>