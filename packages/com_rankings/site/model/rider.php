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
 * Rankings Component Rider Model
 *
 * @since 1.0
 */
class RankingsModelRider extends RankingsModelItem
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JModelList
	 * @since   2.0
	 */
	public function __construct($config = array())
	{
		// Specify filter fields for model
		/*if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'year'
			);
		}*/

		parent::__construct($config);
	}

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the rider model
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
		// Select required fields from rider_current
		$query
			->select($this->getState('list.select', 'rc.*'))
			->select('CONCAT(' . $db->qn('age_category') . ' , " ", ' . $db->qn('gender') . ')' .
				' AS age_gender_category'
			)
			->select(
				'CASE ' . $db->qn('rc.gender') .
				' WHEN "Male" THEN "mars"' .
				' WHEN "Female" THEN "venus"' .
				' ELSE ""' .
				' END' .
				' AS gender_icon'
			)
			->select(
				'CASE ' . $db->qn('rc.ranking_status') .
				' WHEN "F" THEN "Frequent"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS status'
			)
			->select(
				'CASE ' . $db->qn('rc.hc_ranking_status') .
				' WHEN "F" THEN "Frequent"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS hc_status'
			)

		// District details
			->select($db->qn('district_name'));

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the rider model
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
			->from($db->qn('#__rider_current', 'rc'))

		// Join over districts
			->join('LEFT', $db->qn('#__districts', 'd') . ' ON (' . $db->qn('rc.district_code') . ' = ' . $db->qn('d.district_code') . ')');

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the rider model
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
		// Retrieve a specific rider by id
		$query
			->where($db->qn('rider_id') . ' = ' . $this->id);

		return $query;
	}

	/**
	 * Returns the date of the most recent event result for the rider
	 *
	 * @return date Date of last event result
	 *
	 * @since 2.0
	 */
	protected function getLastEventDate()
	{
		// Create a new query object.
		$db 	= $this->getDbo();
		$query 	= $db->getQuery(true);

		$query
			->select('MAX(' . $db->qn('event_date') . ')')
			->from($db->qn('#__events', 'e'))
			->join('RIGHT', $db->qn('#__rides', 'r') . ' ON (' . $db->qn('e.event_id') . ' = ' . $db->qn('r.event_id') . ')')
			->where($db->qn('rider_id') . ' = ' . $this->id)
			->where($db->qn('r.time') . ' > "00:00:00"');

		$db->setQuery($query);

		return $db->loadResult();
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
			->update($db->qn('#__riders'))
			->set($db->qn('hits') . ' = ' . $db->qn('hits') . '+1')
			->where($db->qn('rider_id') . ' = ' . $this->id);

		$db->setQuery($query);

		return $db->execute();
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return 	void
	 *
	 * @since 2.0
	 */
	protected function populateState()
	{
		parent::populateState();

		$app 	= JFactory::getApplication('site');
		$jinput = $app->input;
		$list 	= $jinput->getVar('list');
		$year	= $jinput->getVar('year');

		// If no list parameter is included in the url then reset lists to start at zero
		if (empty($list))
		{
			$this->state->set('.ttrides.list.start', 0);
			$app->setUserState($this->context . '.ttrides.list.start', 0);
			$this->state->set('.hcrides.list.start', 0);
			$app->setUserState($this->context . '.hcrides.list.start', 0);
			$this->state->set('.awards.list.start', 0);
			$app->setUserState($this->context . '.awards.list.start', 0);
		}

		// Set filters - at present only year, if another filter added then this logic needs to change.
		/*foreach ($this->filter_fields as $filterField)
		{
			$filterKey 	= $this->context . '.filter.' . $filterField;
			$filterName = 'filter_' . $filterField;

			$this->state->set('filter.' . $filterField, $this->getUserStateFromRequest($filterKey, $filterName, date('Y',strtotime($this->getLastRunDate())), integer, true));
		}*/

		if (empty($year))
		{
			$year = date('Y',strtotime($this->getLastEventDate()));
		}

		$this->state->set('filter.year', $year);
	}
}
