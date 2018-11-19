<?php
/**
 * Rankings Top Riders Module for Joomla 3.x
 * 
 * @version    1.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class ModRankingsTopRidersHelper
{
    /**
     * Retrieves the top ranked rider
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getRiders($params)
    {
        // Obtain a database connection
        $app = JFactory::getApplication();
        $component_params = $app->getParams('com_rankings');
        
        $db = JDatabaseDriver::getInstance($component_params);
                
        // Get the latest events
        $query = $db->getQuery(TRUE);

        $name = "CONCAT(rr.first_name, ' ', rr.last_name) as name";

        $query->select($db->quoteName(array('rr.rider_id', 'rr.club_name')));
        $query->select($name);
        $query->from('#__riders as rr');

        $query->select($db->quoteName(array('rh.gender_rank')));
        $query->leftjoin('#__rider_history as rh on rh.rider_id = rr.rider_id');

        // Only retrieve records for non-provisional riders for the most recent calculation date
        $subQuery = $db->getQuery(TRUE);

        $subQuery->select('max(rh2.effective_date)');
        $subQuery->from('#__rider_history as rh2');
        $subQuery->where('rh2.rider_id = rh.rider_id');

        $query->where('rh.effective_date = (' . $subQuery . ')');
        $query->where('rh.ranking_status in ("C", "F")');

        // Apply filters
        $gender = $db->quote(str_replace(' ', '%', $db->escape(trim($params['gender']), TRUE)));
        $query->where('rr.gender = ' . $gender);

        if ($params['age_category'] !== "All")
        {
            $age_category = $db->quote(str_replace(' ', '%', $db->escape(trim($params['age_category']), TRUE)));
            $query->where('rr.age_category = ' . $age_category);
        }

        $query->order('rh.gender_rank ASC');
        
        $limit = (int) $params['riders_count'];
        $query->setLimit($limit);

        // Prepare the query
        $db->setQuery($query);

        // Load the riders
        $riders = $db->loadObjectList();

        // Return the set of riders
        return $riders;
    }
}