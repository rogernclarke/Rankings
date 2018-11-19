<?php
/**
 * TT Latest Events Module for Joomla 3.x
 * 
 * @version    0.1
 * @package    TT
 * @subpackage Modules
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class ModTTLatestEventsHelper
{
    /**
     * Retrieves the latest events
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getEvents($params)
    {
        // Obtain a database connection
        $option = array(); //prevent problems
        $option['driver']   = 'mysqli';           // Database driver name
        $option['host']     = 'localhost';        // Database host name
        $option['user']     = 'spindata_ttspdt';  // User for database authentication
        $option['password'] = 'p=WXMpzAWK[k';     // Password for database authentication
        $option['database'] = 'spindata_tttest';  // Database name
        $option['prefix']   = 'tt_';              // Database prefix (may be empty)

        $db = JDatabaseDriver::getInstance( $option );
                
        // Get the latest events
        $query = $db->getQuery(true)
            ->select($db->quoteName(array('event_date', 'event_name')))
            ->from($db->quoteName('#__events'))
            ->order('processed_date DESC')
            ->setLimit('10');

        // Prepare the query
        $db->setQuery($query);

        // Load the events
        $events = $db->loadObjectList();

        // Return the set of events
        return $events;
    }
}