<?php
/**
 * Rankings Top Riders Module for Joomla 3.x
 * 
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
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

        $query
            ->select($db->qn(array('rider_id', 'name', 'club_name', 'gender_rank', 'score')))
            ->from($db->qn('#__rider_current'))
            ->where('ranking_status in ("C", "F")');

        // Apply filters
        $gender = $db->q(str_replace(' ', '%', $db->escape(trim($params['gender']), TRUE)));
        $query->where('gender = ' . $gender);

        if ($params['age_category'] !== "All")
        {
            $age_category = $db->q(str_replace(' ', '%', $db->escape(trim($params['age_category']), TRUE)));
            $query->where('age_category = ' . $age_category);
        }

        $query->order('gender_rank ASC');
        
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