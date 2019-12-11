<?php
/**
 * Rankings Last Updated Module for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Modules
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Rankings Component Last Updated Module Helper
 *
 * @since   2.0
 */
class ModRankingsLastUpdatedHelper
{
	/**
	 * Retrieves the latest ranking calculation date
	 *
	 * @param   array  $params An object containing the module parameters
	 *
	 * @return 	string 	The last run date
	 *
	 * @since 2.0
	 */
	public static function getDate($params)
	{
		// Obtain a database connection
		$app = JFactory::getApplication();
		$componentParams = $app->getParams('com_rankings');

		$db = JDatabaseDriver::getInstance($componentParams);

		// Get the latest run date
		$query = $db->getQuery(true);

		$query
			->select('MAX(rh.effective_date)')
			->from('#__rider_history as rh');

		// Prepare the query
		$db->setQuery($query);

		// Return the result
		return $db->loadresult();
	}
}
