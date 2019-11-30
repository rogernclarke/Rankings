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

// Include stylesheets
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base() . 'media/com_rankings/css/rankings_1.4.css');

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$params['riders_count'] = $params->get('count_riders', '5');
$params['gender'] 		= $params->get('gender', 'Female');
$params['age_category'] = $params->get('age_category', 'All');

$riders = modRankingsTopRidersHelper::getRiders($params);

require JModuleHelper::getLayoutPath('mod_rankings_top_riders');
