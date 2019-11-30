<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Rankings Component Event Model
 *
 * @since 1.0
 */
class RankingsModelEvent extends RankingsModelItem
{
	/**
	 * Indicates whether female results exist
	 *
	 * @var    string
	 * @since  2.0
	 */
	public $femaleResults = false;

	/**
	 * Indicates whether male results exist
	 *
	 * @var    string
	 * @since  2.0
	 */
	public $maleResults = false;

	/**
	 * Indicates whether predicted results exist
	 *
	 * @var    string
	 * @since  2.0
	 */
	public $predictedResults = false;

	/**
	 * Indicates whether vets results exist
	 *
	 * @var    string
	 * @since  2.0
	 */
	public $vetsResults = false;

	/**
	 * getItem
	 *
	 * Gets a specific event.
	 *
	 * @param 	integer $id 	Id of requested event
	 *
	 * @return  model The requested event
	 *
	 * @since 2.0
	 */
	function getItem($id=null) 
	{
		$event = parent::getItem($id);

		// Set state for models
		$config = array();
		$state = new \Jobject;
		$state->set('list.limit', 0);
		$config['state'] = $state;
		$config['ignore_request'] = true;

		// Get entries for the event
		$config['subcontext'] 	= $this->getName() . '.entries';
		$ridesModel 			= new RankingsModelRides($config);
		$ridesModel->set('eventId', $this->id);

		$event->entries = $ridesModel->getItems();

		// Get results for the event
		$config['subcontext'] 	= $this->getName() . '.results';
		$ridesModel 			= new RankingsModelRides($config);
		$ridesModel->set('eventId', $this->id);

		$event->results = $ridesModel->getItems();

		// Get awards for the event
		$config['subcontext'] 	= $this->getName();
		$awardsModel 			= new RankingsModelAwards($config, 'event');
		$awardsModel->set('eventId', $this->id);

		$event->awards = $awardsModel->getItems();

		foreach ($event->entries as $entry)
		{
			if (isset($entry->predicted_time))
			{
				$event->predictedResults = true;
			}
		}

		$rideCount = 0;

		foreach ($event->results as $ride)
		{
			if ($ride->gender === "Female")
			{
				$event->femaleResults = true;
			}
			else
			{
				$event->maleResults = true;
			}

			if (isset($ride->vets_standard_result))
			{
				$event->vetsResults = true;
			}

			// Assign awards to rides
			$awardCount = 0;

			foreach ($event->awards as $award)
			{
				if ($ride->rider_id === $award->rider_id)
				{
					$event->results[$rideCount]->awards[$awardCount] = $award;
					$awardCount++;
				}
			}

			$rideCount++;
		}

		return $event;
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the event model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 **/
	protected function getQuerySelect($db, $query)
	{
		$query
			->select($this->getState('list.select', 'e.*'))

			// Set ranking_event_ind to true if any ride for the event has ranking points awarded
			->select('IF ((' . $this->getSubqueryRankingEvent() . '), 1, 0)' .
				' AS ranking_event_ind'
			);

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the event model
	 *
	 * @param 	object 	$db 		Database object
	 * @param 	object 	$query 		Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryFrom($db, $query)
	{
		$query
			->from($db->qn('#__events', 'e'));

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the event model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryFilters($db, $query)
	{
		// Retrieve a specific event by id
		$query
			->where($db->qn('e.event_id') . ' = ' . $this->id);

		return $query;
	}

	/**
	 * getSubqueryRankingEvent
	 *
	 * Builds the subquery used to determine if the event is a ranking event
	 *
	 * @return object Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryRankingEvent()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Select any ride for this event which has ranking points awarded
		$query
			->select($db->qn('r.ranking_points'))
			->from($db->qn('#__rides', 'r'))
			->where($db->qn('r.event_id') . ' = ' . $db->qn('e.event_id'))
			->where($db->qn('r.ranking_points') . ' > 0')
			->setLimit(1);

		return $query;
	}

	/**
	 * hit
	 *
	 * Updates the number of hits for an item
	 *
	 * @return boolean Result of update
	 *
	 * @since 2.0
	 */
	public function hit()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Increment hits by 1
		$query
			->update($db->qn('#__events'))
			->set($db->qn('hits') . ' = ' . $db->qn('hits') . '+1')
			->where($db->qn('event_id') . ' = ' . $this->id);

		$db->setQuery($query);

		return $db->execute();
	}
}
