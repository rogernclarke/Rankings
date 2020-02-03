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
 * Rankings Component Router
 *
 * @since 2.0
 */
class RankingsRouter extends JComponentRouterView
{
	/**
	 * Constructor
	 *
	 * @param   CMSApplication  $app   The application object
	 * @param   AbstractMenu    $menu  The menu object to work with
	 *
	 * @since 2.0
	 */
	public function __construct($app = null, $menu = null)
	{
		$riders = new JComponentRouterViewconfiguration('riders');
		$this->registerView($riders);

		$rider = new JComponentRouterViewConfiguration('rider');
		$rider->setKey('cid')->setParent($riders);
		$this->registerView($rider);

		parent::__construct($app, $menu);

		$this->attachRule(new JComponentRouterRulesMenu($this));
		$this->attachRule(new JComponentRouterRulesStandard($this));
		$this->attachRule(new JComponentRouterRulesNomenu($this));
	}

	/**
	 * Method to get the segment(s) for a rider
	 *
	 * @param   string  $id     ID of the rider to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getRiderSegment($id, $query)
	{
		if (!strpos($id, ':'))
		{
			// Obtain a database connection
			$app 		= JFactory::getApplication();
			$params 	= $app->getParams('com_rankings');
			$db 		= JDatabaseDriver::getInstance($componentParams);
			$dbquery 	= $db->getQuery(true);

			// Fetch the alias
			$dbquery
				->select($db->qn('name'))
				->from($db->qn('#__rider_current'))
				->where('rider_id = ' . $db->q($id));
			$db->setQuery($dbquery);

			$id .= ':' . JFilterOutput::stringURLSafe($db->loadResult());
		}

		/*if ($this->noIDs)
		{
			list($void, $segment) = explode(':', $id, 2);
			return array($void => $segment);
		}*/
		return array((int) $id => $id);
	}

	/**
	 * Method to get the id for an article
	 *
	 * @param   string  $segment  Segment of the article to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getArticleId($segment, $query)
	{
		/*if ($this->noIDs)
		{
			$db = JFactory::getDbo();
			$dbquery = $db->getQuery(true);
			$dbquery->select($dbquery->qn('id'))
				->from($dbquery->qn('#__content'))
				->where('alias = ' . $dbquery->q($segment))
				->where('catid = ' . $dbquery->q($query['id']));
			$db->setQuery($dbquery);
			return (int) $db->loadResult();
		}*/
		return (int) $segment;
	}
}
