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
 * Route Helper for Rankings component
 *
 * @since 2.0
 */
abstract class RankingsHelperRoute
{
	/**
	 * Get the event route.
	 *
	 * @param   integer $id The id of the event.
	 *
	 * @return  string 	The event item route.
	 */
	public static function getEventRoute($id)
	{
		// Get the menu item id
		$menuItemId = self::getMenuItem('events');

		// Build the link
		$link = 'index.php?option=com_rankings&task=event.display&cid=' . $id . '&Itemid=' . $menuItemId;

		return $link;
	}

	/**
	 * Get the rider route.
	 *
	 * @param   integer $id The id of the rider.
	 *
	 * @return  string 	The rider item route.
	 */
	public static function getRiderRoute($id)
	{
		// Get the menu item id
		$menuItemId = self::getMenuItem('riders');

		// Build the link
		$link = 'index.php?option=com_rankings&&task=rider.display&cid=' . $id . '&Itemid=' . $menuItemId;

		return $link;
	}

	/**
	 * Get the menu item
	 *
	 * @param   string  $itemType The type of the menu item.
	 *
	 * @return 	integer	The id of the menu item
	 */
	public static function getMenuItem($itemType)
	{
		$itemType 	= strtolower($itemType);

		$component 	= JComponentHelper::getComponent('com_rankings');
		$menus 		= JFactory::getApplication()->getMenu('site');
		$menuItems 	= $menus->getItems('component_id', $component->id);

		foreach ($menuItems as $menuItem)
		{
			if ($menuItem->query['view'] == $itemType)
			{
				return $menuItem->id;
			}
		}
	}
}
