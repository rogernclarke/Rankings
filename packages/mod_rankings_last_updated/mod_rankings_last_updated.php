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

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$date = modRankingsLastUpdatedHelper::getDate($params);
require JModuleHelper::getLayoutPath('mod_rankings_last_updated');