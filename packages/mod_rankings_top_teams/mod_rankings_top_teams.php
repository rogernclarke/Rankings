<?php
/**
 * Rankings Top Teams Module for Joomla 3.x
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
$document->addStyleSheet(JUri::base() . 'media/com_rankings/css/rankings_1.7.css');

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$params['teams_count'] 	= $params->get('count_teams', '5');
$params['gender'] 		= $params->get('gender', 'Female');

$teams = modRankingsTopTeamsHelper::getTeams($params);

require JModuleHelper::getLayoutPath('mod_rankings_top_teams');
