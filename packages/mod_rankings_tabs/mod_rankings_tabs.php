<?php
/**
 * Rankings Tabs Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$title = $module->title;

// Get the repeatable field value and decode it
$listItems = json_decode($params->get('list_modules'), true);
$moduleIds = $listItems['module_id'];

$modules = array();

foreach ($moduleIds as $i => $moduleId)
{
	$module = JModuleHelper::getModuleById($moduleId);
	JModuleHelper::renderModule($module);

	$modules[$i] = $module;
}

require JModuleHelper::getLayoutPath('mod_rankings_tabs');
