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
class JFormFieldCourseList extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  0.1
	 */
	public $type = 'CourseList';

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

		$course_code = "DISTINCT e.course_code";

        $query->select($course_code);
        $query->from('#__events as e');
        $query->order('e.course_code ASC');

		// Filter course list based on other filters selected
		// Get the form data
		$formData = $this->form->getData();

		// Filter by district
		$search = $formData->get('filter_district_code');

        if (!empty($search))
        {
            if ($search != 'All')
            {
            	$search = $db->quote(str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(e.course_code LIKE ' . $search . ')');
            }
        }

		// Filter by distance
		$search = $formData->get('filter_distance');

        if (!empty($search))
        {
            switch ($search) 
                {
                    case 'Other':
                        $search = $db->quote(str_replace(' ', '%', $db->escape(trim($search), true)));
                        $query->where('(e.distance NOT IN(10, 25, 50, 100))');
                        break;

                    case '10':
                    case '25':
                    case '50':
                    case '100':
                        $search = $db->quote(str_replace(' ', '%', $db->escape(trim($search), true)));
                        $query->where('(e.distance = ' . $search . ')');
                        break;

                    case 'All':
                    default:
                        break;
                }
        }

        // Filter by year
		$search = $formData->get('filter_year');

        if (!empty($search))
        {
        	if (!empty($search))
            {
                if ($search != 'All')
                {
                    $search = $db->quote(str_replace(' ', '%', $db->escape(trim($search), true)));
                    $query->where('(YEAR(e.event_date) = ' . $search . ')');
                }
            }
        }

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
				$options[] = JHtml::_('select.option', $item->course_code, $item->course_code);
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