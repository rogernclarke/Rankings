<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.3
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 *
 * Public functions
 * __construct  Constructs the event model
 * getItem      Returns a specific event
 * listItems    Returns a list of events
 *
 * Protected functions
 * _buildQuery                  Builds the select query for event(s)
 * _buildSubqueryRankingEvent   Builds the subquery for determining if an event is a ranking event
 * _buildWhere                  Builds the where clause for the query for event(s)
 * _buildOrder                  Builds the order clause for the query for events
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Event Model
 */
class RankingsModelsEvent extends RankingsModelsDefault
{

    public $startsheet_ind = FALSE; // Indicates startsheet exists
    public $entries_count = 0;      // Count of entries
    public $results_count = 0;      // Count of rides (results)
    public $results_ind = FALSE;    // Indicates results exist

    /**
     * Constructor
     **/
    public function __construct()
    {   
        parent::__construct();

        // Specify filter fields for model
        $this->_filter_fields = array('event_name', 'district_code', 'course_code', 'distance', 'year');
        
        // Set id name and table name for model
        $this->_id_name    = 'event_id';
        $this->_table_name = '#__events';
    }

    /**
     * getItem
     *
     * Gets a specific event.
     *
     * @return  model The requested event
     **/
    function getItem() 
    {
        $event = parent::getItem();

        // Get entries for the event
        $rideModel    = new RankingsModelsRide();
        $rideModel->set('_event_id', $event->event_id);
        $rideModel->set('_list_type', "event_entries");
        if ($event->duration_event_ind)
        {
            $rideModel->set('_event_duration', $event->distance);
        }

        $event->entries = $rideModel->listItems(0,1000);

        // Get results (rides) for the event
        $rideModel    = new RankingsModelsRide();
        $rideModel->set('_event_id', $event->event_id);
        $rideModel->set('_list_type', "event_results");

        $event->rides = $rideModel->listItems(0,1000);

        if ($event->event_date < '2019-02-01')
        {
            $event->startsheet_ind = FALSE;
        } else {
            $event->startsheet_ind = TRUE;
        }

        if (count($event->rides) == 0)
        {
            $event->results_ind = FALSE;
        } else {
            $event->results_ind = TRUE;
        }

        // Update hits for the event
        $this->_updateHits();

        return $event;
    }

    /**
     * listItems
     *
     * Gets a list of events.
     *
     * @return  model The requested events
     **/
    public function listItems()
    {
        // If the list of events has been filtered by course code then a check has to be made that there is at least one course in the list being returned. If there are no events then the filter is removed.
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
     * _buildQuery
     *
     * Builds the query to be used by the event model
     * 
     * @return object Query object
     **/
    protected function _buildQuery()
    {
        $query = $this->_db->getQuery(TRUE);

        $query
            ->select($this->_db->qn(array('e.event_id', 'e.event_date', 'e.event_name', 'e.course_code', 'e.processed_date', 'e.duration_event_ind')))
            ->select('CASE ' . $this->_db->qn('e.distance') . 
                ' WHEN 0 THEN "Other"' . 
                ' ELSE ' . $this->_db->qn('e.distance') . 
                ' END' . 
                ' AS distance')
            ->select('IF (' . $this->_db->qn('e.event_date') . 
                ' < "2019-02-01", false, true)' .
                ' AS startsheet_ind')
            ->select('IF (' . $this->_db->qn('e.processed_date') . 
                ' > ' . $this->_db->qn('e.event_date') . ', true, false)' .
                ' AS results_ind');

        // If request is for an individual event then determine if the event is a ranking event or not
        if (isset($this->_id))
        {
            // Set ranking_event_ind to true if any ride for the event has ranking points awarded
            $query
                ->select("IF ((" . $this->_buildSubqueryRankingEvent() . " LIMIT 1), 1, 0)" . 
                    " AS ranking_event_ind");
        }

        $query
            ->from($this->_db->qn('#__events', 'e'));

        return $query;
    }
    
    /**
     * _buildSubqueryRankingEvent
     *
     * Builds the subquery used to determine if the event is a ranking event
     * 
     * @return object Subquery object
     **/
    protected function _buildSubqueryRankingEvent()
    {
        $subquery = $this->_db->getQuery(TRUE);
        
        // Select any ride for this event which has ranking points awarded
        $subquery
            ->select($this->_db->qn('r.ranking_points'))
            ->from  ($this->_db->qn('#__rides', 'r'))
            ->where ($this->_db->qn('r.event_id') . ' = ' . $this->_id)
            ->where ($this->_db->qn('r.ranking_points') . ' > 0');

        return $subquery;
    }

    /**
     * _buildWhere
     *
     * Builds the filter for the query
     * 
     * @param  object Query object
     * @return object Query object
     **/
    protected function _buildWhere($query)
    {
        if (isset($this->_id))
        {
            // Retrieve a specific event by id
            $query
                ->where($this->_db->qn('e.event_id') . ' = ' . $this->_id);
        }
        else
        {
            // Retrieve a list - apply filters if specified

            // Filter by search in event name
            $search = $this->getState('filter.event_name');

            if (!empty($search))
            {
                $search = $this->_db->q('%' . str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                $query
                    ->where($this->_db->qn('e.event_name') . ' LIKE ' . $search);
            }

            // Filter by district code
            $search = $this->getState('filter.district_code');

            if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true) . '%'));
                    $query
                        ->where($this->_db->qn('e.course_code') . ' LIKE ' . $search);
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
                    $search = $this->_db->q(str_replace($find, $replace, $this->_db->escape(trim($search), true)));
                    $query
                        ->where($this->_db->qn('e.course_code') . ' LIKE ' . $search);
                }
            }

            // Filter by distance
            $search = $this->getState('filter.distance');

            if (!empty($search))
            {
                switch ($search) 
                {
                    case 'Other':
                        $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                        $query
                            ->where($this->_db->qn('e.distance') . ' NOT IN(10, 25, 50, 100)');
                        break;

                    case '12':
                    case '24':
                        $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                        $query
                            ->where($this->_db->qn('e.distance') . ' = ' . $search)
                            ->where($this->_db->qn('e.duration_event_ind') . ' = TRUE');
                        break;

                    case '10':
                    case '25':
                    case '50':
                    case '100':
                        $search = $this->_db->q(str_replace(' ', '%', $this->_db->escape(trim($search), true)));
                        $query
                            ->where($this->_db->qn('e.distance') . ' = ' . $search);
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
                    $search = $this->_db->q(str_replace(' ', '%',   $this->_db->escape(trim($search), true)));
                    $query
                        ->where('YEAR(' . $this->_db->qn('e.event_date') . ') = ' . $search);
                }
            }
        }

        return $query;
    }

    /**
     * _buildOrder
     *
     * Builds the sort for the query (when a list is requested).
     * 
     * @param  object Query object
     * @return object Query object
     **/
    protected function _buildOrder($query)
    {
        $query
            ->order($this->_db->qn('e.event_date') . ' DESC')
            ->order($this->_db->qn('e.distance') . ' DESC')
            ->order($this->_db->qn('e.event_name') . ' ASC');

        return $query;
    }


}