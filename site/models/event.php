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
     * Constructor
     **/
    public function __construct()
    {
        parent::__construct();

        // Specify filter fields for model
        $this->_filter_fields = array('event_name','district_code','distance','year');
    }

    function getItem() 
    {
        $event = parent::getItem();

        $rideModel = new RankingsModelsRide();
        $rideModel->set('_event_id', $event->event_id);
        $rideModel->set('_list_type', "event");
        $rideModel->set('_limit', 1000);
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
        if (isset($this->_id))
        {
            // Retrieve by id
            $query->where('e.event_id = ' . $this->_id);
        }
        else
        {
            // Retrieve by filters

            // Filter by search in name or by id
            $search = $this->getState($this->_context . '.event_name.filter');

            if (!empty($search))
            {
                /*if (stripos($search, 'id:') === 0)
                {
                    $query->where('e.event_id = ' . (int) substr($search, 3));
                }
            }
            else
            {*/
                $search = $this->_db->quote('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query->where('(e.event_name LIKE ' . $search . ')');
            }

            // Filter by district code
            $search = $this->getState($this->_context . '.district_code.filter');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->quote(str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                    $query->where('(e.course_code LIKE ' . $search . ')');
                }
            }

            // Filter by distance
            $search = $this->getState($this->_context . '.distance.filter');

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
            $search = $this->getState($this->_context . '.year.filter');

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