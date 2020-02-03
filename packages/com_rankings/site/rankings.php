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

// Load classes
JLoader::registerPrefix('Rankings', JPATH_COMPONENT);

// Include stylesheets
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base() . 'media/com_rankings/css/rankings_1.8.css');

// Include javascript
JHtml::_('jquery.framework', false);
$document->addScript('media/com_rankings/js/rankings-script_1.7.js');

// Register the component helpers needed
require_once JPATH_COMPONENT . '/helpers/route.php';

//$config = array();
//$config['default_task'] = 'list';

$controller	= JControllerLegacy::getInstance('Rankings');
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
