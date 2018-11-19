<?php
/**
 * Rankings Component for Joomla 3.x
 * 
 * @version    1.0
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
class JFormFieldDistrictList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  0.1
	 */
	public $type = 'DistrictList';

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
        $query->select($db->quoteName(array('d.district_code', 'd.district_name')));
        $query->from('#__districts as d');
        $query->order('d.district_name ASC');

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
				$options[] = JHtml::_('select.option', $item->district_code, $item->district_name);
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
        $app = JFactory::getApplication();
        $params = $app->getParams('com_rankings');
        
        $db = JDatabaseDriver::getInstance($params);

        return $db;
    }
}