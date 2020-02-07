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
