<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Event Model
 */
class RankingsModelsEvent extends RankingsModelsDefault
{
    /**
    * Protected fields
    **/
    protected $_event_id = null;
    
    /**
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();
    }

    function getItem() 
    {
        $event = parent::getItem();

        $rideModel = new RankingsModelsRide();
        $rideModel->set('_event_id', $event->event_id);
        $rideModel->set('_list_type', "event");
        $event->rides = $rideModel->listItems();

        return $event;
    }
    public function listItems()
    {
        $events = parent::listItems();

        return $events;
    }

    /**
     * Builds the query to be used by the rankings model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);

        $query->select($this->_db->quoteName(array('e.event_id', 'e.event_date', 'e.event_name', 'e.distance', 'e.course_code', 'e.processed_date')));
        $query->from('#__events as e');

        return $query;
    }

    /**
     * Builds the filter for the query
     * 
     * @param object Query object
     * @return object Query object
     **/
    protected function _buildWhere($query)
    {
        if(is_numeric($this->_id))
        {
            $query->where('e.event_id = ' . (int) $this->_id);
        }    
        return $query;
    }
    /**
     * Builds the sort for the query
     * 
     * @param object Query object
     * @return object Query object
     **/
    protected function _buildOrder($query)
    {
        $query->order('e.event_date DESC');
        $query->order('e.distance DESC');
        $query->order('e.event_name ASC');

        return $query;
    }
}