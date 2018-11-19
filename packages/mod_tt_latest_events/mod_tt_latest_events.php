<?php
/**
 * TT Latest Events Module for Joomla 3.x
 * 
 * @version    0.1
 * @package    TT
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$events = modTTLatestEventsHelper::getEvents($params);
require JModuleHelper::getLayoutPath('mod_tt_latest_events');