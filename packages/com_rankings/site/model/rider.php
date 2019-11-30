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
	 * getItem
	 *
	 * Gets a specific rider.
	 *
	 * @param 	integer $id 	Id of requested rider
	 *
	 * @return  model 	The requested rider
	 *
	 * @since 2.0
	 */
	public function getItem($id = null)
	{
		$rider = parent::getItem($id);

		// Set state for rides models
		$config = array();
		$state 	= new \Jobject;
		$state->set('list.limit', 0);

		$config['state'] 			= $state;
		$config['ignore_request'] 	= true;

		// Get time trial rides for the rider
		$config['subcontext'] 	= $this->getName() . '.tt';
		$ridesModel 			= new RankingsModelRides($config);
		$ridesModel->set('riderId', $this->id);

		$rider->ttRides = $ridesModel->getItems();

		// Get hill climb rides for the rider
		$config['subcontext'] 	= $this->getName() . '.hc';
		$ridesModel 			= new RankingsModelRides($config);
		$ridesModel->set('riderId', $this->id);

		$rider->hcRides = $ridesModel->getItems();

		// Get awards for the rider
		$config['subcontext'] 	= $this->getName();
		$awardsModel 			= new RankingsModelAwards($config);
		$awardsModel->set('riderId', $this->id);

		$rider->awards = $awardsModel->getItems();

		// Assign awards to rides
		$rideCount = 0;

		foreach ($rider->ttRides as $ride)
		{
			$awardCount = 0;

			foreach ($rider->awards as $award)
			{
				if ($ride->event_id === $award->event_id)
				{
					$rider->ttRides[$rideCount]->awards[$awardCount] = $award;
					$awardCount++;
				}
			}

			$rideCount++;
		}

		$rideCount = 0;	

		foreach ($rider->hcRides as $ride)
		{
			$awardCount = 0;

			foreach ($rider->awards as $award)
			{
				if ($ride->event_id === $award->event_id)
				{
					$rider->hcRides[$rideCount]->awards[$awardCount] = $award;
					$awardCount++;
				}
			}

			$rideCount++;
		}

		// Get rider history for the rider
		$riderhistoriesModel = new RankingsModelRiderhistories;
		$riderhistoriesModel->set('riderId', $this->id);

		$rider->riderhistories = $riderhistoriesModel->getItems();

		return $rider;
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
				' WHEN "F" THEN "Frequent rider"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS status'
			)
			->select(
				'CASE ' . $db->qn('rc.hc_ranking_status') .
				' WHEN "F" THEN "Frequent rider"' .
				' WHEN "C" THEN "Qualified"' .
				' WHEN "P" THEN "Provisional"' .
				' WHEN "L" THEN "Lapsed"' .
				' ELSE ""' .
				' END' .
				' AS hc_status'
			);

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
}
