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
 * Rankings Component Riderhistories Model
 *
 * @since   2.0
 */
class RankingsModelRiderhistories extends RankingsModelList
{
	/**
	 * Rider ID
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $riderId = null;

	/**
	 * getQuerySelect
	 *
	 * Builds the query select to be used by the riderhistories model
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
		// Get all riderhistories
		$this->state->set('list.limit', 0);

		// Select required fields from rider_history
		$query
			->select($this->getState('list.select', '*'));

		return $query;
	}

	/**
	 * getQueryFrom
	 *
	 * Builds the query from to be used by the riderhistories model
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
			->from($db->qn('#__rider_history'));

		return $query;
	}

	/**
	 * getQueryFilters
	 *
	 * Builds the query filters to be used by the riderhistories model
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
		$query
			->where($db->qn('rider_id') . ' = ' . $this->riderId)
			->where($db->qn('effective_date') . ' > DATE_SUB(NOW(), INTERVAL 1 YEAR)');

		return $query;
	}

	/**
	 * getQueryOrder
	 *
	 * Builds the query ordering to be used by the riderhistories model
	 *
	 * @param 	object 	$db 	Database object
	 * @param 	object 	$query 	Query object
	 *
	 * @return 	object 	Query object
	 *
	 * @since 2.0
	 */
	protected function getQueryOrder($db, $query)
	{
		$query
			->order($db->qn('effective_date') . 'ASC');

		return $query;
	}
}
