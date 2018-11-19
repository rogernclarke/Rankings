<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Display Controller for Rankings component
 */
class RankingsControllersDisplay extends RankingsControllersDefault
{
    function execute()
    {
        // Get the input
        $jinput = JFactory::getApplication()->input;
        $viewName = $jinput->getWord('view', 'cpanel');
        $viewName = JFile::makeSafe($viewName);

        // Set view and layout
        $jinput->set('view', $viewName);
        $jinput->set('layout', 'display');
        //$jinput->set('edit', true);

        // Display view
        return parent::execute();
    }
}