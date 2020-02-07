<?php
/**
 * Rankings Latest Events Module for Joomla 3.x
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
$document->addStyleSheet(JUri::base() . 'media/com_rankings/css/rankings_1.8.css');

// Load component language file
$language = JFactory::getLanguage();
$language->load('com_rankings');

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

// Register the component helpers needed
JLoader::register('RankingsHelperRoute', JPATH_ROOT . '/components/com_rankings/helpers/route.php');

// Get the module instance parameters
$params['event_status'] 	= $params->get('event_status', 'All');
$params['event_type']   	= $params->get('event_type', 'All');
$params['item_count'] 		= (int) $params->get('item_count', '3');
$params['item_row_count'] 	= (int) $params->get('item_row_count', '5');

$events 		= modRankingsLatestEventsHelper::getEvents($params);
$itemRowCount 	= $params['item_row_count'];

require JModuleHelper::getLayoutPath('mod_rankings_latest_events');
