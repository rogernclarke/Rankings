<?php
/**
 * Rankings Top Teams Module for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

// Include stylesheets
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base() . 'media/com_rankings/css/rankings.css');

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$params['teams_count'] = $params->get('count_riders', '3');
$params['gender'] = $params->get('gender', 'Female');

$teams = modRankingsTopTeamsHelper::getTeams($params);
require JModuleHelper::getLayoutPath('mod_rankings_top_teams');