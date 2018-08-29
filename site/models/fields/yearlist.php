<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    0.0.1
 * @package    Rankings
 * @subpackage Form
 * @copyright  Copyright (C) Spindata. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Supports a custom SQL select list from an external database
 *
 * @since  0.1
 */
class JFormFieldYearList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  0.1
	 */
	public $type = 'YearList';

	/**
	 * Method to get the custom field options.
	 * Use the query attribute to supply a query to generate the list.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   0.1
	 */
	protected function getOptions()
	{
		$options = array();

		// Get the database object
		$db = $this->_loadDb();
		
		// Build the query
		$query = $db->getQuery(TRUE);

		$year = "DISTINCT YEAR(e.event_date) as year";

		$query->select($year);
        $query->from('#__events as e');
        $query->order('year DESC');

		// Set the query and get the result list.
		$db->setQuery($query);
		
		try
		{
			$items = $db->loadObjectlist();
		}
		catch (JDatabaseExceptionExecuting $e)
		{
			JFactory::getApplication()->enqueueMessage(JText::_($e->getMessage()), 'error');
		}	

		// Build the field options.
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
	 *
	 * @since   0.1
	 */
    protected function _loadDb()
    {
        $options = array();

        // Set the database connection options
        $options['driver']   = 'mysqli';
        $options['host']     = 'localhost';
        $options['user']     = 'spindata_ttspdt';
        $options['password'] = 'p=WXMpzAWK[k';
        $options['database'] = 'spindata_tttest';
        $options['prefix']   = 'tt_';

        // Get the database object
        $db = JDatabaseDriver::getInstance($options);

        return $db;
    }
}