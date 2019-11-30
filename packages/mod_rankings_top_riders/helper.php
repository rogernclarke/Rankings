<?php
/**
 * Rankings Top Riders Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Rankings Component Top Riders Module Helper
 *
 * @since   2.0
 */
class ModRankingsTopRidersHelper
{
	/**
	 * Retrieves the top ranked riders
	 *
	 * @param   array  $params An object containing the module parameters
	 *
	 * @return 	object 	List of riders
	 *
	 * @since 2.0
	 */
	public static function getRiders($params)
	{
		// Obtain a database connection
		$app = JFactory::getApplication();
		$componentParams = $app->getParams('com_rankings');

		$db = JDatabaseDriver::getInstance($componentParams);

		// Get the latest events
		$query = $db->getQuery(true);

		$query
			->select($db->qn(array('rider_id', 'name', 'club_name', 'gender_rank')))
			->from($db->qn('#__rider_current'))
			->where('ranking_status in ("C", "F")');

		// Apply filters
		$gender = $db->q(str_replace(' ', '%', $db->escape(trim($params['gender']), true)));
		$query->where('gender = ' . $gender);

		if ($params['age_category'] !== "All")
		{
			$ageCategory = $db->q(str_replace(' ', '%', $db->escape(trim($params['age_category']), true)));
			$query->where('age_category = ' . $ageCategory);
		}

		$query->order('gender_rank ASC');

		$limit = (int) $params['riders_count'];
		$query->setLimit($limit);

		// Prepare the query
		$db->setQuery($query);

		// Load the riders
		$riders = $db->loadObjectList();

		// Prepare the data.
		// Compute the rider link url.
		foreach ($riders as $rider)
		{
			$rider->link = JRoute::_(RankingsHelperRoute::getRiderRoute($rider->rider_id));
		}

		// Return the set of riders
		return $riders;
	}
}
