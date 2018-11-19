<?php
/**
 * Rankings Last Updated Module for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class ModRankingsLastUpdatedHelper
{
    /**
     * Retrieves the latest ranking calculation date
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getDate($params)
    {
        // Obtain a database connection
        $app = JFactory::getApplication();
        $params = $app->getParams('com_rankings');
        
        $db = JDatabaseDriver::getInstance($params);
                
        // Get the latest events
        $query = $db->getQuery(TRUE);

        $rundate = "MAX(rh.effective_date)";
        $query->select($rundate);
        $query->from('#__rider_history as rh');

        // Prepare the query
        $db->setQuery($query);

        // Return the result
        return $db->loadresult();
    }
}