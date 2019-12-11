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
 * Rankings Component Statistics Model
 *
 * @since 2.0
 */
class RankingsModelStatistics extends RankingsModelList
{
	/**
	 * Constructor.
	 *
	 * @param   array  	$config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		// Specify filter fields for model
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'year'
			);
		}

		parent::__construct($config);

		// Store the year from the request, set to year of last run date if not set
		$this->year 		= (int) $this->getState('filter.year');

		if (empty($this->year))
		{
			$this->year = date("Y", strtotime($this->getLastRunDate()));
		}
	}

	/**
	 * getRidersByDistance
	 *
	 * Gets the riders with greatest distance raced
	 *
	 * @return  mixed  An array of riders on success, false on failure.
	 *
	 * @since 2.0
	 */
	public function getRidersByDistance()
	{
		// Get a storage key.
		$store = $this->getStoreId('getRidersByDistance');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		try
		{
			// Load the total and add the total to the internal cache.
			// Create a new query object.
			$db 	= $this->getDbo();
			$query 	= $db->getQuery(true);

			$query
				->select($db->qn(array('rc.rider_id', 'name')))
				->select($db->qn('lrby.club_name'))
				->select('(COALESCE(SUM(' . $db->qn('e.distance') . '), 0) + COALESCE(SUM(' . $db->qn('r.distance') . '), 0)) AS total')
				->from($db->qn('#__rider_current', 'rc'))

				// Join over the last ride by year
				->join('LEFT', '(' . $this->getSubqueryLastRideByYear() . ') AS lrby ON (' . $db->qn('rc.rider_id') . ' = ' . $db->qn('lrby.rider_id') . ')')

				// Join over the rides
				->join('LEFT', $db->qn('#__rides', 'r') . ' ON (' . $db->qn('rc.rider_id') . ' = ' . $db->qn('r.rider_id') . ')')

				// Join over the events
				->join('LEFT', $db->qn('#__events', 'e') . ' ON (' . $db->qn('r.event_id') . ' = ' . $db->qn('e.event_id') . ')')

				// Standard filters
				->where($db->qn('r.time') . ' > "00:00:00"')
				->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year)
				->where($db->qn('blacklist_ind') . ' = 0')

				->group($db->qn(array('rider_id', 'name', 'club_name')))
				->order($db->qn('total') . 'DESC, ' . $db->qn('name') . 'ASC')
				->setLimit(20);

			$db->setQuery($query);

			$this->cache[$store] = $this->getDbo()->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * getRidersByRideCount
	 *
	 * Gets the riders with the most rides
	 *
	 * @return  mixed  An array of riders on success, false on failure.
	 *
	 * @since 2.0
	 */
	public function getRidersByRideCount()
	{
		// Get a storage key.
		$store = $this->getStoreId('getRidersByRideCount');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		try
		{
			// Load the total and add the total to the internal cache.
			// Create a new query object.
			$db 	= $this->getDbo();
			$query 	= $db->getQuery(true);

			$query
				->select($db->qn(array('rc.rider_id', 'name')))
				->select($db->qn('lrby.club_name'))
				->select('COUNT(*) AS total')
				->from($db->qn('#__rider_current', 'rc'))

				// Join over the last ride by year
				->join('LEFT', '(' . $this->getSubqueryLastRideByYear() . ') AS lrby ON (' . $db->qn('rc.rider_id') . ' = ' . $db->qn('lrby.rider_id') . ')')

				// Join over the rides
				->join('LEFT', $db->qn('#__rides', 'r') . ' ON (' . $db->qn('rc.rider_id') . ' = ' . $db->qn('r.rider_id') . ')')

				// Join over the events
				->join('LEFT', $db->qn('#__events', 'e') . ' ON (' . $db->qn('r.event_id') . ' = ' . $db->qn('e.event_id') . ')')

				// Standard filters
				->where($db->qn('r.time') . ' > "00:00:00"')
				->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year)
				->where($db->qn('blacklist_ind') . ' = 0')

				->group($db->qn(array('rider_id', 'name', 'club_name')))
				->order($db->qn('total') . 'DESC, ' . $db->qn('name') . 'ASC')
				->setLimit(20);

			$db->setQuery($query);

			$this->cache[$store] = $this->getDbo()->loadObjectList();
		}
		catch (\RuntimeException $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return $this->cache[$store];
	}

	/**
	 * getSubqueryLastRideByYear
	 *
	 * Builds the subquery used to return details from a rider's last ride of the requested year (for e.g. year end club name)
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryLastRideByYear()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Select from rides
		$query
			->select($db->qn('rider_id'))
			// Allow for two rides on date of last ride
			->select('MAX(' . $db->qn('club_name') . ') AS club_name')
			->from($db->qn('#__rides') . ' AS r')

		// Join over the event
			->join('INNER', $db->qn('#__events', 'e') . ' ON (' . $db->qn('r.event_id') . ' = ' . $db->qn('e.event_id') . ')')

		// Filter the rides
			->where($db->qn('e.event_date') . ' = (' . $this->getSubQueryLastRideDate() . ')')

		// Group by rider_id
			->group('rider_id');

		return $query;
	}

	/**
	 * getSubqueryLastRideDate
	 *
	 * Builds the subquery used to return the date of a rider's last ride of the requested year
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getSubqueryLastRideDate()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		// Select from events
		$query
			->select('MAX(' . $db->qn('event_date') . ')')
			->from($db->qn('#__events') . ' AS e')

		// Join over the rides
			->join('INNER', $db->qn('#__rides', 'r2') . ' ON (' . $db->qn('r2.event_id') . ' = ' . $db->qn('e.event_id') . ')')

		// Filter the events
			->where('YEAR(' . $db->qn('event_date') . ') = ' . $this->year)
			->where($db->qn('r2.rider_id') . ' = ' . $db->qn('r.rider_id'));

		return $query;
	}
}
