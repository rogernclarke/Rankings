<?php
/**
 * Rankings Top Teams Module for Joomla 3.x
 * 
 * @version    1.2
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class ModRankingsTopTeamsHelper
{
    /**
     * Retrieves the top ranked teams
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getTeams($params)
    {
        // Obtain a database connection
        $options = array();
        
        $app = JFactory::getApplication();
        $component_params = $app->getParams('com_rankings');

        $db = JDatabaseDriver::getInstance($component_params);
   
        // Prepare the query
        $limit = (int) $params['teams_count'];
        $gender = $db->quote(str_replace(' ', '%', $db->escape(trim($params['gender']), TRUE)));

        $db->setQuery("call get_top_teams(" . $gender . ", 0, " . $limit . ")");

        // Load the teams
        $teams = $db->loadObjectList();

        // Return the set of teams
        return $teams;
    }
}