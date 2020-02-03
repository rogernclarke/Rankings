<?php
/**
 * Rankings Component for Joomla 3.x
 *
 * @version    2.0
 * @package    Rankings
 * @subpackage Form
 * @copyright  Copyright (C) 2019 Spindata. All rights reserved.
 * @license    GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports a custom SQL select list from an external database
 *
 * @since 2.0
 */
class JFormFieldRiderYearList extends JFormFieldList
{
	/**
	 * Rider ID
	 *
	 * @var    string
	 * @since  2.0
	 */
	protected $riderId = null;

	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'rideryearlist';

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 */
	protected function getOptions()
	{
		// Get the application input riderId
		$jinput  = JFactory::getApplication()->input;
		$riderId = $jinput->getInt('cid');

		// Get the database object
		$db = $this->loadDb();

		// Build the query
		$query3 = $db->getQuery(true);

		$query3
			->select('DISTINCT YEAR(' . $db->qn('event_date') . ')')
			->from($db->qn('#__events', 'e'))
			->join('LEFT', $db->qn('#__rides', 'r') .
						' ON (' . $db->qn('e.event_id') . ' = ' . $db->qn('r.event_id') . ')')
			->where($db->qn('rider_id') . ' = ' . $riderId)
			->where($db->qn('r.time') . ' > "00:00:00"');

		$query1 = $db->getQuery(true);

		$query1
			->select('DISTINCT YEAR(' . $db->qn('effective_date') . ') AS year')
			->from($db->qn('#__rider_history'))
			->where($db->qn('rider_id') . ' = ' . $riderId)
			->where('YEAR(' . $db->qn('effective_date') . ') IN (' . $query3 . ')');

		$query2 = $db->getQuery(true);

		$query2
			->select('DISTINCT YEAR(' . $db->qn('effective_date') . ') AS year')
			->from($db->qn('#__hc_rider_history'))
			->where($db->qn('rider_id') . ' = ' . $riderId)
			->where('YEAR(' . $db->qn('effective_date') . ') IN (' . $query3 . ')');

		$query1
			->union($query2)
			->order('year DESC');

		$db->setQuery($query1);

		try
		{
			$items = $db->loadObjectlist();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_($e->getMessage()), 'error');
		}

		// Build the field options.
		$options = array();

		if (!empty($items))
		{
			foreach ($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->year, $item->year);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}

	/**
	 * Method to obtain the database connection
	 *
	 * @return  database object
	 */
	protected function loadDb()
	{
		$app = JFactory::getApplication();
		$params = $app->getParams('com_rankings');

		$db = JDatabaseDriver::getInstance($params);

		return $db;
	}
}
