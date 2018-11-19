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
 * Rankings Component Event Model
 */
class RankingsModelsEvent extends RankingsModelsDefault
{
    /**
     * Constructor
     **/
    public function __construct()
    {   
        parent::__construct();

        // Specify filter fields for model
        $this->_filter_fields = array('event_name', 'district_code', 'course_code', 'distance', 'year');
        $this->_id_name = 'event_id';
        $this->_table_name = '#__events';
    }

    function getItem() 
    {
        $event = parent::getItem();

        $rideModel = new RankingsModelsRide();
        $rideModel->set('_event_id', $event->event_id);
        $rideModel->set('_list_type', "event");
        $event->rides = $rideModel->listItems(0,1000);

        // Update hits for the rider
        $this->_updateHits();

        return $event;
    }
    public function listItems()
    {
        If ($this->getState('filter.course_code') != 'All')
        {
            If ($this->getTotal() == 0)
            {
                $this->state->set('filter.course_code', 'All');
                $app = JFactory::getApplication();
                $app->setUserState($this->_context . '.filter.course_code' , 'All');
            }
        }

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

        $distance = "CASE e.distance WHEN 0 THEN 'Other' ELSE e.distance END as distance";

        $query->select($this->_db->quoteName(array('e.event_id', 'e.event_date', 'e.event_name', 'e.course_code', 'e.processed_date')));
        $query->select($distance);

        if (isset($this->_id))
        {
            $subQuery = $this->_db->getQuery(TRUE);
            $subQuery->select($this->_db->quoteName(array('r.ranking_points')));
            $subQuery->from('#__rides as r');
            $subQuery->where('r.event_id = ' . $this->_id);
            $subQuery->where('r.ranking_points > 0');

            $query->select('IF ((' . $subQuery . ' LIMIT 1), 1, 0) as ranking_event_ind');
        }
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
        if (isset($this->_id))
        {
            // Retrieve by id
            $query->where('e.event_id = ' . $this->_id);
        }
        else
        {
            // Retrieve by filters

            // Filter by search in event name
            $search = $this->getState('filter.event_name');

            if (!empty($search))
            {
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query->where('(e.event_name LIKE ' . $search . ')');
            }

            // Filter by district code
            $search = $this->getState('filter.district_code');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                    $query->where('(e.course_code LIKE ' . $search . ')');
                }
            }

            // Filter by course code
            $search = $this->getState('filter.course_code');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $find = array(' ');
                    $replace = array('%');
                    $search = $this->_db->quote(str_replace($find, $replace, $this->_db->escape(trim($search), true)));
                    $query->where('e.course_code LIKE ' . $search);
                }
            }

            // Filter by distance
            $search = $this->getState('filter.distance');

            if (!empty($search))
            {
                switch ($search) 
                {
                    case 'Other':
                        $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                        $query->where('(e.distance NOT IN(10, 25, 50, 100))');
                        break;

                    case '10':
                    case '25':
                    case '50':
                    case '100':
                        $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                        $query->where('(e.distance = ' . $search . ')');
                        break;

                    case 'All':
                    default:
                        break;
                }
            }

            // Filter by year
            $search = $this->getState('filter.year');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->quote(str_replace(' ', '%',   $this->_db->escape(trim($search), true)));
                    $query->where('(YEAR(e.event_date) = ' . $search . ')');
                }
            }
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