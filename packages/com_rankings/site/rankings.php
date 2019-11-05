<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    1.8
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import sessions
//jimport( 'joomla.session.session' );
 
// Load classes
JLoader::registerPrefix('Rankings', JPATH_COMPONENT);

// Include stylesheets
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base() . 'media/com_rankings/css/rankings_1.8.css');

//Include javascript
JHtml::_('jquery.framework', false);
$document->addScript('media/com_rankings/js/rankings-script_1.7.js');

// Load plugins
//JPluginHelper::importPlugin('race');

// Set the table directory
//JTable::addIncludePath(JPATH_COMPONENT.'/tables');

// Get the input
$jinput = JFactory::getApplication()->input;

// Get the task
$taskName = $jinput->getCmd('task', '');
$taskName = JFilterInput::getInstance()->clean($taskName);

if (strpos($taskName, '.') != false)
{
	// A view/controller pair exists
	list($viewName, $controllerName) = explode('.', $taskName);

	// Define the view name
	$viewName = JFile::makeSafe($viewName);

	// Define the controller name
	$controllerName	= strtolower($controllerName);
	$controllerName = JFile::makeSafe($controllerName);
	
	// Set the view
	$jinput->set('view', $viewName);

	// Set the layout
	$jinput->set('layout', $controllerName);
}
else
{
	// Define specific controller if requested
	$controllerName = $jinput->getCmd('controller','list');
	$controllerName	= strtolower($controllerName);
	$controllerName = JFile::makeSafe($controllerName);
}

// Require the controller
require_once (JPATH_COMPONENT . '/controllers/' . $controllerName . '.php');

// Create the controller
$className  = 'RankingsControllers'.ucwords($controllerName);
$controller = new $className();

// Perform the Request task
$controller->execute();

// Redirect if set by the controller
$controller->redirect();