<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Component
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Event View
 *
 * @since 2.0
 */
class RankingsViewEvent extends JViewLegacy
{
	/**
	 * Display the Event view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed   A string if successful, otherwise an Error object.
	 */
	public function display($tpl = null)
	{
		$this->event = $this->get('Item');

		// Prepare the data.
		// Compute the entry link url.
		foreach ($this->event->entries as $entry)
		{
			$entry->link = JRoute::_(RankingsHelperRoute::getRiderRoute($rider->rider_id));
		}
		// Compute the entry link url.
		foreach ($this->event->results as $ride)
		{
			$ride->link = JRoute::_(RankingsHelperRoute::getRiderRoute($ride->rider_id));
		}
		// Compute the award link url.
		foreach ($this->event->awards as $award)
		{
			$award->link = JRoute::_(RankingsHelperRoute::getRiderRoute($award->rider_id));
		}

		// Display
		return parent::display($tpl);
	}
}
